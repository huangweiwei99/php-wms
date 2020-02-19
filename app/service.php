<?php

use app\AppService;
use app\service\AccountRegister;
use app\service\BFERegister;
use app\service\PayPalRegister;
use app\service\SFCRegister;
use app\service\WMSRegister;

// 系统服务定义文件
// 服务在完成全局初始化之后执行
return [
    AppService::class,
    BFERegister::class,
    SFCRegister::class,
    PayPalRegister::class,
    WMSRegister::class,
    AccountRegister::class,
];
