<?php

// 这是系统自动生成的provider定义文件

use app\common\exception\Http;

return [
    // 绑定自定义异常处理handle类
    'think\exception\Handle' => Http::class,
];
