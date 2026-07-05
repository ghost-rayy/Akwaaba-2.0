<?php

namespace App\Support;

class FlashToast
{
    /** @var array<string, string> */
    public const SESSION_KEYS = [
        'message' => 'success',
        'error' => 'error',
        'success' => 'success',
        'profile_message' => 'success',
        'signature_message' => 'success',
        'stamp_message' => 'success',
        'posting_letter_message' => 'success',
        'password_message' => 'success',
    ];

    /**
     * @return list<array{message: string, type: string}>
     */
    public static function pending(): array
    {
        $toasts = [];

        foreach (self::SESSION_KEYS as $key => $type) {
            $message = session($key);

            if (is_string($message) && $message !== '') {
                $toasts[] = ['message' => $message, 'type' => $type];
            }
        }

        return $toasts;
    }
}
