<?php

declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use think\Request;

class Supplier extends BaseController
{
    /**
     * 显示资源列表.
     *
     * @return \think\Response
     */
    public function index()
    {
        //参数
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

        $suppliers = [];
        foreach ($this->app->wms_service->getSuppliers($keywords, $page, $limit, $sort, $order) as $supplier) {
            $supplier->products->visible(['id', 'dimension', 'name', 'sku', 'weight']);
            $supplier = $supplier->visible(['id', 'name', 'platform', 'address', 'products']);
            $suppliers[] = $supplier;
        }

        $suppliers_count = $this->app->wms_service->getSuppliers();

        $supplier_list['items'] = $suppliers;
        $supplier_list['total'] = count($suppliers_count);

        return json((resultResponse(['data' => $supplier_list])));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return '显示创建供应商表单页';
    }

    /**
     * 保存新建的资源.
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $params = $request->only(['name', 'platform', 'address']);

        $supplier = $this->app->wms_service->createSupplier($params);
        if ($supplier->isEmpty()) {
            $supplier = '没有数据';
        }

        return json((resultResponse(['data' => $supplier])));
    }

    /**
     * 显示指定的资源.
     *
     * @return \think\Response
     */
    public function read(int $id)
    {
        $supplier = $this->app->wms_service->getSupplierById($id);
        if ($supplier->isEmpty()) {
            $supplier = '没有数据';
        } else {
            $supplier->visible(['id', 'name', 'platform', 'address']);
        }

        return json((resultResponse(['data' => $supplier])));
    }

    /**
     * 显示编辑资源表单页.
     *
     * @return \think\Response
     */
    public function edit(int $id)
    {
    }

    /**
     * 保存更新的资源.
     *
     * @return \think\Response
     */
    public function update(Request $request, int $id)
    {
        $params = $request->put(['name', 'platform', 'address']);
        $params['id'] = $id;
        $supplier = $this->app->wms_service->updateSupplierById($params, $id);
        if ($supplier->isEmpty()) {
            $supplier = '没有数据';
        }

        return json((resultResponse(['data' => $supplier])));
    }

    /**
     * 删除指定资源.
     *
     * @return \think\Response
     */
    public function delete(int $id)
    {
        $this->validate(['ID' => $id], ['ID' => 'integer']);
        $supplier = $this->app->wms_service->deleteSupplierById($id);

        return json((resultResponse(['data' => $supplier ? '删除成功' : '删除出错'])));
    }
}
