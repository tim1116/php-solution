<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\exception;

class LoopMaxException extends \RuntimeException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
