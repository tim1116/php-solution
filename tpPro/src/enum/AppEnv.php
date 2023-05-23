<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\enum;

use MyCLabs\Enum\Enum;

/**
 * @method static AppEnv PROD()
 * @method static AppEnv TEST()
 * @method static AppEnv PREPARE()
 *
 * Class AppEnv
 */
final class AppEnv extends Enum
{
    private const PROD    = 'prod';    // 生产环境
    private const PREPARE = 'prepare';    // 预生产环境
    private const TEST    = 'test';    // 测试环境

    public static function transform(string $config): self
    {
        switch ($config) {
            case self::PROD()->getValue():
                return new AppEnv(self::PROD);
            case self::TEST()->getValue():
                return new AppEnv(self::TEST);
            case self::PREPARE()->getValue():
                return new AppEnv(self::PREPARE);
            default:
                // 默认生产环境
                return new AppEnv(self::PROD);
        }
    }

    /**
     * 获取当前系统的env.
     *
     * @return static
     */
    public static function env(): self
    {
        return AppEnv::transform(Config('app.app_env'));
    }
}
