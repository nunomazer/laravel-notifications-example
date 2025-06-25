<?php

namespace App\Repositories;

use App\Enums\ReadStatus;
use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements Contracts\NotificationRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(array $data): Notification
    {
        $notification = Notification::create($data);

        // Invalidate cache
        $this->invalidateUserCache($data['user_id']);

        return $notification;
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Notification
    {
        // TODO: Implement findById() method.
    }

    /**
     * @inheritDoc
     */
    public function listByUser(?int $userId, ?ReadStatus $readStatus, ?int $perPage): LengthAwarePaginator
    {
        // TODO: Implement listByUser() method.
    }

    /**
     * @inheritDoc
     */
    public function markAsRead(int $id): bool
    {
        // TODO: Implement markAsRead() method.
    }

    /**
     * @inheritDoc
     */
    public function markAllAsRead(?int $userId): int
    {
        // TODO: Implement markAllAsRead() method.
    }

    /**
     * Invalidates the cache for a user's pagination notifications and unread count.
     *
     * @param int $userId
     * @return void
     */
    private function invalidateUserCache(int $userId): void
    {
        Cache::forget("unread_notifications_count_{$userId}");
    }
}
