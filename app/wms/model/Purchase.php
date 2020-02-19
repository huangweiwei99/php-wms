<?php

declare(strict_types=1);

namespace app\wms\model;

use app\BaseModel;

/**
 * @mixin think\Model
 */
class Purchase extends BaseModel
{
    protected $connection = 'wms';

    public function setDateAttr($value)
    {
        return strtotime($value);
    }

    public function searchPlaceAttr($query, $value, $data)
    {
        $query->where('place', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    public function searchPurchaseTransactionIdAttr($query, $value, $data)
    {
        $query->where('purchase_transaction_id', 'like', $value.'%');
        if (isset($data['sort'])) {
            $query->order($data['sort']);
        }
    }

    // public function getSupplierAttr()
    // {
    //     $product = $this->Products()->select()[0];

    //     return $product->supplier;
    // }

    public function Products()
    {
        return $this->hasMany(Product::class, 'product_id', 'id');
    }

    public function Supplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function content()
    {
        return $this->hasMany(PurchaseContent::class, 'pid', 'id');
    }

    public function updateDataById($data, $id)
    {
        $this->startTrans();
        try {
            $purchase = $this->find($id);

            if ($purchase) {
                $purchase->save($data);

                $purchase->content->delete();
                if (!empty($data['products'])) {
                    $purchase->content()->saveAll($data['products']);
                }
                $this->commit();

                return $purchase;
            }
            $this->error = '找不到要更新的数据';

            return $this;
        } catch (\Throwable $th) {
            $this->error = '错误:'.$th->getMessage();
            $this->rollback();

            return $this;
        }
    }

    public function createData($data)
    {
        $this->startTrans();
        try {
            $purchase = $this->create($data);

            if (!empty($data['products'])) {
                $purchase->content()->saveAll($data['products']);
            }
            $this->commit();

            return $purchase;
        } catch (\Throwable $th) {
            //throw $th;
            $this->rollback();
        }
    }

    public function deleteDataById($id)
    {
        $this->startTrans();
        try {
            $purchase = $this->with('content')->find($id);
            $purchase->together(['content'])->delete();
            //code...
            $this->commit();
        } catch (\Throwable $th) {
            //throw $th;
            $this->rollback();
        }
    }
}
