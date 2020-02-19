<?php

declare(strict_types=1);

namespace app\wms\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class Supplier extends BaseModel
{
    protected $connection = 'wms';

    public function searchNameAttr($query, $value, $data)
    {
        $query->where('name', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function searchPlatformAttr($query, $value, $data)
    {
        $query->where('platform', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function searchAddressAttr($query, $value, $data)
    {
        $query->where('address', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
