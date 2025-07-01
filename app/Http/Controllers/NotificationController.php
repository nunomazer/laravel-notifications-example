<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

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
        $notifications = $this->notificationService->latestUnreadForUser($user->id);

        return response()->json([
            'data' => NotificationResource::collection($notifications),
            'message' => 'Latest notifications retrieved.',
        ], Response::HTTP_OK);
    }
}
