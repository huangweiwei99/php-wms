<?php

declare(strict_types=1);

namespace app\service;

class WMSRegister extends \think\Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind('wms_service', WMS::class);
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
