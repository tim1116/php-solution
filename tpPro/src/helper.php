<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

if (!function_exists('error_info')) {
    // 获取错误格式化信息
    function error_info(Throwable $e): string
    {
        return $e->getFile() . ' Line: ' . $e->getLine() . ' --> ' . $e->getMessage() . '. exception code: ' . $e->getCode();
    }
}
