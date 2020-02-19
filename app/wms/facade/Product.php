<?php

namespace app\wms\facade;

use app\wms\model\Product as ProductModel;
use think\Facade;

class Product extends Facade
{
    protected static function getFacadeClass()
    {
        return ProductModel::class;
    }
}
