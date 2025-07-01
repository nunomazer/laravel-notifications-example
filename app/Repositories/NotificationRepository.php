<?php

namespace App\Repositories;

use App\Enums\ReadStatus;
use App\Models\Notification;
use App\Services\NotificationCacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements Contracts\NotificationRepositoryInterface
{    /**
     * @inheritDoc
     */
    public function create(array $data): Notification
    {
        $notification = Notification::create($data);

        return $notification;
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id, bool $fail = true): ?Notification
    {
        if ($fail) {
            return Notification::findOrFail($id);
        }
        return Notification::find($id);
    }

    /**
     * @inheritDoc
     */
    public function listByUser(?int $userId, ?ReadStatus $readStatus, ?int $perPage): LengthAwarePaginator
    {
        $query = Notification::query()
            ->forUser($userId);

        if ($readStatus === ReadStatus::READ) {
            $query->read();
        } elseif ($readStatus === ReadStatus::UNREAD) {
            $query->unread();
        }

        return $query->paginate($perPage ?? 15);
    }

    /**
     * @inheritDoc
     */
    public function markAsRead(int | Notification $notification): bool
    {
        if (is_numeric($notification)) {
            $notification = Notification::findOrFail($notification);
        }

        return $notification->markAsRead();
    }

    /**
     * @inheritDoc
     */
    public function markAllAsRead(?int $userId): int
    {
        // TODO: Implement markAllAsRead() method.
    }

    /**
     * @inheritDoc
     */
    public function latestUnread(?int $userId, int $limit = 10): Collection
    {
        return Notification::unread()
            ->forUser($userId)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function countUnread(int $userId): int
    {
        return Notification::unread()
            ->forUser($userId)
            ->count();
    }
}
