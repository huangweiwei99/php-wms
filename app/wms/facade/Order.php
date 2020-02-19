<?php

namespace app\wms\facade;

use app\wms\model\Order as OrderModel;
use think\Facade;

class Order extends Facade
{
    protected static function getFacadeClass()
    {
        return OrderModel::class;
    }
}
