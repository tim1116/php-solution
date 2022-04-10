<?php

class Rank
{
    const TIME_MAX = 9999999999;

    /** @var \Redis */
    protected $handler;

    /** @var string redis key */
    protected $key = "rank";

    public function __construct(\Redis $redis)
    {
        $this->handler = $redis;
    }

    /**
     * 设置key
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
    }

    /**
     * 将用户uid转换为string类型 主要用于分数相同按照id字典排序
     * @param  int  $userId
     * @return string
     */
    public function getValue(int $userId): string
    {
        return sprintf("%08d", $userId);
    }

    /**
     * 获取对应用户分分数
     * @param  int  $userId
     * @return float|false
     *
     * @link https://redis.io/commands/zscore/
     */
    public function getScore(int $userId)
    {
        $userId = $this->getValue($userId);
        return $this->handler->zScore($this->key, $userId);
    }

    /**
     * 添加用户到排行榜中
     * @param  int  $userId  用户id
     * @param  float  $score  初始化分数
     * @param  array  $option
     * @return int
     *
     * @link https://redis.io/commands/zadd/
     */
    public function add(int $userId, float $score = 1, array $option = ['NX'])
    {
        $userId = $this->getValue($userId);
        if ($option) {
            $res = $this->handler->zAdd($this->key, $score, $option, $userId);
        } else {
            $res = $this->handler->zAdd($this->key, $score, $userId);
        }
        return $res;
    }

    /**
     * 批量添加
     * @param  array  $batch
     * @param  array|string[]  $option
     * @return int
     *
     * @see add()
     * @link https://github.com/phpredis/phpredis/#zadd
     */
    public function addBatch(array $batch, array $option = ['NX'])
    {
        $insert = [];
        foreach ($batch as $value) {
            $insert[] = $value['score'];
            $insert[] = $this->getValue($value['user_id']);
        }
        if ($option) {
            $res = $this->handler->zAdd($this->key, $option, ...$insert);
        } else {
            $res = $this->handler->zAdd($this->key, [], ...$insert);
        }
        return $res;
    }

    /**
     * 增加对应用户分数
     * @param  int  $userId
     * @param  float  $score
     * @return float
     *
     * @link https://redis.io/commands/zincrby/
     */
    public function increase(int $userId, float $score = 1): float
    {
        $userId = $this->getValue($userId);
        return $this->handler->zIncrBy($this->key, $score, $userId);
    }

    /**
     * 增加对应分数 关联时间最后时间
     * @param  int  $userId
     * @param  int  $score
     * @return float
     */
    public function increaseAssociateTime(int $userId, int $score = 1): float
    {
        $scoreOld = $this->getScore($userId);
        if ($scoreOld === false) {
            $scoreOld = 0;
        }
        // 时间早的排前面
        $time = self::TIME_MAX - time();
        $time = floatval("0.".$time);
        $score = intval($scoreOld) + $score + $time;
        return $this->add($userId, $score, []);
    }

    /**
     * 获取指定用户的排名  分数相同情况下使用默认的字典排序
     * @param  int  $userId
     * @return int|false
     *
     * @link https://redis.io/commands/zrevrank
     */
    public function myRank(int $userId)
    {
        $userId = $this->getValue($userId);
        $rank = $this->handler->zRevRank($this->key, $userId);
        if ($rank === false) {
            return false;
        }
        return $rank + 1;
    }
}