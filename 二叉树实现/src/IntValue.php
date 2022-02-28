<?php


class IntValue implements Value
{
    /**
     * @var int
     */
    protected $value;

    public function __construct(int $num)
    {
        $this->value = $num;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function compare(Value $a): int
    {
        if (!$a instanceof self) {
            throw new InvalidArgumentException("类型异常");
        }

        if ($this->value > $a->getValue()) {
            return 1;
        } elseif ($this->value === $a->getValue()) {
            return 0;
        } else {
            return -1;
        }
    }

}