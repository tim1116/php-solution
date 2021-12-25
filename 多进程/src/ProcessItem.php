<?php

class ProcessItem
{
    /**
     * @var int
     */
    public $pid = 0;

    /**
     * 是否存活
     * @var bool
     */
    public $isAlive = true;

    /**
     * 创建时间戳
     * @var int
     */
    public $createTime = 0;

    /**
     * 结束时间
     * @var int
     */
    public $destroyTime = 0;

    /**
     * 结束状态信息
     * @var mixed
     */
    public $destroyStatus;

    public function __construct(int $pid)
    {
        $this->pid = $pid;
        $this->createTime = time();
    }

    /**
     * 进程结束
     * @param  int  $destroyStatus
     */
    public function destroy(int $destroyStatus)
    {
        $this->isAlive = false;
        $this->destroyTime = time();
        $this->destroyStatus = $destroyStatus;
    }
}