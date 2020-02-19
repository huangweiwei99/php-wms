<?php

declare(strict_types=1);

namespace app\middleware;

use think\facade\Cache;
use think\facade\Cookie;

class CheckToken
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     *
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $token = Cookie::get('token');
        $cache = Cache::store('redis')->get($token);
        if ($cache) {
            return $next($request);
        } else {
            return json(['请登录']);
        }
    }
}
