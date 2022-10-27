<?php

namespace Vigilant\Exception;

use Throwable;

class CheckException extends \Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        $message = '[Feed check error] ' . $message;

        parent::__construct($message, $code, $previous);
    }
}
