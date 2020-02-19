<?php

declare(strict_types=1);

namespace app\wms\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class PurchaseContent extends BaseModel
{
    // protected $table = 'wms_purchase_content';

    protected $connection = 'wms';

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
