<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2019/8/6
 * Time: 17:11
 */
    namespace app\admin\controller;

    use think\captcha\Captcha;
    use think\Controller;
    use think\Db;
    use think\Session;

    /**
     * 用户相关类
     * Class User
     * @package app\admin\controller
     */
    class User extends Controller
    {

        /**
         * 管理员登录
         * @return \think\response\Json|\think\response\View
         * @throws \think\Exception
         */
        public function login()
        {

            if (request()->method() == 'POST'){
                $req = request()->post();
                $data['user'] = trim($req['user']);
                $data['pass'] = md5(trim($req['pass']));
                if (check_verify(trim($req['code']))){
                    $ret = Db::name('admin')->where($data)->value('id');
                    if (!empty($ret)){
                        $plus = Db::name('admin')->where($data)->setInc('num');
                        if ($plus >= 1){
                            $json = ajax(200,'登录成功');
                            Session::set('user',['id'=>$ret,'user'=>$data['user']]);
                        }else{
                            $json = ajax(500,'登录失败!');
                        }

                    }else{
                        $json = ajax(600,'用户名或密码有误!');
                    }
                }else{
                    $json = ajax(300,'验证码错误');
                }
                return $json;
            }else{
                return view();
            }
        }

        /**
         * 生成验证码
         * @return \think\Response
         */
        public function getYzm()
        {
            $captcha  = new Captcha();
            return $captcha->entry();
        }

        /**
         * 退出登录
         */
        public function outLogin()
        {
            Session::delete('user');
            $this->success('退出成功',url('User/login'));
        }

        /**
         * 管理员操作界面 | 管理员修改
         * @return \think\response\Json|\think\response\View
         * @throws \think\Exception
         * @throws \think\exception\PDOException
         */
        public function adminoperation()
        {
            if (request()->method() == 'POST'){
                $res = input('data/a');
                $where['user'] = $res['user'];
                $where['pass'] = md5($res['pass']);
                $isPass = Db::name('admin')->where($where)->value('id');
                if (!empty($isPass)){
                    $data = array(
                        'user' => $res['user'],
                        'pass' => md5($res['newpass']),
                        'ip' =>request()->server()['REMOTE_ADDR'],
                        'time'=>time(),
                    );
                    $ret = Db::name('admin')->where('user',$res['user'])->update($data);
                    if ($ret >= 1){
                        $json = ajax(200,'修改成功',$ret);
                    }else{
                        $json = ajax(500,'修改失败');
                    }
                }else{
                    $json = ajax(500,'密码错误');
                }
                return $json;
            }else{

                return view();
            }
        }



        /**
         * 返回当前管理员信息
         * @return \think\response\Json
         * @throws \think\db\exception\DataNotFoundException
         * @throws \think\db\exception\ModelNotFoundException
         * @throws \think\exception\DbException
         */
        public function getAdminInfo()
        {
            $ret = Db::name('admin')->where('user',trim(request()->post('user')))->find();
            if (!empty($ret)){
                $json = ajax(200,'返回成功',$ret);
            }else{
                $json = ajax(400,'当前管理员不存在');
            }
            return $json;
        }


        /**
         * 添加管理员信息
         * @return \think\response\Json
         */
        public function addAdminInfo()
        {
            $req = input('data/a');

            $isUser = Db::name('admin')->where('user',intval($req['adduser']))->value('id');
            if (empty($isUser)){
                $data = array(
                    'user' => $req['adduser'],
                    'pass' => md5($req['addnewpass']),
                    'ip' =>request()->server()['REMOTE_ADDR'],
                    'time'=>time(),
                );

                $ret = Db::name('admin')->insert($data);
                if ($ret >= 1){
                    $json = ajax(200,'添加成功',$ret);
                }else{
                    $json = ajax(400,'添加失败');
                }
            }else{
                $json = ajax(300,'该管理员账号已存在!');
            }
            return $json;
        }
    }

?>