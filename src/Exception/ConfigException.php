<?php

namespace Vigilant\Exception;

use Throwable;

class ConfigException extends \Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        $message = 'Config Error: ' . $message;

        parent::__construct($message, $code, $previous);
    }
}
