<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedNotificationAccessException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'You do not have permission to access this notification.',
            'error' => 'UNAUTHORIZED_NOTIFICATION_ACCESS',
        ], 403);
    }
}
