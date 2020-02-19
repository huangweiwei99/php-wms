<?php

declare(strict_types=1);

namespace app\service;

class PayPalRegister extends \think\Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('paypal_service', PayPal::class);
    }

    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {
    }
}
