<?php

declare(strict_types=1);

namespace app\wms\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class OrderPayPalItem extends BaseModel
{
    protected $connection = 'wms';

    protected $table = 'wms_order_paypal_item';
}
