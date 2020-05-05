<?php

namespace app\server;

use app\service\PayPal;
use think\worker\Server;
use Workerman\Lib\Timer;

class PayPalOrderSync extends Server
{
    protected $socket = 'http://0.0.0.0:2310';

    public function onWorkerStart()
    {
        //每2.5秒执行一次
        $time_interval = 2.5;

        // Timer::add($time_interval, function () {
        //     echo "task run\n";
        // });
        //  $startDate = '2017-12-20T00:00:00+0100';
        //  $endDate = '2017-12-21T00:00:00+0100';
        //  Timer::add($time_interval, APIServiceFacade::syncOrder($startDate, $endDate, 'paypal_config_vson_mail'));
        //  Timer::add($time_interval, APIServiceFacade::syncPayPalOrderWithWorkerman('paypal_config_vson_mail'));
        Timer::add($time_interval, PayPal::syncPayPalOrderWithWorkermanStatic('paypal_config_vson_mail'));
        echo 'socket开始...';
    }

    public function onMessage($connection, $data)
    {
        \dump($data);

        $result = ['msg' => 'server side'];
        $connection->send(json_encode($result));
    }
}
