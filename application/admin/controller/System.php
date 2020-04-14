<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2019/8/7
 * Time: 16:43
 */
    namespace app\admin\controller;
    use think\Controller;

    /**
     * 系统配置类
     * Class System
     * @package app\admin\controller
     */
    class System extends Controller
    {
        public function serverinfo()
        {
            return view();
        }
        public function aa()
        {
            dump();
        }
    }

?>