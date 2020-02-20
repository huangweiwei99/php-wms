<?php

declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use think\Request;

class Order extends BaseController
{
    /**
     * 显示订单列表.
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
        $orders = [];
        $orders_count = $this->app->wms_service->getOrders();
        foreach ($this->app->wms_service->getOrders($keywords, $page, $limit, $sort, $order) as $order) {
            $order = $order->append(['orderpaypal']);
            $orders[] = [
                'id' => $order->id,
                'date' => $order->orderpaypal->payment_date,
                'orderId' => $order->internal_transaction_id,
                'transId' => $order->orderpaypal->transaction_id,
                'status' => $order->status,
                'express' => $order->express,
                'agent' => $order->agent,
                'postage' => $order->postage,
            ];
        }

        $order_list['items'] = $orders;
        $order_list['total'] = count($orders_count);

        return json((resultResponse(['data' => $order_list])));
    }

    /**
     * 显示创建订单表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return '显示创建订单表单页';
    }

    /**
     * 保存新建的订单.
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        return '保存新的订单';
    }

    /**
     * 显示指定的订单.
     *
     * @return \think\Response
     */
    public function read(int $id)
    {
        $order = $this->app->wms_service->getOrderById($id);
        if ($order->isEmpty()) {
            $order = '没有数据';
        } else {
            $order->visible(['id', 'internal_transaction_id', 'api_account']);
        }

        return json((resultResponse(['data' => $order])));
    }

    /**
     * 显示编辑订单表单页.
     *
     * @return \think\Response
     */
    public function edit(int $id)
    {
    }

    /**
     * 保存更新的订单.
     *
     * @return \think\Response
     */
    public function update(Request $request, int $id)
    {
        //处理输入参数
        $params = $request->put(['platform',
                                'api_account',
                                'tracking_number',
                                'agent',
                                'express',
                                'postage',
                                'status', ]);
        $params['id'] = $id;

        $order = $this->app->wms_service->updateOrderById($params, $id);

        // 处理输出数据
        if ($order->isEmpty()) {
            $order = '没有数据';
        }

        return json((resultResponse(['data' => $order])));
    }

    /**
     * 删除指定订单.
     *
     * @return \think\Response
     */
    public function delete(int $id)
    {
        return '删除订单'; //$this->app->wms_service->deleteOrderById($id);
    }
}
