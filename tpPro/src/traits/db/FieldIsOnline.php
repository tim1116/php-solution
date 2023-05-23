<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\traits\db;

/**
 * 该字段定义数据表状态是否开启 (不是删除).
 */
trait FieldIsOnline
{
    public static $IS_ONLINE_OPEN  = 1;
    public static $IS_ONLINE_CLOSE = 0;

    public static function STATUS(): array
    {
        return [
            self::$IS_ONLINE_OPEN  => '开启',
            self::$IS_ONLINE_CLOSE => '禁用',
        ];
    }

    /**
     * 获取状态说明
     *  ->append(['is_online_show']);.
     *
     * @param mixed $value
     * @param mixed $data
     */
    public function getIsOnlineShowAttr($value, $data): string
    {
        $status = $data['is_online'] ?? null;
        if (isset(self::STATUS()[$status])) {
            return self::STATUS()[$status];
        }

        return '未知状态';
    }
}
