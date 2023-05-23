<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\exception;

use think\exception\HttpException;

class InputArgumentException extends HttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct(\ErrorCode::COM_ERR[0], $message ?: \ErrorCode::COM_ERR[1]);
    }
}
