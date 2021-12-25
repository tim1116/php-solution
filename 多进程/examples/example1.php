<?php

define('ROOT_PROC_SRC', dirname(__DIR__).'/src');

include ROOT_PROC_SRC."/PcntlProcess.php";
include ROOT_PROC_SRC."/ProcessItem.php";
include ROOT_PROC_SRC."/Pipe.php";
include ROOT_PROC_SRC."/exception/PcntlProcessException.php";


for ($n = 1; $n <= 3; $n++) {
    $process = new PcntlProcess(function () use ($n) {
        echo 'Child #'.getmypid()." start and sleep {$n}s".PHP_EOL;
        sleep($n);
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
    echo "开始读取: {$n}".PHP_EOL;
    var_dump($info);
}

for ($n = 3; $n--;) {
    // 阻塞状态
    $status = PcntlProcess::wait();
    var_dump($status);
}

var_dump((PcntlProcess::childProcessList()));

echo "end";