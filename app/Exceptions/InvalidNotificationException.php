<?php

namespace App\Exceptions;

use Exception;

class InvalidNotificationException extends Exception
{
    public function __construct(string $wrongData, int $code = 0, \Throwable $previous = null)
    {
        $message = "Invalid notification data provided: '{$wrongData}'.";
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'INVALID_NOTIFICATION_DATA',
        ], 422);
    }
}
