<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\Request;

class Purchase extends BaseController
{
    /**
     * ��示资源列表.
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
        $purchase = [];
        foreach ($this->app->wms_service->getPurchase($keywords, $page, $limit, $sort, $order)->hidden(['create_time', 'update_time', 'supplier_id']) as $p) {
            $p = $p->append(['supplier', 'content']);

            //供应商
            $supplier = $p->supplier;
            $p['supplier'] = ['id' => $supplier->id, 'address' => $supplier->address, 'platform' => $supplier->platform];

            //采购的产品
            $products = [];
            foreach ($p->content->append(['product']) as $content) {
                $products[] = ['content_id' => $content->id,
                                'cost' => $content->cost,
                                'quantity' => $content->quantity,
                                'sku' => $content->product->sku,
                                'id' => $content->product->id,
                             ];
            }
            $p['products'] = $products;

            //采购单号
            $p['purchasetransid'] = $p['purchase_transaction_id'];

            $p = $p->toArray();
            unset($p['content']);
            unset($p['purchase_transaction_id']);

            $purchase[] = $p;
        }

        $purchase_count = count($this->app->wms_service->getPurchase());

        $purchase_list['items'] = $purchase;
        $purchase_list['total'] = $purchase_count;

        return json((resultResponse(['data' => $purchase_list])));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return '显示采购单表单';
    }

    /**
     * 保存新建的资源.
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $params = $request->post(['date', 'purchase_transaction_id', 'place', 'supplier_id', 'products']);
        if (!empty($params['products'])) {
            foreach ($params['products'] as $product) {
                if (isset($product['cost']) && isset($product['quantity'])) {
                    $products[] = ['cost' => $product['cost'], 'quantity' => $product['quantity'], 'product_id' => $product['id']];
                }
            }
        }
        $params['products'] = $products;

        $purchase = $this->app->wms_service->createPurchase($params);
        // $purchase = '';

        return json((resultResponse(['data' => $purchase])));
    }

    /**
     * 显示指定的资源.
     *
     * @return \think\Response
     */
    public function read(int $id)
    {
        $purchase = $this->app->wms_service->getPurchaseById($id);
        if ($purchase->isEmpty()) {
            $purchase = '没有数据';
        } else {
            $purchase->visible(['id', 'date', 'purchase_transaction_id', 'place']);
        }

        return json((resultResponse(['data' => $purchase])));
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     *
     * @return \think\Response
     */
    public function edit($id)
    {
        return '显示编辑采购单单页';
    }

    /**
     * 保存更新的资源.
     *
     * @return \think\Response
     */
    public function update(Request $request, int $id)
    {
        $params = $request->put(['date', 'purchase_transaction_id', 'place', 'supplier_id', 'products']);
        $params['id'] = $id;
        $products = [];

        if (!empty($params['products'])) {
            foreach ($params['products'] as $product) {
                if (isset($product['cost']) && isset($product['quantity'])) {
                    $products[] = ['pid' => $id, 'cost' => $product['cost'], 'quantity' => $product['quantity'], 'product_id' => $product['id']];
                }
            }
        }
        $params['products'] = $products;

        $purchase = $this->app->wms_service->updatePurchaseById($params, $id);

        return json((resultResponse(['data' => $purchase])));
    }

    /**
     * 删除指定资源.
     *
     * @return \think\Response
     */
    public function delete(int $id)
    {
        $this->validate(['ID' => $id], ['ID' => 'integer']);
        $purchase = $this->app->wms_service->deletePurchaseById($id);

        return json((resultResponse(['data' => $purchase ? '删除成功' : '删除出错'])));
    }
}
