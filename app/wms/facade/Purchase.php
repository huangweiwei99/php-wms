<?php

namespace app\wms\facade;

use app\wms\model\Purchase as PurchaseModel;
use think\Facade;

class Purchase extends Facade
{
    protected static function getFacadeClass()
    {
        return PurchaseModel::class;
    }
}
