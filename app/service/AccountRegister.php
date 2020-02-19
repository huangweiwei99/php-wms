<?php

declare(strict_types=1);

namespace app\service;

class AccountRegister extends \think\Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('account_service', Account::class);
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
