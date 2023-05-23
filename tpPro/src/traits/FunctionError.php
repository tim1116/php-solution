<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\traits;

/**
 * 解决函数中返回错误类型的问题.
 */
trait FunctionError
{
    /**
     * 错误信息.
     *
     * @var string
     */
    protected $errorMsg = '';

    public function error(): string
    {
        return $this->errorMsg;
    }
}
