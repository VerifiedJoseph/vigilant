<?php

namespace Vigilant\Exception;

use Throwable;

class NotificationException extends \Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        $message = '[Notification error] ' . $message;

        parent::__construct($message, $code, $previous);
    }
}
