<?php

declare(strict_types=1);

namespace app\api\controller;

use PHPSocketIO\SocketIO;
use Workerman\Worker;

class Sio
{
    public function index()
    {
        $io = new SocketIO(2311);
        $io->on('connection', function ($socket) use ($io) {
            echo 'new connection coming';
        });
        Worker::runAll();
    }
}
