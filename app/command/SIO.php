<?php

declare(strict_types=1);

namespace app\command;

use app\server\socketio\Simple;
use PHPSocketIO\SocketIO;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;
use Workerman\Worker;

class SIO extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('sio')
            ->addArgument('action', Argument::OPTIONAL, 'start|stop|restart|reload|status|connections', 'start')
            ->addOption('host', 'H', Option::VALUE_OPTIONAL, 'the host of workerman server.', null)
            ->addOption('port', 'p', Option::VALUE_OPTIONAL, 'the port of workerman server.', null)
            ->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the workerman server in daemon mode.')
            ->setDescription('Workerman Server for ThinkPHP');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $action = $input->getArgument('action');

        if (DIRECTORY_SEPARATOR !== '\\') {
            if (!in_array($action, ['start', 'stop', 'reload', 'restart', 'status', 'connections'])) {
                $output->writeln("<error>Invalid argument action:{$action}, Expected start|stop|restart|reload|status|connections .</error>");

                return false;
            }

            global $argv;
            array_shift($argv);
            array_shift($argv);
            array_unshift($argv, 'think', $action);
        } elseif ('start' != $action) {
            $output->writeln("<error>Not Support action:{$action} on Windows.</error>");

            return false;
        }

        $this->config = Config::get('worker_socketio');

        if ('start' == $action) {
            $output->writeln('Starting PHPStockIO server...');
        }
        // $this->simpleSioServer();

        // $this->startServer($this->config['socketio_class']);
        // $io = new Simple();

        // 自定义服务器入口类
        if (!empty($this->config['socketio_class'])) {
            $class = (array) $this->config['socketio_class'];

            foreach ($class as $server) {
                $this->startServer($server);
            }

            // Run worker
            Worker::runAll();

            return;
        }

        if (!empty($this->config['socket'])) {
            $socket = $this->config['socket'];
            list($host, $port) = explode(':', $socket);
        } else {
            $host = $this->getHost();
            $port = $this->getPort();
            $protocol = !empty($this->config['protocol']) ? $this->config['protocol'] : 'websocket';
            $socket = $protocol.'://'.$host.':'.$port;
            unset($this->config['host'], $this->config['port'], $this->config['protocol']);
        }
        // 避免pid混乱
        $this->config['pidFile'] .= '_'.$port;

        // 开启守护进程模式
        if ($this->input->hasOption('daemon')) {
            Worker::$daemonize = true;
        }

        if (!empty($this->config['ssl'])) {
            $this->config['transport'] = 'ssl';
            unset($this->config['ssl']);
        }

        // 设置服务器参数
        foreach ($this->config as $name => $val) {
            if (in_array($name, ['stdoutFile', 'daemonize', 'pidFile', 'logFile'])) {
                Worker::${$name} = $val;
            } else {
                $worker->$name = $val;
            }
        }

        // Run worker
        Worker::runAll();
    }

    protected function simpleSioServer()
    {
        $io = new SocketIO(2021);
        // 当有客户端连接时打印一行文字
        $io->on('connection', function ($connection) use ($io) {
            echo "new connection coming\n";
            // 定义chat message事件回调函数
            $connection->on('chat message', function ($msg) use ($io) {
                // 触发所有客户端定义的chat message from server事件
                $io->emit('chat message from server', '服务器信息:'.$msg);
            });
        });
    }

    protected function startServer(string $class)
    {
        if (class_exists($class)) {
            $io = new $class();
        // if (!$io instanceof SocketServer) {
            //     $this->output->writeln('<error>Worker Server Class Must extends \\think\\worker\\Server</error>');
            // }
        } else {
            $this->output->writeln("<error>Worker Server Class Not Exists : {$class}</error>");
        }
    }

    // protected function startServer(string $class)
    // {
    //     if (class_exists($class)) {
    //         $io = new $class();
    //         if (!$io instanceof SocketServer) {
    //             $this->output->writeln('<error>Worker Server Class Must extends \\think\\worker\\Server</error>');
    //         }
    //     } else {
    //         $this->output->writeln("<error>Worker Server Class Not Exists : {$class}</error>");
    //     }
    // }

    protected function getHost()
    {
        if ($this->input->hasOption('host')) {
            $host = $this->input->getOption('host');
        } else {
            $host = !empty($this->config['host']) ? $this->config['host'] : '0.0.0.0';
        }

        return $host;
    }

    protected function getPort()
    {
        if ($this->input->hasOption('port')) {
            $port = $this->input->getOption('port');
        } else {
            $port = !empty($this->config['port']) ? $this->config['port'] : 2348;
        }

        return $port;
    }
}
