<?php

namespace app\server;

use think\facade\Cache;
use think\worker\Server;
use Workerman\Lib\Timer;

class PPSyncTest extends Server
{
    protected $socket = 'http://0.0.0.0:2320';
    protected $option = ['name' => 'pp'];

    public function onWorkerStart()
    {
        $time_interval = 2.5;
        Timer::add($time_interval, $this->testPP());
    }

    protected function testPP()
    {
        Cache::store('redis')->delete('pp_number');
        for ($i = 0; $i < 10; ++$i) {
            sleep(1);

            dump('同步第'.($i + 1).'订单');
            Cache::store('redis')->set('pp_number', $i + 1, 0);
        }

        return;
    }
}
