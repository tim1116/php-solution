<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\services;

use phpSolution\TpPro\utils\CacheManager;
use think\Container;
use think\facade\Cache as CacheStatic;

class CacheService
{
    /**
     * 标签名.
     *
     * @deprecated
     *
     * @var string
     */
    protected static $tag = '';

    /**
     * 过期时间.
     *
     * @var int
     */
    protected static $expire;

    /**
     * 魔术方法.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::redisHandler()->{$name}(...$arguments);
    }

    /**
     * 魔术方法.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return self::redisHandler()->{$name}(...$arguments);
    }

    /**
     * 写入缓存.
     *
     * @param string $name   缓存名称
     * @param mixed  $value  缓存值
     * @param int    $expire 缓存时间，为0读取系统缓存时间
     */
    public static function set(string $name, $value, int $expire = null): bool
    {
        return self::handler()->set($name, $value, $expire ?? self::getExpire($expire));
    }

    /**
     * 如果不存在则写入缓存.
     *
     * @param bool $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = false, int $expire = null)
    {
        try {
            if (!self::checkUseCache()) {
                return false;
            }

            return self::handler()->remember($name, $default, $expire ?? self::getExpire($expire));
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 获取缓存 获取不到的话就从闭包函数中读取.
     *
     * todo 待补充关于缓存穿透/击穿等
     *
     * @param bool $forceNotCache 强制不适用缓存 默认false
     *
     * @return mixed
     */
    public static function getByDefault(CacheManager $manager, \Closure $callback, bool $forceNotCache = false)
    {
        if (!self::checkUseCache() || $forceNotCache) {
            return Container::getInstance()->invokeFunction($callback);
        }

        if ($manager->getTag()) {
            return CacheStatic::tag($manager->getTag())->remember($manager->getKey(), $callback, $manager->getExpire());
        }

        return CacheStatic::remember($manager->getKey(), $callback, $manager->getExpire());
    }

    /**
     * 是否使用缓存.
     */
    public static function checkUseCache(): bool
    {
        if ('true' == input('__cache')) {
            // 强制使用
            return true;
        } elseif ('false' == input('__cache')) {
            // 强制不使用
            return false;
        }

        if ('test' == config('app.app_env')) {
            return false;
        }

        return true;
    }

    /**
     * 删除缓存.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return bool
     */
    public static function delete(string $name)
    {
        return CacheStatic::delete($name);
    }

    /**
     * 删除缓冲池.
     */
    public static function deleteByTag(string $tag): bool
    {
        return CacheStatic::tag($tag)->clear();
    }

    /**
     * 缓存句柄.
     *
     * @return \think\cache\TagSet|CacheStatic
     */
    public static function handler()
    {
        return static::redisHandler();
    }

    /**
     * 清空缓存池.
     *
     * @return bool
     */
    public static function clear()
    {
        return self::handler()->clear();
    }

    /**
     * 设置标签.
     *
     * @deprecated
     */
    public static function setTag(string $tag): void
    {
        self::$tag = $tag;
    }

    /**
     * Redis缓存句柄.
     *
     * @return \think\cache\TagSet|CacheStatic
     */
    public static function redisHandler()
    {
        return CacheStatic::store('redis');
    }

    /**
     * 获取缓存过期时间.
     */
    protected static function getExpire(int $expire = null): int
    {
        if (self::$expire) {
            return (int) self::$expire;
        }
        $expire = !is_null($expire) ? $expire : 3600;
        if (!is_int($expire)) {
            $expire = (int) $expire;
        }

        return self::$expire = $expire;
    }
}
