<?php

namespace app\account\facade;

use think\Facade;

class Permission extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\account\model\Permission';
    }
}
