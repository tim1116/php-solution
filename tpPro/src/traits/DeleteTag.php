<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\traits;

use phpSolution\TpPro\services\CacheService;

trait DeleteTag
{
    /**
     * tag前缀
     *
     * @var string
     */
    private static $preTeg = 'tag-';

    public static function onAfterWrite($item)
    {
        if (empty(self::$relatedTag)) {
            $tag = self::$preTeg . (new self())->getTable();
        } else {
            $tag = self::$relatedTag;
        }

        CacheService::deleteByTag($tag);
    }

    public static function onAfterDelete()
    {
        if (empty(self::$relatedTag)) {
            $tag = self::$preTeg . (new self())->getTable();
        } else {
            $tag = self::$relatedTag;
        }
        CacheService::deleteByTag($tag);
    }
}
