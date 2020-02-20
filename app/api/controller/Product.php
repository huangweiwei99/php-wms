<?php

declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use think\Request;

class Product extends BaseController
{
    /**
     * 显示产品列表.
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = request()->get(['keywords', 'page', 'limit', 'sort', 'order']);
        $keywords = $params['keywords'] ?? [];
        $page = $params['page'] ?? null;
        $limit = $params['limit'] ?? null;
        $sort = $params['sort'] ?? null;
        $order = $params['order'] ?? null;
        $this->validate([
            'keywords' => $keywords,
            'page' => $page,
            'limit' => $limit,
             ], [
            'keywords' => 'array',
            'page' => 'integer',
            'limit' => 'integer', ]);
        $products = $this->app->wms_service->getProducts($keywords, $page, $limit, $sort, $order);
        $product_count = $this->app->wms_service->getProducts();

        $product_list['items'] = $products->visible(['id', 'sku', 'name', 'weight', 'dimension']);
        $product_list['total'] = count($product_count);

        return json((resultResponse(['data' => $product_list])));
    }

    /**
     * 显示创建产品表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return '显示创建产品表单页';
    }

    /**
     * 保存新建的产品.
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $params = $request->only(['sku', 'weight', 'dimension', 'name']);
        $product = $this->app->wms_service->createProduct($params);

        return json((resultResponse(['data' => $product])));
    }

    /**
     * 显示指定的产品.
     *
     * @return \think\Response
     */
    public function read(int $id)
    {
        $product = $this->app->wms_service->getProductByid($id);

        // 处理输出数据
        if ($product->isEmpty()) {
            $product = '没有数据';
        } else {
            $product->visible(['id', 'supplier_id', 'sku', 'name', 'weight', 'dimension']);
            $product = $product->toArray();
            $product['supplier'] = $product['supplier_id'];
            unset($product['supplier_id']);
        }

        return json((resultResponse(['data' => $product])));
    }

    /**
     * 显示编辑产品表单页.
     *
     * @param int $id
     *
     * @return \think\Response
     */
    public function edit($id)
    {
    }

    /**
     * 保存更新的产品.
     *
     * @return \think\Response
     */
    public function update(Request $request, int $id)
    {
        //处理输入参数
        $params = $request->put(['sku', 'weight', 'dimension', 'name', 'supplier']);
        $params['id'] = $id;

        $product = $this->app->wms_service->updateProductById($params, $id);

        // 处理输出数据
        if ($product->isEmpty()) {
            $product = '没有数据';
        }

        return json((resultResponse(['data' => $product])));
    }

    /**
     * 删除指定产品.
     *
     * @return \think\Response
     */
    public function delete(int $id)
    {
        $this->validate(['ID' => $id], ['ID' => 'integer']);
        $product = $this->app->wms_service->deleteProductById($id);

        return json((resultResponse(['data' => $product ? '删除成功' : '删除出错'])));
    }
}
