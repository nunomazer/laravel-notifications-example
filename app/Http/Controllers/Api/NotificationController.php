<?php

namespace App\Http\Controllers\Api;

use App\Enums\NotificationType;
use App\Enums\ReadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class NotificationController
 * Handles notification-related API requests.
 */
class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    /**
     * List all notifications for the authenticated user with pagination and filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = (int)$request->query('page', 1);
            $perPage = (int)$request->query('per_page', 15);
            $readStatus = $request->query('read_status');
            $type = $request->query('type');

            // Validate and convert read_status parameter
            $readStatusEnum = null;
            if ($readStatus && ReadStatus::isValid($readStatus)) {
                $readStatusEnum = ReadStatus::from($readStatus);
            }

            // Validate and convert type parameter
            $typeEnum = null;
            if ($type && NotificationType::isValid($type)) {
                $typeEnum = NotificationType::from($type);
            }

            // Get paginated notifications for authenticated user
            $notifications = $this->notificationService->listForUser(
                $request->user()->id,
                $page,
                $perPage,
                $readStatusEnum,
                $typeEnum
            );

            return response()->json([
                'data' => NotificationResource::collection($notifications->items()),
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                ],
                'message' => 'Notifications retrieved successfully.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving notifications.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new notification.
     */
    public function store(StoreNotificationRequest $request): JsonResponse
    {
        try {
            $notification = $this->notificationService->createNotification(
                $request->validated()
            );

            return response()->json([
                'data' => new NotificationResource($notification->load('user')),
                'message' => 'Notification created.',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating notification.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getLatestByUser(Request $request, User $user): JsonResponse
    {
        try {
            $notifications = $this->notificationService->latestUnreadForUser($user->id);

            return response()->json([
                'data' => NotificationResource::collection($notifications),
                'message' => 'Latest notifications retrieved.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error trying retrieve latest notifications.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function putMarkAsRead(Request $request, Notification $notification): JsonResponse
    {
        try {
            $this->notificationService->markAsRead($notification);

            return response()->json([
                'data' => new NotificationResource($notification->load('user')),
                'message' => 'Notification marked as read.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error marking notification as read.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
