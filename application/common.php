<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * JOSN格式返回
 * @param $status
 * @param $message
 * @param $data
 * @param int $time
 * @return \think\response\Json
 */
function ajax($status,$message,$data = null,$time = 1000)
{
    return json([
        'status' => $status,
        'message' => $message,
        'data' => $data,
        'time' => $time
    ]);
}

/**
 * 验证码 检查
 * @param $code
 * @param string $id
 * @return mixed
 */
function check_verify($code, $id = ''){
    $captcha = new \think\captcha\Captcha();
    return $captcha->check($code, $id);

}