<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\traits\db;

/**
 * 该字段定义数据表状态是否开启 (不是删除).
 */
trait FieldStatus
{
    public static $STATUS_OPEN  = 1;
    public static $STATUS_CLOSE = 0;

    public static function STATUS(): array
    {
        return [
            self::$STATUS_CLOSE => '关闭',
            self::$STATUS_OPEN  => '开启',
        ];
    }

    /**
     * 获取状态说明
     *  ->append(['status_show']);.
     *
     * @param mixed $value
     * @param mixed $data
     */
    public function getStatusShowAttr($value, $data): string
    {
        $status = $data['status'] ?? null;
        if (isset(self::STATUS()[$status])) {
            return self::STATUS()[$status];
        }

        return '未知状态';
    }
}
