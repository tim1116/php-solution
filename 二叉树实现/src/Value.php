<?php

interface Value
{
    /**
     * 比较 大于返回1  等于返回0 小于返回-1
     * @param Value $a
     * @return int
     */
    public function compare(self $a): int;

    public function getValue();
}