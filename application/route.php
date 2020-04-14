<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

////首页
//Route::get('index','Index/index');
//内容页
Route::get('/about','Index/about');
//图片列表页
Route::get('/imglist','Index/imglist');
//列表页
Route::get('/list','Index/_list');
//文章信息页
Route::get('/info','Index/info');
//
////案例展示
//Route::get('case','Showcase/caselist');



