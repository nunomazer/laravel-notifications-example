<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Events\NotificationCreated;
use App\Exceptions\InvalidNotificationException;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
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
