<?php

declare(strict_types=1);

namespace app\wms\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class Product extends BaseModel
{
    protected $connection = 'wms';

    public function searchSkuAttr($query, $value, $data)
    {
        $query->where('sku', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function searchNameAttr($query, $value, $data)
    {
        $query->where('name', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function searchWeightAttr($query, $value, $data)
    {
        $query->where('weight', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }
}
