<?php

// 应用公共文件

/**
 * 方法用途描述
 * 描述：用于处理API访问时返回数组.
 *
 * @param $array 响应的数据
 *
 * @return [] 处理后返回对象
 * @date 2017年11月2日下午4:38:58
 */
function resultResponse($array)
{
    return [
        'code' => 20000,
        'data' => $array['data'],
        'time' => $_SERVER['REQUEST_TIME'],
    ];
}

 /**
  * 生成唯一的uuid值
  *
  * @param  int $lenght 生成的uuid长度
  *
  * @return
  */
 function uniqueReal($lenght = 13)
 {
     if (function_exists('random_bytes')) {
         $bytes = random_bytes(ceil($lenght / 2));
     } elseif (function_exists('openssl_random_pseudo_bytes')) {
         $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
     } else {
         throw new Exception('no cryptographically secure random function available');
     }

     return substr(bin2hex($bytes), 0, $lenght);
 }
