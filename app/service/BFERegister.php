<?php

declare(strict_types=1);

namespace app\service;

class BFERegister extends \think\Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('bfe_service', BFE::class);
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
