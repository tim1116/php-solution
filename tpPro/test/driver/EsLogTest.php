<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace Jianzhi\tests\log;

use Jianzhi\JzTool\log\EsLog;
use Jianzhi\tests\InteractsWithApp;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use think\log\Channel;

class EsLogTest extends TestCase
{
    use InteractsWithApp;

    /** @var EsLog|MockInterface */
    protected $esLog;

    protected function setUp()
    {
        $this->prepareApp();
        $this->esLog = new EsLog($this->app);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function testBasic()
    {
        $this->assertInstanceOf(EsLog::class, $this->esLog);
    }

    public function testGetConfig()
    {
        $config = [
            'channels' => [
                'jz-es' => [
                    'type'           => 'Jianzhi\JzTool\log\EsLog',
                    'realtime_write' => true,
                    'tag'            => 'jz_es',
                ],
            ],
        ];

        $this->config->set($config, 'log');
        // 加载es日志通道
        $esChannel = \think\facade\Log::channel('jz-es');

        return $esChannel;
    }

    /**
     * @depends testGetConfig
     */
    public function testSave(Channel $channel)
    {
        $logFile = $this->esLog->getMasterLogFile();

        is_file($logFile) && unlink($logFile);

        $channel->info('foo_info');
        $this->assertTrue(is_file($logFile));
        @unlink($logFile);
    }
}
