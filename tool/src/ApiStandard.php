<?php


class ApiStandard implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $arr = [];

    public function __construct(int $code, string $msg = '', $data = null)
    {
        $this->arr['code']      = $code;
        $this->arr['msg']       = $msg;
        $this->arr['timestamp'] = time();
        $this->arr['data']      = $data;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->arr[] = $value;
        } else {
            $this->arr[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->arr[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->arr[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->arr[$offset]) ? $this->arr[$offset] : null;
    }

    public function getData(): array
    {
        return $this->arr;
    }

}