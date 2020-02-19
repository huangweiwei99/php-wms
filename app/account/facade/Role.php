<?php

namespace app\account\facade;

use think\Facade;

class Role extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\account\model\Role';
    }
}
