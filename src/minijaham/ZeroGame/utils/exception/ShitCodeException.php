<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\utils\exception;

use Exception;

final class ShitCodeException extends Exception
{
    public function __construct(string $message, int $code = 6969)
    {
        parent::__construct($message, $code);
    }
}