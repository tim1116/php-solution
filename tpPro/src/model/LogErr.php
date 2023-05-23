<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\model;

use think\Model;

class LogErr extends Model
{
    protected $table = 'log_err';

    protected $autoWriteTimestamp = true;
}

/*
 需要先手动创建日志表

CREATE TABLE `log_err` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `url` varchar(355) NOT NULL DEFAULT '' COMMENT 'url',
    `content` text COMMENT '内容',
    `trace` text,
    `create_time` datetime DEFAULT NULL COMMENT '操作时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `createtime` (`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='错误日志';


 */
