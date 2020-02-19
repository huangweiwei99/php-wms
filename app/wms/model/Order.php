<?php

declare(strict_types=1);

namespace app\wms\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class Order extends BaseModel
{
    protected $connection = 'wms';

    public function searchApiAccountAttr($query, $value, $data)
    {
        $query->where('api_username', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function searchAgentAttr($query, $value, $data)
    {
        $query->where('agent', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function searchStatusAttr($query, $value, $data)
    {
        $query->where('status', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function orderpaypal()
    {
        return  $this->hasOne(OrderPayPal::class);
    }

    public function createPayPalOrder($data)
    {
        $this->startTrans();
        try {
            $order = $this->create($data);
            $order->orderpaypal()->save($data['paypalorder']);
            $order->orderpaypal->items()->saveAll($data['paypalorder']['items']);
            $this->commit();

            return $order;
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }
}
