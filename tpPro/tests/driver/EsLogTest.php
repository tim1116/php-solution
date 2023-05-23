<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\tests\driver;

use Mockery as m;
use Mockery\MockInterface;
use phpSolution\TpPro\driver\EsLog;
use phpSolution\TpPro\tests\InteractsWithApp;
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
                'es' => [
                    'type'           => 'phpSolution\TpPro\driver\EsLog',
                    'realtime_write' => true,
                    'tag'            => 'es',
                ],
            ],
        ];

        $this->config->set($config, 'log');
        // 加载es日志通道
        $esChannel = \think\facade\Log::channel('es');

        return $esChannel;
    }

    /**
     * @depends testGetConfig
     */
    public function testSave(Channel $channel)
    {
        $logFile = $this->esLog->getMasterLogFile();
        is_file($logFile) && unlink($logFile);

        var_dump($logFile);

        $channel->info('foo_info');
        $this->assertTrue(is_file($logFile));
        @unlink($logFile);
    }
}
