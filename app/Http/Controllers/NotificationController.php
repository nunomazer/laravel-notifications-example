<?php

namespace App\Http\Controllers;

use App\Enums\ReadStatus;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class NotificationController
 * Handles web requests related to notifications.
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
    public function index(Request $request): View
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

            return view('notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error to show notifications.');
        }
    }

}
