<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\traits;

trait Single
{
    private static $instance = null;

    private function __clone()
    {
    }

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public static function getInstance(...$args)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static(...$args);
        }

        return static::$instance;
    }

//    private function __wakeup()
//    {
//    }
}
