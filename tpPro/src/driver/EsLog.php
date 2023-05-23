<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\driver;

use think\App;
use think\log\driver\File;

class EsLog extends File
{
    //配置参数
    protected $config
        = [
            'version'        => '1.0.0',
            'realtime_write' => true,
            'tag'            => 'es', //标识，标签
        ];
    protected $app;
    protected $traceId = '';

    public function __construct(App $app, $config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        $this->app = $app;
    }

    /**
     * 保存.
     */
    public function save(array $log): bool
    {
        try {
            //处理数据
            $arrLog = [];
            foreach ($log as $level => $itemArr) {
                //需要处理成es的格式
                $content = [];
                foreach ($itemArr as $item) {
                    $tmp = $this->getExtParams();
                    if (!is_string($item)) {
                        throw new \InvalidArgumentException('msg格式异常');
                    }
                    $tmp['msg']   = $item;
                    $tmp['trace'] = '';

                    $content[] = $tmp;
                }
                $arrLog[$level] = $content;
            }
            //进行记录
            return $this->simpleSave($arrLog);
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * 获取主日志文件名.
     */
    public function getMasterLogFile(): string
    {
        $name = $this->config['tag'];
        if ($this->app->request->isCli()) {
            $name .= '_cli';
        }
        $path = $this->app->getRuntimePath();

        return $path . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . $name . '.log';
    }

    /**
     * 获取公共参数.
     */
    private function getExtParams(): array
    {
        $request = $this->app->request;
        $server  = $request->server();
        $header  = $request->header();
        $body    = $request->post();

        return [
            'server_addr'    => $server['SERVER_ADDR'] ?? '0.0.0.0',
            'remote_addr'    => $server['REMOTE_ADDR'] ?? '0.0.0.0',
            'request_method' => $request->isCli() ? 'CLI' : $request->method(),
            'request_uri'    => $request->baseUrl(),
            'request_params' => $request->query(),
            'request_header' => json_encode($header, JSON_UNESCAPED_UNICODE),
            'request_body'   => json_encode($body, JSON_UNESCAPED_UNICODE),
            'response_code'  => http_response_code(),
            'runtime'        => sprintf('%.5f', microtime(true) - $this->app->getBeginTime()),
            'user_id'        => $this->getUidBySecurityKey(),
        ];
    }

    /**
     * 获取user_id.
     */
    private function getUidBySecurityKey(): int
    {
        $uid = 0;
        try {
            $params = $this->app->request->param();
            if (isset($params['uid']) && is_numeric($params['uid'])) {
                $uid = (int) $params['uid'];
            }
        } catch (\Exception $e) {
        }

        return $uid;
    }

    /**
     * 正常记录.
     *
     * @param $arrLog
     */
    private function simpleSave($arrLog): bool
    {
        $destination = $this->getMasterLogFile();
        $path        = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);

        $content = '';
        foreach ($arrLog as $level => $arr) {
            foreach ($arr as $msg) {
                if (is_array($msg)) {
                    $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
                }
                $msg       = str_replace(["\n", "\r"], '', $msg);
                $baseParam = $this->getBaseParams();
                // 跟运维定义好的格式参数
                $content .= implode('|',
                        [$baseParam['version'], strtoupper($level), $baseParam['app_name'], $baseParam['time'], $baseParam['trace_id'], $msg]) . "\r\n";
            }
        }
        if ('' == $content) {
            return true;
        }

        return error_log($content, 3, $destination);
    }

    /**
     * 获取基础信息.
     */
    private function getBaseParams(): array
    {
        // 日志信息封装
        [$sec, $ms] = explode('.', sprintf('%.03f', microtime(true)));
        $timeLocal  = date('Y-m-d\TH:i:s.' . $ms . '+08:00', $sec);

        return [
            'version'  => $this->config['version'],
            'app_name' => $this->config['tag'],
            'time'     => $timeLocal,
            'trace_id' => $this->getTraceId(),
        ];
    }

    /**
     * 设置traceid.
     */
    private function setTraceId()
    {
        if (!$this->traceId) {
            $microTime     = strval(microtime(true));
            $traceId       = md5(uniqid($microTime, true) . mt_rand(100, 999));
            $this->traceId = substr($traceId, 0, 10);
        }
    }

    /**
     * 获取trace id.
     */
    private function getTraceId(): string
    {
        if (!$this->traceId) {
            $this->setTraceId();
        }

        return $this->traceId;
    }
}
