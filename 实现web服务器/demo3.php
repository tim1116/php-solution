<?php

class Server
{
    /**
     * @var false|resource
     */
    private $socket;

    /**
     * 是否http服务器 默认tcp服务器
     * @var bool
     */
    private $isHttp = false;

    /**
     * @var []resource
     */
    private $socketList = [];

    public function __construct(string $address)
    {
        $this->socket = @stream_socket_server($address, $errNo, $errMessage);
        if (!is_resource($this->socket)) {
            throw new InvalidArgumentException("参数异常:" . $errMessage);
        }
        // stream_set_blocking 设置非阻塞io
        stream_set_blocking($this->socket, false);
        // 添加当前监听的socket
        $this->socketList[(int)$this->socket] = $this->socket;
    }

    public function isHttp(bool $bool): self
    {
        $this->isHttp = $bool;
        return $this;
    }

    /**
     * 启动服务器
     */
    public function run()
    {
        while (true) {
            $read  = $this->socketList;
            $write = $except = [];
            //阻塞监听 设置null阻塞监听
            $count = @stream_select($read, $write, $except, null);
            if ($count) {
                foreach ($read as $k => $v) {
                    if ($v == $this->socket) {
                        // connect success
                        // accept Connection
                        @$conn = stream_socket_accept($this->socket, null, $remote_address);
                        echo "connect fd:" . $k . PHP_EOL;
                        $this->socketList[(int)$conn] = $conn;
                    } else {
                        //receive
                        if ($this->isHttp) {
                            $this->receiveHttp($v);
                        } else {
                            $this->receiveTcp($v);
                        }
                    }
                }
            }
        }
    }

    protected function receiveHttp($stream)
    {
        $buffer = fread($stream, 1024);
        if (empty($buffer) && (feof($stream) || !is_resource($stream))) {
            fclose($stream);
            unset($this->socketList[(int)$stream]);
            echo "退出成功" . PHP_EOL;
            return;
        }
        //http server
        $data     = "Hello World";
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Server: MyServer1\r\n";
        $response .= "Content-length: " . strlen($data) . "\r\n\r\n";
        $response .= $data;
        fwrite($stream, $response);
    }

    /**
     * 处理tcp请求
     */
    private function receiveTcp($stream)
    {
        $buffer = fread($stream, 1024);
        if (feof($stream) || !is_resource($stream)) {
            unset($this->socketList[(int)$stream]);
            fclose($stream);
            echo "退出成功" . PHP_EOL;
            return;
        }
        echo 'onReceive ' . $buffer . " ->" . $stream . PHP_EOL;
        $message = 'I have received that : ' . $buffer;
        fwrite($stream, "{$message}");
    }
}

$server = new Server("0.0.0.0:2345");
// 启动 tcp 服务器
$server->run();

// 启动 http服务器
//$server->isHttp(true)->run();