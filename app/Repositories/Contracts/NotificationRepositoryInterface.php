<?php

namespace App\Repositories\Contracts;

use App\Enums\ReadStatus;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    /**
     * Stroes a new notification in the database.
     *
     * @param array $data
     * @return Notification
     */
    public function create(array $data): Notification;

    /**
     * Retrieves a notification by its ID.
     *
     * @param int $id
     * @return Notification|null
     */
    public function findById(int $id): ?Notification;

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
     * Marks a notification as read.
     *
     * @param int $id
     * @return bool
     */
    public function markAsRead(int $id): bool;

    /**
     * Marks all notifications as read for a specific user.
     *
     * @param int|null $user
     * @return int
     */
    public function markAllAsRead(null | int $userId): int;
}
