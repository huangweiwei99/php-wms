<?php

declare(strict_types=1);

namespace app\middleware;

use app\BaseController;
use think\facade\Cache;
use think\facade\Cookie;

class Authorize extends BaseController
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
        $username = Cache::store('redis')->get(Cookie::get('token'));
        $roles = $this->app->account_service->getUserByName($username)->roles;

        $permission = $this->app->account_service->getPermissionByControllerAndAction(strtolower($request->controller()), strtolower($request->action()));

        foreach ($roles as $role) {
            if (in_array($permission['id'], explode(',', $role['permission']))) {
                return $next($request);
                break;
            }
        }

        return  json(['没有授权']);
    }
}
