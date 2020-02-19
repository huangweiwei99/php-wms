<?php

namespace app\service;

use think\facade\Config;

class BFE
{
    protected static $base_url = 'http://demo.chukou1.cn/v3/';

    protected function get_pricing_params_string($params, $name)
    {
        //echo "<pre>";
        //print_r($params);
        //echo "</pre>";
        $result = '';
        foreach ($params as $item) {
            $result .= '&'.$name.'=';
            $result .= urlencode($item['product']).'*'.$item['count'];
        }
        //echo "<br>pricing params string is $result <br>";
        return $result;
    }

    protected function get_label_params_string($params, $name)
    {
        $result = '';
        foreach ($params as $item) {
            $result .= '&'.$name.'=';
            $result .= urlencode($item);
        }
        //echo "<br>label params string is $result <br>";
        return $result;
    }

    protected function get_repeat_string_params_string($params, $name)
    {
        $result = '';
        foreach ($params as $item) {
            $result .= '&'.$name.'=';
            $result .= urlencode($item);
        }
        //echo "<br>label params string is $result <br>";
        return $result;
    }

    protected static function rest_helper_static($dispatcher, $params = null, $verb = 'GET', $format = 'json', $bulid_query = true)
    {
        $url = self::$base_url.join('/', $dispatcher);

        $cparams = [
            'http' => [
            'method' => $verb,
            'ignore_errors' => true,
            ],
        ];
        if (null !== $params) {
            $auth = Config::get('api.bfe_config_vson_mail');

            $params = \array_merge($auth, $params);
            if ($bulid_query) {
                $params = http_build_query($params);
            }
            if ('POST' == $verb) {
                $cparams['http']['header'] = "Content-type: application/x-www-form-urlencoded\r\n"
                .'Content-Length: '.strlen($params)."\r\n";
                $cparams['http']['content'] = $params;
            } else {
                $url .= '?'.$params;
            }
        }

        $context = stream_context_create($cparams);

        $fp = fopen($url, 'rb', false, $context);
        if (!$fp) {
            $res = false;
        } else {
            // If you're trying to troubleshoot problems, try uncommenting the
            // next two lines; it will show you the HTTP response headers across
            // all the redirects:
            // $meta = stream_get_meta_data($fp);
            // var_dump($meta['wrapper_data']);
            $res = stream_get_contents($fp);
        }

        if (false === $res) {
            throw new Exception("$verb $url failed: $php_errormsg");
        }

        switch ($format) {
            case 'json':
            $r = json_decode($res);
            if (null === $r) {
                throw new Exception("failed to decode $res as json");
            }

            return $r;

            case 'xml':
            $r = simplexml_load_string($res);
            if (null === $r) {
                throw new Exception("failed to decode $res as xml");
            }

            return $r;
        }

        return $res;
    }

    protected static function request_static($servername = '', $request_data = [])
    {
        switch ($servername) {
            case 'express-list-all-service':
                $req = [
                    'dispatcher' => [
                        'category' => 'direct-express',
                        'handler' => 'misc',
                        'action' => 'list-all-service',
                    ],
                    'method' => 'GET',
                ];

                break;
            case 'express-pricing-all':
                $req = [
                    'dispatcher' => [
                        'category' => 'direct-express',
                        'handler' => 'package',
                        'action' => 'compare-charge',
                    ],
                    'method' => 'GET',
                ];

                break;
            case 'express-pricing':
                $req = [
                    'dispatcher' => [
                        'category' => 'direct-express',
                        'handler' => 'package',
                        'action' => 'pricing',
                    ],
                    'method' => 'GET',
                ];

                break;
            case 'get-tracking':
                $req = [
                        'dispatcher' => [
                            'category' => 'system',
                            'handler' => 'tracking',
                            'action' => 'get-tracking',
                        ],
                        'method' => 'GET',
                    ];

                    break;
            case 'list-all-service':
                $req = [
                    'dispatcher' => [
                        'category' => 'outbound',
                        'handler' => 'misc',
                        'action' => 'list-all-service',
                    ],
                    'method' => 'GET',
                    ];

                    break;
            case 'list-pending':
                $req = [
                    'dispatcher' => [
                        'category' => 'direct-express',
                        'handler' => 'package',
                        'action' => 'list-pending',
                    ],
                    'method' => 'GET',
                    ];

                    break;
            case 'list-product-stock':
                $req = [
                    'dispatcher' => [
                        'category' => 'product',
                        'handler' => 'stock',
                        'action' => 'list-product-stock',
                    ],
                    'method' => 'GET',
                    ];

                    break;
            case 'outbound-pricing-all':
                $req = [
                    'dispatcher' => [
                        'category' => 'outbound',
                        'handler' => 'package',
                        'action' => 'pricing-all',
                    ],
                    'method' => 'GET',
                    ];

                    break;
            case 'outbound-pricing':
                $req = [
                    'dispatcher' => [
                        'category' => 'outbound',
                        'handler' => 'package',
                        'action' => 'pricing',
                    ],
                    'method' => 'GET',
                    ];

                    break;
            case 'pricing-for-sku':
                $req = [
                    'dispatcher' => [
                        'category' => 'outbound',
                        'handler' => 'package',
                        'action' => 'pricing-for-sku',
                    ],
                    'method' => 'GET',
                    ];

                    break;
            case 'query-exchange-rate':
                $req = [
                    'dispatcher' => [
                        'category' => 'direct-express',
                        'handler' => 'misc',
                        'action' => 'query-exchange-rate',
                    ],
                    'method' => 'GET',
                    ];

                    break;

                    default:
                // code...
                break;
        }

        return  array_merge(['data' => $request_data], $req);
    }

    public function response($service, $data)
    {
        try {
            $req_params = self::request_static($service, $data);
            $response = self::rest_helper_static($req_params['dispatcher'], $req_params['data'], $req_params['method']);

            return $response;
        } catch (exception $e) {
            echo $e;
        }
    }
}
