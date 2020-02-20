<?php

declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use app\service\Account;
use think\facade\Cache;
use think\facade\Cookie;
use think\Request;

class User extends BaseController
{
    public function loginWithToken(Request $request, Account $account)
    {
        // return '登录';
        $params = $request->post(['username', 'password']);
        $this->validate($params, 'app\account\validate\User');

        $user = $account->getUserByUsrAndPwd($params['username'], $params['password']);

        if ($user->isEmpty()) {
            return '用户名或者密码错误';
        }
        // 使用uuid生成唯一秘钥写入redis中，并设置30分钟后过期
        $hash = password_hash(uniqueReal(), PASSWORD_DEFAULT);
        if (Cache::store('redis')->set($hash, $user->username, 1800)) {
            Cookie::set('token', $hash, 1800);

            return json((resultResponse(['data' => ['token' => 'Admin-Token']])));
        }
    }

    public function logout()
    {
        Cookie::delete('token');

        return json((resultResponse(['data' => '退出系统'])));
    }

    public function info($token)
    {
        if ($token) {
            $data = ['avatar' => 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
                    'introduction' => 'administrator',
                    'name' => 'Hugo',
                    'roles' => ['admin'],
            ];
        }

        return json((resultResponse(['data' => $data])));
    }
}
