<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Exceptions\InvalidNotificationException;
use App\Repositories\Contracts\NotificationRepositoryInterface;

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
        DB::beginTransaction();

        try {
            $data = $this->prepareNotificationData($data);

            // Cria notificação
            $notification = $this->notificationRepository->create($data);

            // Dispatch jobs assíncronos
            $this->dispatchNotificationJobs($notification);

            // Evento para broadcasting
            event(new NotificationCreated($notification));

            DB::commit();

            Log::info('Notification created successfully', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type,
            ]);

            return $notification;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create notification', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Lista notificações do usuário com filtros
     */
    public function getUserNotifications(
        int $userId,
        int $perPage = 15,
        array $filters = []
    ): LengthAwarePaginator {
        return $this->notificationRepository->getUserNotifications($userId, $perPage, $filters);
    }

    /**
     * Busca notificações não lidas para badges
     */
    public function getUserUnreadNotifications(int $userId): Collection
    {
        return $this->notificationRepository->getUserUnreadNotifications($userId);
    }

    /**
     * Marca notificação como lida com verificação de autorização
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = $this->notificationRepository->findByIdForUser($notificationId, $userId);

        if (!$notification) {
            throw new NotificationNotFoundException("Notification {$notificationId} not found for user {$userId}");
        }

        if ($notification->user_id !== $userId) {
            throw new UnauthorizedNotificationAccessException(
                "User {$userId} not authorized to access notification {$notificationId}"
            );
        }

        $result = $this->notificationRepository->markAsReadForUser($notificationId, $userId);

        if ($result) {
            Log::info('Notification marked as read', [
                'notification_id' => $notificationId,
                'user_id' => $userId,
            ]);
        }

        return $result;
    }

    /**
     * Marca todas as notificações como lidas
     */
    public function markAllAsRead(int $userId): int
    {
        $count = $this->notificationRepository->markAllAsRead($userId);

        Log::info('All notifications marked as read', [
            'user_id' => $userId,
            'count' => $count,
        ]);

        return $count;
    }

    /**
     * Conta notificações não lidas
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }

    /**
     * Estatísticas do usuário
     */
    public function getUserStats(int $userId): array
    {
        return $this->notificationRepository->getNotificationStats($userId);
    }

    /**
     * Aquece cache para usuário ativo
     */
    public function warmupUserCache(int $userId): void
    {
        $this->cacheService->warmupUserCache($userId);
    }

    /**
     * Limpeza de notificações antigas
     */
    public function cleanupOldNotifications(int $days = 90): int
    {
        $count = $this->notificationRepository->deleteOldNotifications($days);

        Log::info('Old notifications cleaned up', [
            'days' => $days,
            'count' => $count,
        ]);

        return $count;
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

    /**
     * Dispara jobs assíncronos relacionados à notificação
     */
    private function dispatchNotificationJobs(Notification $notification): void
    {
        // Job principal de processamento
        ProcessNotificationJob::dispatch($notification)
            ->onQueue('notifications')
            ->delay(now()->addSeconds(5));

        // Email para notificações urgentes
        if ($notification->isUrgent()) {
            SendNotificationEmailJob::dispatch($notification)
                ->onQueue('emails')
                ->delay(now()->addMinutes(1));
        }
    }
}
