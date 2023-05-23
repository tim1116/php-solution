<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\utils;

use think\facade\Config;

class CacheManager
{
    /**
     * 配置.
     *
     * @var array
     */
    protected static $config = [];

    /**
     * 缓存名称.
     *
     * @var string
     */
    protected $key = '';

    /**
     * 缓存tag.
     *
     * @var string
     */
    protected $tag = '';

    /**
     * 过期时间.
     *
     * @var int
     */
    protected $expire = 3600;

    public function __construct(string $key, ...$args)
    {
        $cache = self::getAllConfig();
        if (!isset($cache[$key])) {
            throw new \InvalidArgumentException(sprintf('%s 没有找到这个缓存配置', $key));
        }

        $this->expire = $cache[$key]['expire'];
        $this->key    = sprintf($key, ...$args);

        if (!empty($cache[$key]['tag'])) {
            $this->tag = $cache[$key]['tag'];
        }
    }

    /**
     * 获取缓存标签.
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * 获取key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * 获取过期时间.
     */
    public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * 动态设置过期时间.
     */
    public function setExpire(int $expire): void
    {
        $this->expire = $expire;
    }

    public static function getAllConfig(): array
    {
        if (empty(self::$config)) {
            self::$config = Config::get('cache_key');
        }

        return self::$config;
    }
}
