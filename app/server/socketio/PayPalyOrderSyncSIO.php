<?php

namespace app\server\socketio;

use app\service\PayPal;
use PHPSocketIO\SocketIO;

class PayPalyOrderSyncSIO extends SocketIO
{
    public function __construct()
    {
        $io = new SocketIO(2022);
        // 当有客户端连接时打印一行文字
        $io->on('connection', function ($connection) use ($io) {
            echo '来自客户端的连接';
            // 定义chat message事件回调函数
            $connection->on('chat message', function ($msg) use ($io) {
                // 触发所有客户端定义的chat message from server事件
                $io->emit('chat message from server', '同步开始');
                // $items = PayPal::getTransDetailStatic('2017-12-20T00:00:00+0100', '2017-12-21T00:00:00+0100');
                // foreach ($items as $key => $value) {
                //     \sleep(5);
                //     $io->emit('chat message from server', '服务器信息:同步第'.$msg.'条');
                // }
                // $io->emit('chat message from server', '服务器信息:'.$msg);

                for ($i = 0; $i < 60; ++$i) {
                    \sleep(1);
                    $io->emit('chat message from server', '服务器信息:同步第'.$i.'条');
                }
            });
        });
    }
}
