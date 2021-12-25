<?php


class Pipe
{
    protected $path;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            if (!posix_mkfifo($path, 0666)) {
                throw new PcntlProcessException('make pipe failed');
            }
        }
        $this->path = $path;
    }

    /**
     * 写入
     * @param  mixed  $message
     */
    public function send($message)
    {
        $file = fopen($this->path, 'w');
        fwrite($file, serialize($message));  //向管道中写标识，标识写入完毕。
        fclose($file);
    }

    /**
     * 读取
     * @return mixed
     */
    public function read()
    {
        $file = fopen($this->path, 'r');
        $msg = (fread($file, 200)).PHP_EOL;
        fclose($file);
        return unserialize($msg);
    }
}