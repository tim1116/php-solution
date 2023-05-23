<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\driver;

use think\facade\Log;
use think\log\driver\File;

/**
 * 日志规则
 * 1：日志 级别error(包括)以及以上的 记录到 日志表里面
 * 2：error以下的的按照File驱动来 (后期这里可以补充到es).
 *
 * Class MyLog
 */
class MyLog extends File
{
    /**
     * 严重登录.
     *
     * @var array []string
     */
    protected $highLevel = [
        \think\Log::EMERGENCY,
        \think\Log::ALERT,
        \think\Log::CRITICAL,
        \think\Log::ERROR,
    ];

    public function getHighLevel(): array
    {
        return $this->highLevel;
    }

    public function save(array $log): bool
    {
        try {
            $highLog = [];
            foreach ($log as $key => $value) {
                if (in_array($key, $this->highLevel)) {
                    // 数据表
                    $highLog[$key] = $value;
                    unset($log[$key]);
                }
            }
            parent::save($log);
            $this->dealHighLog($highLog);
        } catch (\Throwable $e) {
            Log::error(error_info($e));
        }

        return true;
    }

    protected function dealHighLog(array $highLog): void
    {
        if (empty($highLog)) {
            return;
        }
        $log    = new LogErr();
        $insert = [];
        foreach ($highLog as $key => $value) {
            foreach ($value as $content) {
                array_push($insert, [
                    'content' => sprintf('[%s] %s', $key, $content),
                    'trace'   => '自定义通道,my-log',
                ]);
            }
        }
        $log->saveAll($insert);
    }
}
