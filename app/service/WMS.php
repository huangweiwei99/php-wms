<?php

namespace app\service;

use app\wms\facade\Order;
use app\wms\facade\Product;
use app\wms\facade\Purchase;
use app\wms\facade\Supplier;

interface IWMS
{
    //############################# Product #############################

    public function getProducts($keywords = null, $page = null, $limit = null, $sort = null, $order = null);

    public function getProductById(int $id);

    public function createProduct(array $data);

    public function updateProductById(array $data, int $id);

    public function deleteProductById(int $id);

    //############################# Supplier #############################

    public function getSuppliers($keywords = null, $page = null, $limit = null, $sort = null, $order = null);

    public function getSupplierById(int $id);

    public function createSupplier(array $data);

    public function updateSupplierById(array $data, int $id);

    public function deleteSupplierById(int $id);

    //############################# Purchase #############################

    public function getPurchase($keywords = null, $page = null, $limit = null, $sort = null, $order = null);

    public function getPurchaseById(int $id);

    public function createPurchase(array $data);

    public function updatePurchaseById(array $data, int $id);

    public function deletePurchaseById(int $id);

    //############################# Order #############################

    public function getOrders($keywords = null, $page = null, $limit = null, $sort = null, $order = null);

    public function getOrderById(int $id);

    public function createOrder(array $data);

    public function updateOrderById(array $data, int $id);

    public function deleteOrderById(int $id);
}

class WMS implements IWMS
{
    //############################# Product #############################

    public function getProductById(int $id)
    {
        $product = Product::getDataById($id);

        return $product;
    }

    public function getProducts($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        $product = Product::getDataCollection($keywords, $page, $limit, $sort, $order);

        return $product;
    }

    public function createProduct(array $data)
    {
        return Product::createData($data);
    }

    public function updateProductById(array $data, int $id)
    {
        return Product::updateDataById($data, $id);
    }

    public function deleteProductById(int $id)
    {
        validate(['id' => 'integer'])->check(['id' => $id]);

        return Product::deleteDataById($id);
    }

    //############################# Purchase #############################

    public function getPurchaseById(int $id)
    {
        $purchase = Purchase::getDataById($id);

        return $purchase;
    }

    public function getPurchase($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        $purchase = Purchase::getDataCollection($keywords, $page, $limit, $sort, $order);

        return $purchase;
    }

    public function createPurchase(array $data)
    {
        return Purchase::createData($data);
    }

    public function updatePurchaseById(array $data, int $id)
    {
        return Purchase::updateDataById($data, $id);
    }

    public function deletePurchaseById(int $id)
    {
        return Purchase::deleteDataById($id);
    }

    //############################# Supplier #############################

    public function getSupplierById(int $id)
    {
        $supplier = Supplier::getDataById($id);

        return $supplier;
    }

    public function getSuppliers($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        $suppliers = Supplier::getDataCollection($keywords, $page, $limit, $sort, $order);

        return $suppliers;
    }

    public function createSupplier(array $data)
    {
        return Supplier::createData($data);
    }

    public function updateSupplierById(array $data, int $id)
    {
        return Supplier::updateDataById($data, $id);
    }

    public function deleteSupplierById(int $id)
    {
        return Supplier::deleteDataById($id);
    }

    //############################# Supplier #############################

    public function getOrderById(int $id)
    {
        $order = Order::getDataById($id);

        return $order;
    }

    public function getOrders($keywords = null, $page = null, $limit = null, $sort = null, $order = null)
    {
        $collection = Order::getDataCollection($keywords, $page, $limit, $sort, $order);

        return $collection;
    }

    public function createOrder(array $data)
    {
        return Order::createData($data);
    }

    public function updateOrderById(array $data, int $id)
    {
        return Order::updateDataById($data, $id);
    }

    public function deleteOrderById(int $id)
    {
        return 'order deleted';
    }
}
