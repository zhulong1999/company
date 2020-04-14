<?php
/**
 * Created by PhpStorm.
 * User: Rick
 * Date: 2019/8/5
 * Time: 9:56
 */

namespace app\admin\controller;
use think\Controller;
use think\Session;

class Common extends  Controller
{
    /**
     * 检查是否登录
     */
    public function _initialize()
    {

        $isLogin = Session::get('user');
        if (empty($isLogin)){
            $this->success('请登录',url("User/login"));
        }
    }

    public function common()
    {

        return view();
    }
}