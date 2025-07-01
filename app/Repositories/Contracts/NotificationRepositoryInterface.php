<?php

namespace App\Repositories\Contracts;

use App\Enums\ReadStatus;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationCacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    /**
     * Stores a new notification in the database.
     *
     * @param array $data
     * @return Notification
     */
    public function create(array $data): Notification;

    /**
     * Retrieves a notification by its ID.
     *
     * @param int $id
     * @param bool $fail Whether to throw an exception if the notification is not found.
     * @return Notification|null
     */
    public function findById(int $id, bool $fail = true): ?Notification;

    /**
     * Lists notifications for a specific user with optional read status and pagination.
     *
     * @param int|null $user
     * @param ReadStatus|null $readStatus
     * @param int|null $perPage Defaults to 15 items per page.
     * @return LengthAwarePaginator
     */
    public function listByUser(
        null | int $userId,
        null | ReadStatus $readStatus,
        null | int $perPage
    ): LengthAwarePaginator;

    /**
     * Lists latest unread notifications for a specific user.
     *
     * @param int|User $user
     * @param int $limit
     * @return Collection
     */
    public function latestUnread(
        null | int $userId,
        int $limit = 10
    ): Collection;

    /**
     * Marks a notification as read.
     *
     * @param int | Notification $notification
     * @return bool
     */
    public function markAsRead(int | Notification $notification): bool;

    /**
     * Marks all notifications as read for a specific user.
     *
     * @param int|null $user
     * @return int
     */
    public function markAllAsRead(null | int $userId): int;

    /**
     * Counts unread notifications for a specific user.
     *
     * @param int $userId
     * @return int
     */
    public function countUnread(int $userId): int;
}
