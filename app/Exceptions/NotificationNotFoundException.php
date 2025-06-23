<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationNotFoundException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Notification not found.',
            'error' => 'NOTIFICATION_NOT_FOUND',
        ], 404);
    }
}
