# 多进程

基于 [pcntl](https://www.php.net/manual/en/book.pcntl.php) ,用于在没有swoole环境中的进程管理。

swoole环境中可以使用 swoole提供的 [Process模块](https://wiki.swoole.com/#/process/process), 实现更加完善的功能。

## 使用说明

##### 1:开启三个子进程完成任务,并实现阻塞回收

```php

// 开启三个子进程
for ($n = 1; $n <= 3; $n++) {
    $process = new PcntlProcess(function () use ($n) {
        //执行业务代码
        sleep($n);
    });
    // 成功的话 返回子进程pid
    $process->start();
}

// 回收
for ($n = 3; $n--;) {
    // 阻塞状态
    $status = PcntlProcess::wait();
    echo "wait {$n}";
    // 获取状态信息
    var_dump($status);
}

// 需要的话可以获取子进程信息
//var_dump((PcntlProcess::childProcessList()));

```

##### 2:基于管道实现进程通信(子进程写入数据给主进程)

```php

// 开启三个子进程
for ($n = 1; $n <= 3; $n++) {
    $process = new PcntlProcess(function () use ($n) {
        sleep($n);
        // 子进程最后 向通道中写入数据
        $pipe = PcntlProcess::pipe();
        $pipe->send([
            'pid' => getmypid(),
            'info' => "abc {$n}",
        ]);
        echo 'Child #'.getmypid().' exit'.PHP_EOL;
    });
    // 成功的话 返回子进程pid
    $process->start();
}

$pipe = PcntlProcess::pipe();
for ($n = 3; $n--;) {
    //读取数据
    $info = $pipe->read();
    echo "开始读取:".PHP_EOL;
    var_dump($info);
}

// 回收
for ($n = 3; $n--;) {
    PcntlProcess::wait();
}

```
