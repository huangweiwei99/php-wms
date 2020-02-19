<?php

namespace app\service;

use app\wms\facade\OrderFacade;
use PayPal\PayPalAPI\GetTransactionDetailsReq;
use PayPal\PayPalAPI\GetTransactionDetailsRequestType;
use PayPal\PayPalAPI\TransactionSearchReq;
use PayPal\PayPalAPI\TransactionSearchRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use think\facade\Cache;
use think\facade\Config;

class PayPal
{
    /**
     * 获取账号授权.
     */
    protected function getConfig($pp_account)
    {
        $paypal_config_base = Config::get('api.paypal_config_base');
        $paypal_account_config = Config::get('api.'.$pp_account);

        // $paypal_config_base=Config::load('extra/config', 'extra');

        return array_merge($paypal_config_base, $paypal_account_config);
    }

    /**
     * 搜索订单号.
     */
    public function getTransSearch($startDate, $endDate, $pp_account, $transaction_class = 'Received')
    {
        $transactionSearchRequest = new TransactionSearchRequestType();
        $transactionSearchRequest->StartDate = $startDate; //'2017-12-20T00:00:00+0100';
        $transactionSearchRequest->EndDate = $endDate; //'2017-12-21T00:00:00+0100';
        $transactionSearchRequest->TransactionClass = $transaction_class;

        $tranSearchReq = new TransactionSearchReq();
        $tranSearchReq->TransactionSearchRequest = $transactionSearchRequest;

        $paypalService = new PayPalAPIInterfaceServiceService($this->getConfig($pp_account));

        try {
            $transactionSearchResponse = $paypalService->TransactionSearch($tranSearchReq);
            $items = [];
            if ('Success' === $transactionSearchResponse->Ack) {
                if (!\is_null($transactionSearchResponse->PaymentTransactions)) {
                    foreach ($transactionSearchResponse->PaymentTransactions as $transaction) {
                        $items[] = $transaction->TransactionID;
                    }
                }
            }
            if ('Failure' === $transactionSearchResponse->Ack) {
                dump(is_null($transactionSearchResponse->Errors) ? '唔知道挫系唔薯' : $transactionSearchResponse->Errors);
            }

            return $items;
        } catch (\Expection $e) {
            dump($e->getMessage());
        }
    }

    /**
     * 描述：交易详细.
     *
     * @param $transId
     * @param $accountConfig
     *
     * @return \PayPal\PayPalAPI\GetTransactionDetailsResponseType|string
     *
     * @throws \Exception
     */
    public function getTransDetails($transId, $pp_account)
    {
        $transactionDetails = new GetTransactionDetailsRequestType();
        $transactionDetails->TransactionID = $transId;

        $request = new GetTransactionDetailsReq();
        $request->GetTransactionDetailsRequest = $transactionDetails;

        $paypalService = new PayPalAPIInterfaceServiceService($this->getConfig($pp_account));

        try {
            $transDetailsResponse = $paypalService->GetTransactionDetails($request);

            return $transDetailsResponse;
        } catch (\Expection $e) {
            dump($e->getMessage());
        }
    }

    /**
     * 在日期范围内的交易明细,一次最多只能同步一百条
     *
     * @param $startDate
     * @param $endDate
     * @param $pp_account
     * @param $transaction_class
     *
     * @return []GetTransactionDetailsRequestType
     *
     * @throws \Exception
     */
    public function syncTrans($startDate, $endDate, $pp_account, $transaction_class = 'Received')
    {
        $items = $this->getTransSearch($startDate, $endDate, $pp_account);
        foreach ($items as $key => $value) {
            $this->getTransDetails($value, $pp_account);
        }

        $config = $this->getConfig($pp_account);

        $paypalService = new PayPalAPIInterfaceServiceService($config);

        //搜索订单请求
        $transactionSearchRequest = new TransactionSearchRequestType();
        $transactionSearchRequest->StartDate = $startDate; //'2017-12-20T00:00:00+0100';
        $transactionSearchRequest->EndDate = $endDate; //'2017-12-21T00:00:00+0100';
        $transactionSearchRequest->TransactionClass = $transaction_class;

        $tranSearchReq = new TransactionSearchReq();
        $tranSearchReq->TransactionSearchRequest = $transactionSearchRequest;

        try {
            // 返回交易
            $transactionSearchResponse = $paypalService->TransactionSearch($tranSearchReq);
            $search_items = [];

            if ('Failure' === $transactionSearchResponse->Ack) {
                dump(is_null($transactionSearchResponse->Errors) ? '唔知道挫系唔薯' : $transactionSearchResponse->Errors);
            }
            if (!\is_null($transactionSearchResponse->PaymentTransactions)) {
                foreach ($transactionSearchResponse->PaymentTransactions as $transaction) {
                    $search_items[] = $transaction->TransactionID;
                }
            }
            $details_items = [];
            foreach ($search_items as $transId) {
                // 返回交易明细
                $transactionDetails = new GetTransactionDetailsRequestType();
                $transactionDetails->TransactionID = $transId;

                $request = new GetTransactionDetailsReq();
                $request->GetTransactionDetailsRequest = $transactionDetails;

                $transDetailsResponse = $paypalService->GetTransactionDetails($request);
                $details_items[] = $transDetailsResponse;
            }

            return $details_items;
        } catch (\Expection $e) {
            dump($e->getMessage());
        }
    }

    // 同步全部订单
    public function syncPayPalOrderWithWorkerman($pp_account = 'paypal_config_vson_mail', $resettime = 0)
    {
        $account_tids = $pp_account.'_tids'; //所有交易号
        $account_end_currentday = $pp_account.'_end_currentday'; //程序运行的最后一天

        //计算时间跨度
        dump('计算时间跨度');
        $start_time = Cache::store('redis')->get($account_end_currentday) ?? \strtotime('2019-12-20');
        $end_time = time();
        $days = ($end_time - $start_time) / 86400;
        $days = \ceil($days);
        $tids = Cache::store('redis')->get($account_tids) ?? []; //只有订单号的一维数组
        // $tids_index = []; //每日对应的日期订单二维数组
        for ($i = 0; $i < $days; ++$i) {
            $start = $start_time + $i * 86400;
            $end = $start + 86400;
            $start_currentday = date("Y-m-d\TH:i:s\Z", $start);
            $end_currentday = date("Y-m-d\TH:i:s\Z", $end);
            //防止超过实际时间
            if (($start_time + ($i + 1) * 86400 - time()) > 0) {
                $end_currentday = date("Y-m-d\TH:i:s\Z", time());
            }
            dump('开始获取交易号');
            $currentids = $this->getTransSearch($start_currentday, $end_currentday, $pp_account);
            if (100 >= count($currentids)) {
                $tids = \array_merge($tids, $currentids);
                // $tids_index[] = [strtotime($end_currentday) => $currentids];
            }
            Cache::store('redis')->set($account_tids, $tids, 0); //记录每次增量,以便中断恢复
            Cache::store('redis')->set($account_end_currentday, strtotime($end_currentday), 0);
            dump($start_currentday.'到'.$end_currentday.'24小时内共有:'.count($currentids).'记录'); //每天的交易记录
            dump('------------------');
        }

        dump('获取订单号结束,总共有'.count(Cache::store('redis')->get($account_tids)).'条记录');

        $transactionids = Cache::store('redis')->get($account_tids);
        $total = count($transactionids); //总交易数
        dump('共有'.$total.'记录');
        dump('准���写入数据库');
        // 同步中
        foreach ($transactionids as $key => $value) {
            $order = OrderFacade::hasWhere('orderpaypal', ['transaction_id' => $value])->findOrEmpty();
            if ($order->isEmpty()) {
                //订单表
                $internal_transaction_id = OrderFacade::max('internal_transaction_id', false);
                if (substr($internal_transaction_id, 0, 8) != date('Ymd')) {
                    $internal_transaction_id = date('Ymd').'0001';
                } else {
                    $internal_transaction_id = ((int) $internal_transaction_id + 1);
                }
                // // dump($details);
                $details = $this->getTransDetails($value, $pp_account)->PaymentTransactionDetails;
                $items = [];
                if (!empty($details->PaymentItemInfo->PaymentItem)) {
                    foreach ($details->PaymentItemInfo->PaymentItem as $item) {
                        $items[] = [
                            'ebay_item_txn_id' => $item->EbayItemTxnId,
                            'item_name' => $item->Name,
                            'item_number' => $item->Number,
                            'item_quantity' => $item->Quantity,
                            'item_amount' => $item->Amount->value ?? null,
                        ];
                    }
                }
                //PayPal订单表
                $pp_order = [
                    'receiver' => $details->ReceiverInfo->Receiver ?? null,
                    'payer_id' => $details->PayerInfo->PayerID ?? null,
                    'payer' => $details->PayerInfo->Payer ?? null,
                    'payer_firstname' => $details->PayerInfo->PayerName->FirstName ?? null,
                    'payer_middlename' => $details->PayerInfo->PayerName->MiddleName ?? null,
                    'payer_lastname' => $details->PayerInfo->PayerName->LastName ?? null,
                    'payer_business' => $details->PayerInfo->PayerID ?? null,
                    'payer_address_owner' => $details->PayerInfo->Address->AddressOwner ?? null,
                    'payer_address_status' => $details->PayerInfo->PayerStatus ?? null,
                    'payer_address_name' => $details->PayerInfo->Address->Name ?? null,
                    'payer_address_street1' => $details->PayerInfo->Address->Street1 ?? null,
                    'payer_address_street2' => $details->PayerInfo->Address->Street2 ?? null, //dump($details->PayerInfo->Address))?null:,die())?null:,
                    'payer_city_name' => $details->PayerInfo->Address->CityName ?? null,
                    'payer_state_or_province' => $details->PayerInfo->Address->StateOrProvince ?? null,
                    'payer_postal_code' => $details->PayerInfo->Address->PostalCode ?? null,
                    'payer_country' => $details->PayerInfo->Address->Country ?? null,
                    'payer_country_name' => $details->PayerInfo->Address->CountryName ?? null,
                    'payer_phone' => $details->PayerInfo->Address->Phone ?? null,
                    'transaction_id' => $details->PaymentInfo->TransactionID ?? null,
                    'ebay_transaction_id' => $details->PaymentInfo->EbayTransactionID ?? null,
                    'parent_transaction_id' => $details->PayerInfo->PayerID ?? null,
                    'payment_type' => $details->PaymentInfo->PaymentType ?? null,
                    'payment_date' => strtotime($details->PaymentInfo->PaymentDate) ?? null,
                    'currency_code' => $details->PaymentInfo->GrossAmount->currencyID ?? null,
                    'gross_amount' => $details->PaymentInfo->GrossAmount->value ?? null,
                    'fee_amount' => $details->PaymentInfo->FeeAmount->value ?? null,
                    'settle_amount' => $details->PaymentInfo->SettleAmount->value ?? null,
                    'tax_amount' => $details->PaymentInfo->TaxAmount->value ?? null,
                    'exchange_rate' => $details->PaymentInfo->ExchangeRate ?? null,
                    'payment_status' => $details->PaymentInfo->PaymentStatus ?? null,
                    'pending_reason' => $details->PaymentInfo->PendingReason ?? null,
                    'invoice_id' => $details->PaymentItemInfo->InvoiceID ?? null,
                    'memo' => $details->PaymentItemInfo->Memo ?? null,
                    'sales_tax' => $details->PaymentItemInfo->SalesTax ?? null,
                    'payer_status' => $details->PayerInfo->PayerStatus ?? null,
                    'subject' => $details->PaymentInfo->Subject ?? null,
                    'buyer_id' => $details->PaymentItemInfo->Auction ?? null,
                    'items' => $items,
                ];

                $data = [
                    'platform' => 1,
                    'internal_transaction_id' => $internal_transaction_id,
                    'paypalorder' => $pp_order,
                    'status' => 1,
                ];
                // dump($pp_order['items']);
                $order = OrderFacade::createPayPalOrder($data);
                dump('创建第'.($key + 1).'订单记录');
            }

            // todo ...写入数据库
            $percent = ceil((($key + 1) / $total) * 100);
            dump('已完成:'.$percent.'%');
            dump('------------------');
            // Cache::store('redis')->set('syncstatus', $percent);
            // array_splice($transactionids, 0, 1);

            // if ($key === $total - 1) {
            //     // Cache::store('redis')->set('syncstatus', 0);
            //     // Cache::store('redis')->delete($tids);
            // }
        }

        dump('同步已完成');
        Cache::store('redis')->delete($account_tids);
        Cache::store('redis')->delete($account_end_currentday);
    }

    protected static function getConfigStatic($pp_account)
    {
        $paypal_config_base = Config::get('api.paypal_config_base');
        $paypal_account_config = Config::get('api.'.$pp_account);

        // $paypal_config_base=Config::load('extra/config', 'extra');

        return array_merge($paypal_config_base, $paypal_account_config);
    }

    /**
     * 搜索订单号.
     */
    public static function getTransSearchStatic($startDate, $endDate, $pp_account, $transaction_class = 'Received')
    {
        $transactionSearchRequest = new TransactionSearchRequestType();
        $transactionSearchRequest->StartDate = $startDate; //'2017-12-20T00:00:00+0100';
        $transactionSearchRequest->EndDate = $endDate; //'2017-12-21T00:00:00+0100';
        $transactionSearchRequest->TransactionClass = $transaction_class;

        $tranSearchReq = new TransactionSearchReq();
        $tranSearchReq->TransactionSearchRequest = $transactionSearchRequest;

        $paypalService = new PayPalAPIInterfaceServiceService(self::getConfigStatic($pp_account));

        try {
            $transactionSearchResponse = $paypalService->TransactionSearch($tranSearchReq);
            $items = [];
            if ('Success' === $transactionSearchResponse->Ack) {
                if (!\is_null($transactionSearchResponse->PaymentTransactions)) {
                    foreach ($transactionSearchResponse->PaymentTransactions as $transaction) {
                        $items[] = $transaction->TransactionID;
                    }
                }
            }
            if ('Failure' === $transactionSearchResponse->Ack) {
                dump(is_null($transactionSearchResponse->Errors) ? '唔知道挫系唔薯' : $transactionSearchResponse->Errors);
            }

            return $items;
        } catch (\Expection $e) {
            dump($e->getMessage());
        }
    }

    /**
     * 描述：交易详细.
     *
     * @param $transId
     * @param $accountConfig
     *
     * @return \PayPal\PayPalAPI\GetTransactionDetailsResponseType|string
     *
     * @throws \Exception
     */
    public static function getTransDetailStatic($transId, $pp_account)
    {
        $transactionDetails = new GetTransactionDetailsRequestType();
        $transactionDetails->TransactionID = $transId;

        $request = new GetTransactionDetailsReq();
        $request->GetTransactionDetailsRequest = $transactionDetails;

        $paypalService = new PayPalAPIInterfaceServiceService(self::getConfigStatic($pp_account));

        try {
            $transDetailsResponse = $paypalService->GetTransactionDetails($request);

            return $transDetailsResponse;
        } catch (\Expection $e) {
            dump($e->getMessage());
        }
    }

    // 同步全部订单
    public static function syncPayPalOrderWithWorkermanStatic($pp_account = 'paypal_config_vson_mail', $resettime = 0)
    {
        $account_tids = $pp_account.'_tids'; //所有交易号
        $account_end_currentday = $pp_account.'_end_currentday'; //程序运行的最后一天

        //计算时间跨度
        dump('计算时间跨度');
        $start_time = Cache::store('redis')->get($account_end_currentday) ?? \strtotime('2019-12-20');
        $end_time = time();
        $days = ($end_time - $start_time) / 86400;
        $days = \ceil($days);
        $tids = Cache::store('redis')->get($account_tids) ?? []; //只有订单号的一维数组
        // $tids_index = []; //每日对应的日期订单二维数组
        for ($i = 0; $i < $days; ++$i) {
            $start = $start_time + $i * 86400;
            $end = $start + 86400;
            $start_currentday = date("Y-m-d\TH:i:s\Z", $start);
            $end_currentday = date("Y-m-d\TH:i:s\Z", $end);
            //防止超过实际时间
            if (($start_time + ($i + 1) * 86400 - time()) > 0) {
                $end_currentday = date("Y-m-d\TH:i:s\Z", time());
            }
            dump('开始获取交易号');
            $currentids = self::getTransSearchStatic($start_currentday, $end_currentday, $pp_account);
            if (100 >= count($currentids)) {
                $tids = \array_merge($tids, $currentids);
                // $tids_index[] = [strtotime($end_currentday) => $currentids];
            }
            Cache::store('redis')->set($account_tids, $tids, 0); //记录每次增量,以便中断恢复
            Cache::store('redis')->set($account_end_currentday, strtotime($end_currentday), 0);
            dump($start_currentday.'到'.$end_currentday.'24小时内共有:'.count($currentids).'记录'); //每天的交易记录
            dump('------------------');
        }

        dump('获取订单号结束,总共有'.count(Cache::store('redis')->get($account_tids)).'条记录');

        $transactionids = Cache::store('redis')->get($account_tids);
        $total = count($transactionids); //总交易数
        dump('共有'.$total.'记录');
        dump('准备写入数据库');
        // 同步中
        foreach ($transactionids as $key => $value) {
            $order = OrderFacade::hasWhere('orderpaypal', ['transaction_id' => $value])->findOrEmpty();
            if ($order->isEmpty()) {
                //订单表
                $internal_transaction_id = OrderFacade::max('internal_transaction_id', false);
                if (substr($internal_transaction_id, 0, 8) != date('Ymd')) {
                    $internal_transaction_id = date('Ymd').'0001';
                } else {
                    $internal_transaction_id = ((int) $internal_transaction_id + 1);
                }
                // // dump($details);
                $details = self::getTransDetailStatic($value, $pp_account)->PaymentTransactionDetails;
                $items = [];
                if (!empty($details->PaymentItemInfo->PaymentItem)) {
                    foreach ($details->PaymentItemInfo->PaymentItem as $item) {
                        $items[] = [
                            'ebay_item_txn_id' => $item->EbayItemTxnId,
                            'item_name' => $item->Name,
                            'item_number' => $item->Number,
                            'item_quantity' => $item->Quantity,
                            'item_amount' => $item->Amount->value ?? null,
                        ];
                    }
                }
                //PayPal订单表
                $pp_order = [
                    'receiver' => $details->ReceiverInfo->Receiver ?? null,
                    'payer_id' => $details->PayerInfo->PayerID ?? null,
                    'payer' => $details->PayerInfo->Payer ?? null,
                    'payer_firstname' => $details->PayerInfo->PayerName->FirstName ?? null,
                    'payer_middlename' => $details->PayerInfo->PayerName->MiddleName ?? null,
                    'payer_lastname' => $details->PayerInfo->PayerName->LastName ?? null,
                    'payer_business' => $details->PayerInfo->PayerID ?? null,
                    'payer_address_owner' => $details->PayerInfo->Address->AddressOwner ?? null,
                    'payer_address_status' => $details->PayerInfo->PayerStatus ?? null,
                    'payer_address_name' => $details->PayerInfo->Address->Name ?? null,
                    'payer_address_street1' => $details->PayerInfo->Address->Street1 ?? null,
                    'payer_address_street2' => $details->PayerInfo->Address->Street2 ?? null, //dump($details->PayerInfo->Address))?null:,die())?null:,
                    'payer_city_name' => $details->PayerInfo->Address->CityName ?? null,
                    'payer_state_or_province' => $details->PayerInfo->Address->StateOrProvince ?? null,
                    'payer_postal_code' => $details->PayerInfo->Address->PostalCode ?? null,
                    'payer_country' => $details->PayerInfo->Address->Country ?? null,
                    'payer_country_name' => $details->PayerInfo->Address->CountryName ?? null,
                    'payer_phone' => $details->PayerInfo->Address->Phone ?? null,
                    'transaction_id' => $details->PaymentInfo->TransactionID ?? null,
                    'ebay_transaction_id' => $details->PaymentInfo->EbayTransactionID ?? null,
                    'parent_transaction_id' => $details->PayerInfo->PayerID ?? null,
                    'payment_type' => $details->PaymentInfo->PaymentType ?? null,
                    'payment_date' => strtotime($details->PaymentInfo->PaymentDate) ?? null,
                    'currency_code' => $details->PaymentInfo->GrossAmount->currencyID ?? null,
                    'gross_amount' => $details->PaymentInfo->GrossAmount->value ?? null,
                    'fee_amount' => $details->PaymentInfo->FeeAmount->value ?? null,
                    'settle_amount' => $details->PaymentInfo->SettleAmount->value ?? null,
                    'tax_amount' => $details->PaymentInfo->TaxAmount->value ?? null,
                    'exchange_rate' => $details->PaymentInfo->ExchangeRate ?? null,
                    'payment_status' => $details->PaymentInfo->PaymentStatus ?? null,
                    'pending_reason' => $details->PaymentInfo->PendingReason ?? null,
                    'invoice_id' => $details->PaymentItemInfo->InvoiceID ?? null,
                    'memo' => $details->PaymentItemInfo->Memo ?? null,
                    'sales_tax' => $details->PaymentItemInfo->SalesTax ?? null,
                    'payer_status' => $details->PayerInfo->PayerStatus ?? null,
                    'subject' => $details->PaymentInfo->Subject ?? null,
                    'buyer_id' => $details->PaymentItemInfo->Auction ?? null,
                    'items' => $items,
                ];

                $data = [
                    'platform' => 1,
                    'internal_transaction_id' => $internal_transaction_id,
                    'paypalorder' => $pp_order,
                    'status' => 1,
                ];
                // dump($pp_order['items']);
                $order = OrderFacade::createPayPalOrder($data);
                dump('创建第'.($key + 1).'订单记录');
            }

            // todo ...写入数据库
            $percent = ceil((($key + 1) / $total) * 100);
            dump('已完成:'.$percent.'%');
            dump('------------------');
            // Cache::store('redis')->set('syncstatus', $percent);
            // array_splice($transactionids, 0, 1);

            // if ($key === $total - 1) {
            //     // Cache::store('redis')->set('syncstatus', 0);
            //     // Cache::store('redis')->delete($tids);
            // }
        }

        dump('同步已完成');
        Cache::store('redis')->delete($account_tids);
        Cache::store('redis')->delete($account_end_currentday);
    }
}
