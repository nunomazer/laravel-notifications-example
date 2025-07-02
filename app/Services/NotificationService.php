<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Enums\ReadStatus;
use App\Events\NotificationCreated;
use App\Exceptions\InvalidNotificationException;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private NotificationCacheService $cacheService
    ) {
    }

    /**
     * Creates a new notification with validation and sanitization.
     *
     * @param array $data
     * @return Notification
     * @throws \Exception
     */
    public function createNotification(array $data): Notification
    {
        try {
            $data = $this->prepareNotificationData($data);

            $notification = $this->notificationRepository->create($data);

            $this->cacheService->invalidateUserNotificationCache($data['user_id']);

            event(new NotificationCreated($notification));

            Log::info('Notification created successfully', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type,
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Mark a notification as read and invalidate the cache for the user.
     *
     * @param int | Notification $notification
     * @return bool
     */
    public function markAsRead(int | Notification $notification): bool
    {
        if (is_numeric($notification)) {
            $notification = $this->notificationRepository->findById($notification);
        }

        $result = $this->notificationRepository->markAsRead($notification);

        if ($result) {
            $this->cacheService->invalidateUserNotificationCache($notification->user_id);
        }

        return $result;
    }

    /**
     * Retrieves the list of latest unread notifications for a user
     *
     * @param int|User $userId
     * @param int $limit
     * @return Collection
     */
    public function latestUnreadForUser(int $userId, int $limit = 10): Collection
    {
        $cacheKey = $this->cacheService->getUserNotificationsCacheKey($userId, 1, 'latest_unread_notifications');
        return $this->cacheService->remember($userId, $cacheKey, null, function () use ($userId, $limit) {
            return $this->notificationRepository->latestUnread($userId, $limit);
        });
    }

    public function countUnreadForUser(int $userId): int
    {
        $cacheKey = $this->cacheService->getUserNotificationsCacheKey($userId, 1, 'unread_notification_count');
        return $this->cacheService->remember($userId, $cacheKey, null, function () use ($userId) {
            return $this->notificationRepository->countUnread($userId);
        });
    }

    /**
     * List all notifications for a user with pagination and filters
     *
     * @param int $userId The authenticated user ID
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param ReadStatus|null $readStatus Optional filter by read status
     * @return LengthAwarePaginator
     */
    public function listForUser(int $userId, int $page, int $perPage, ?ReadStatus $readStatus = null): LengthAwarePaginator
    {
        $filters = ['read_status' => $readStatus?->value];
        $cacheKey = $this->cacheService->getUserNotificationsCacheKey($userId, $page, $perPage, $filters);

        return $this->cacheService->remember($userId, $cacheKey, null, function () use ($userId, $readStatus, $perPage) {
            return $this->notificationRepository->listByUser($userId, $readStatus, $perPage);
        });
    }

    /**
     * Prepares notification data for creation
     *
     * @param array $data
     * @return array
     */
    private function prepareNotificationData(array $data): array
    {
        if (!isset($data['title']) || empty(trim($data['title']))) {
            throw new InvalidNotificationException('The title is required and cannot be empty.');
        }

        $data['title'] = strip_tags($data['title']);
        $data['message'] = strip_tags($data['message']);

        if (isset($data['type']) && !NotificationType::isValid($data['type'])) {
            throw new InvalidNotificationException('Wrong type: ' . $data['type']);
        }

        return $data;
    }

}
