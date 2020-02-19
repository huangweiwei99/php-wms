<?php

namespace app\server\socketio;

use PHPSocketIO\SocketIO;
use think\facade\Cache;
use think\worker\Server;

class Simple extends Server
{
    public function __construct()
    {
        $io = new SocketIO(2021);
        // 当有客户端连接时打印一行文字
        $io->on('connection', function ($connection) use ($io) {
            // echo "new connection coming\n";
            // // dump($connection);
            // // 定义chat message事件回调函数
            // $connection->on('chat', function ($msg) use ($io) {
            //     \dump($msg);
            //     // 触发所有客户端定义的chat message from server事件
            //     $io->emit('msg', '服务器信息:'.$msg.'现在时间是'.date('H:i:s'));
            // });

            $connection->on('sync', function ($msg) use ($io) {
                $io->emit('count', '订单同步中,同步第'.Cache::store('redis')->get('pp_number').'订单');
            });
        });
    }
}
