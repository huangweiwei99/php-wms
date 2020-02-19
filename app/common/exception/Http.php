<?php

namespace app\common\exception;

use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

class Http extends Handle
{
    public function render($request, Throwable $e): Response
    {
        $env = $request->env();
        if (isset($env['APP_DEBUG'])) {
            if ($e instanceof ValidateException) {
                $result = [
                    'code' => 422,
                    'msg' => $e->getError(),
                    'type' => 'Validate',
                    'time' => $_SERVER['REQUEST_TIME'],
                ];

                return json($result, 422);
            } elseif ($e instanceof HttpException) {
                $result = [
                    'code' => $e->getStatusCode(),
                    'msg' => $e->getMessage(),
                    'type' => 'Route',
                    'time' => $_SERVER['REQUEST_TIME'],
                ];

                return json($result, $e->getStatusCode());
            } else {
                $result = [
                    'code' => 500,
                    'msg' => '内部错误:'.$e->getMessage(),
                    'type' => 'SYS',
                    'time' => $_SERVER['REQUEST_TIME'],
                ];

                return json($result, 500);
            }
        } else {
            if ($e instanceof ValidateException) {
                $result = [
                    'code' => 422,
                    'msg' => $e->getError(),
                    'type' => 'Validate',
                    'time' => $_SERVER['REQUEST_TIME'],
                ];

                return json($result, 422);
            }

            if ($e instanceof HttpException) {
                $result = [
                    'code' => $e->getStatusCode(),
                    'msg' => $e->getMessage(),
                    'type' => 'Route',
                    'time' => $_SERVER['REQUEST_TIME'],
                ];

                return json($result, $e->getStatusCode());
            }

            // // 其他错误交给系统处理
            return parent::render($request, $e);
        }
    }
}
