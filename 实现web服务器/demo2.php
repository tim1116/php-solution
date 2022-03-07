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

    public function __construct(string $address)
    {
        $this->socket = @stream_socket_server($address, $errNo, $errMessage);
        if (!is_resource($this->socket)) {
            throw new InvalidArgumentException("参数异常:" . $errMessage);
        }
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
            $conn = @stream_socket_accept($this->socket);
            if ($conn) {
                if ($this->isHttp) {
                    if (pcntl_fork() == 0) {
                        //http server
                        $data     = "Hello World";
                        $response = "HTTP/1.1 200 OK\r\n";
                        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
                        $response .= "Server: MyServer1\r\n";
                        $response .= "Content-length: " . strlen($data) . "\r\n\r\n";
                        $response .= $data;
                        fwrite($conn, $response);
                        fclose($conn);
                        exit;
                    }
                } else {
                    // tcp server
                    if (pcntl_fork() == 0) {
                        while ($message = fread($conn, 1024)) {
                            echo 'I have received that : ' . $message;
                            fwrite($conn, "OK\n");
                        }
                        exit;
                    }
                }
            }
        }
    }
}

$server = new Server("0.0.0.0:2345");
// 启动 tcp 服务器
//$server->run();

// 启动 http服务器
$server->isHttp(true)->run();