<?php

namespace App\Repositories;

use App\Enums\ReadStatus;
use App\Models\Notification;
use App\Services\NotificationCacheService;
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
        // TODO: Implement listByUser() method.
    }

    /**
     * @inheritDoc
     */
    public function markAsRead(int | Notification $notification): bool
    {
        if (is_numeric($notification)) {
            $notification = Notification::findOrFail($notification);
        }

        return $notification->update(['read_at' => now()]);
    }

    /**
     * @inheritDoc
     */
    public function markAllAsRead(?int $userId): int
    {
        // TODO: Implement markAllAsRead() method.
    }

}
