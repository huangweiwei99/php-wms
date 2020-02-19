<?php

namespace app\service;

use SoapClient;
use think\facade\Config;

class SFC
{
    protected static $base_url_static = 'http://www.sendfromchina.com/ishipsvc/web-service?wsdl';

    protected static function soap_req_static($service, $req_data)
    {
        $client = new SoapClient(self::$base_url_static);
        $auth = [
            'HeaderRequest' => Config::get('api.sfc_config_vson_mail'),
        ];
        try {
            switch ($service) {
            case 'searchOrder':
                $req_data = ['searchOrderRequestInfo' => $req_data];
                $params = \array_merge($auth, $req_data);
                $result = $client->searchOrder($params);
                break;
            case 'getRatesByType':// 三态货运方式类型查询运费规则
                $req_data = ['ratesRequestInfo' => $req_data];
                $params = \array_merge($auth, $req_data);
                $result = $client->getRatesByType($params);
                break;
            case 'getShipTypes':// 三态货运方式代号
                $req_data = [];
                $params = \array_merge($auth, $req_data);
                $result = $client->getShipTypes($params);
                break;
            case 'getCountries'://外部网络可查询我们系统所支持的邮寄区域
                $req_data = [];
                $params = \array_merge($auth, $req_data);
                $result = $client->getCountries($params);
                break;
            case 'getRates':// 目的地与重量查询我们系统中的可用的运费规则
                $req_data = ['ratesRequestInfo' => $req_data];
                $params = \array_merge($auth, $req_data);
                $result = $client->getRates($params);
                break;
            case 'addOrder':// 目的地与重量查询我们系统中的可用的运费规则
                $req_data = ['addOrderRequestInfo' => $req_data];
                $params = \array_merge($auth, $req_data);
                $result = $client->addOrder($params);
                break;
            default:
                // code...
                break;
        }

            return $result;
        } catch (SoapFault$e) {
            echo "Sorry an error was caught executing your request:{$e->getMessage()}";
        }
    }

    public function response($service, $data)
    {
        $response = self::soap_req_static($service, $data);

        return $response;
    }
}
