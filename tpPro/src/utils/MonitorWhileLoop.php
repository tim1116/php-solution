<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\utils;

use phpSolution\TpPro\exception\LoopMaxException;

/**
 * 限制while 循环的最大循环次数 避免死循环
 * Class MonitorWhileLoop.
 */
class MonitorWhileLoop
{
    /** @var int 最大循环次数 */
    protected $maxLoopNum = 10000;

    /** @var int 最大错误循环次数 */
    protected $maxErrorLoopNum = 0;
    protected $errorLoopNum    = 0;

    public function __construct(int $maxLoopNum = 0)
    {
        if ($maxLoopNum > 0) {
            $this->maxLoopNum = $maxLoopNum;
        }
    }

    public function setMaxErrorLoopNum(int $maxLoopNum): self
    {
        $this->maxErrorLoopNum = $maxLoopNum;

        return $this;
    }

    /**
     * 更新失败次数.
     */
    public function updateMaxErrorLoopNum(int $num = 1): void
    {
        $this->errorLoopNum += $num;
    }

    /**
     * 分页递增跳出循环.
     */
    public function startByPage(callable $callable, int $limit): void
    {
        $num  = 0;
        $page = 1;
        while (true) {
            ++$num;
            $res = $callable($page, $limit, $this);
            ++$page;
            if (false === $res) {
                // false 跳出循环 循环结束
                break;
            }

            if ($num >= $this->maxLoopNum) {
                throw new LoopMaxException('循环次数超过最大限制');
            }
            if ($this->maxErrorLoopNum && ($this->errorLoopNum > $this->maxErrorLoopNum)) {
                throw new LoopMaxException('错误循环次数超过最大限制');
            }
        }
    }
}
