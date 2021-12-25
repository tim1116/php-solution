<?php

class PcntlProcess
{
    /**
     * 开启子进程数量
     * @var int
     */
    protected static $num = 0;

    /**
     * 默认管道文件
     * @var string
     */
    protected static $defalutPipe = '/tmp/pcntlProcess.pipe';

    /**
     * 子进程回调方法
     * @var callable
     */
    protected $callBack;

    /**
     * 创建子进程信息
     * @var array  map[int]ProcessItem
     */
    protected static $childProcessList = [];

    /**
     * 子进程执行完毕后结束掉子进程
     * @var bool
     */
    protected $exitChildProcess = true;

    public function __construct(callable $callBack)
    {
        self::checkEnv();
        $this->callBack = $callBack;
    }

    /**
     * 启动子进程 返回子进程pid
     * @return int
     */
    public function start(): int
    {
        $pid = pcntl_fork();
        if ($pid > 0) {
            self::$num++;
            $pidInfo = new ProcessItem($pid);
            self::$childProcessList[$pid] = $pidInfo;
        } elseif ($pid == 0) {
            ($this->callBack)($this);
            // 特别注意循环中要及时结束子进程
            if ($this->exitChildProcess) {
                exit;
            }
        } else {
            throw new PcntlProcessException("fork failed,could not fork");
        }
        return $pid;
    }


    /**
     * 初始化管道
     * @param  string  $pipeFile
     * @return Pipe
     */
    public static function pipe(string $pipeFile = '')
    {
        if (!$pipeFile) {
            $pipeFile = self::$defalutPipe;
        }
        return new Pipe($pipeFile);
    }


    /**
     * 创建子进程的信息
     * @return array
     */
    public static function childProcessList(): array
    {
        return self::$childProcessList;
    }

    /**
     * 回收子进程
     * @return array
     */
    public static function wait(): array
    {
        $pid = pcntl_wait($status);
        if ($pid > 0) {
            (self::$childProcessList[$pid])->destroy($status);
        }
        return [
            'pid' => $pid,
            'status' => $status,
        ];
    }

    public function setExitChildProcess(bool $bool): void
    {
        $this->exitChildProcess = $bool;
    }

    public static function checkEnv()
    {
        if (!function_exists('pcntl_fork')) {
            throw new PcntlProcessException("PCNTL extensions is not installed");
        }
    }
}