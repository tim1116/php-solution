<?php

class Node
{
    /**
     * @var null|Node
     */
    public $left = null;

    /**
     * @var null | Node
     */
    public $right = null;

    /**
     * 父节点
     * @var null | Node
     */
    public $parent = null;

    /**
     * 当前节点元素
     * @var Value
     */
    public $value = null;

    public function __construct(Value $value)
    {
        $this->value = $value;
    }

}