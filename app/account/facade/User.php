<?php

namespace app\account\facade;

use think\Facade;

class User extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\account\model\User';
    }
}
