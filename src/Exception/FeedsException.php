<?php

namespace Vigilant\Exception;

use Throwable;

class FeedsException extends \Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        $message = 'Feeds error: ' . $message;

        parent::__construct($message, $code, $previous);
    }
}
