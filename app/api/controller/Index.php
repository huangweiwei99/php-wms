<?php

declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use app\service\PayPal;
use app\service\SFC;
use app\service\WMS;
use think\facade\Cache;
use think\facade\Cookie;
use think\Request;

class Index extends BaseController
{
    public function index()
    {
        return '您好！这是一个[admin]示例应用';
    }

    public function bfe()
    {
        $a = $this->app->bfe_service->response('express-list-all-service', [
            'warehouse' => 'us',
            ]);

        return json($a);
    }

    public function sfc(SFC $sfc)
    {
        // $a = $this->app->sfc_service->response('getRates', [
        //     'country' => 'Canada',
        //     'weight' => 0.05,
        //     'length' => 20,
        //     'width' => 30,
        //     'height' => 20,
        //     'priceType' => 1,
        //     ]);
        $a = $sfc->response('getRates', [
                'country' => 'Canada',
                'weight' => 0.05,
                'length' => 20,
                'width' => 30,
                'height' => 20,
                'priceType' => 1,
                ]);

        return json($a);
    }

    public function pp(PayPal $pp)
    {
        $startDate = '2017-12-20T00:00:00+0100';
        $endDate = '2017-12-21T00:00:00+0100';
        $ppAccount = 'paypal_config_vson_mail';
        $a = $pp->syncTrans($startDate, $endDate, $ppAccount);

        return json($a);
    }

    public function wms(WMS $wms)
    {
        $a = $wms->getProducts();

        return json($a);
    }

    public function products(WMS $wms)
    {
        $a = $wms->getProducts();

        return json($a);
    }

    public function purchase(WMS $wms)
    {
        $a = $wms->getPurchase();

        return json($a);
    }

    public function suppliers(WMS $wms)
    {
        $a = $wms->getSuppliers();

        return json($a);
    }

    public function orders(WMS $wms)
    {
        $a = $wms->getOrders();

        return json($a);
    }

    public function loginWithToken(Request $request)
    {
        // return '登录';
        $params = $request->post(['username', 'password']);
        $this->validate($params, 'app\account\validate\User');

        $user = $this->app->account_service->getUserByUsrAndPwd($params['username'], $params['password']);

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
