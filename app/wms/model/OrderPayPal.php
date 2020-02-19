<?php

declare(strict_types=1);

namespace app\wms\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class OrderPayPal extends BaseModel
{
    protected $connection = 'wms';

    protected $table = 'wms_order_paypal';

    public function items()
    {
        return $this->hasMany(OrderPayPalItem::class, 'order_paypal_id', 'id');
    }
}
