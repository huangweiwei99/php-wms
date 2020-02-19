<?php

namespace app\wms\facade;

use app\wms\model\Supplier as SupplierModel;
use think\Facade;

class Supplier extends Facade
{
    protected static function getFacadeClass()
    {
        return SupplierModel::class;
    }
}
