<?php
/**
 * Created by PhpStorm.
 * User: Rick
 * Date: 2019/8/5
 * Time: 11:00
 */

namespace app\admin\controller;
use app\admin\model\Head;
use app\admin\model\Navigation;
use app\admin\model\SonNavigation;
use app\admin\model\site;
use think\Controller;
use think\Db;

class Basics extends  Controller
{

    /**
     * 站点信息设置
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function site()
    {
        if (request()->method() == 'POST'){
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('logo');
            $arr = request()->post();
            $data['title'] = trim($arr['title']);
            $data['keywords'] = trim($arr['Keyword']);
            $data['description'] = trim($arr['describe']);
            $data['footer'] = trim($arr['footer']);
            $data['pubid'] = session('user')['id'];
            $data['pubname'] = session('user')['user'];
            $data['pubtime'] = time();
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->move(ROOT_PATH . 'public/static' . DS . 'uploads');
                if($info){
                    $logo = $info->getSaveName();
                    $data['logo'] = $logo;

                }else{
                    // 上传失败获取错误信息
                    $this->error($file->getError());
                }
            }
            $ret = Db::name('site')->where('id',1)->update($data);
            if ($ret >= 1){
                $this->success("配置成功");
            }else{
                $this ->success('配置失败');
            }
        }else{
            $data = Db::name('site')->find();
            $this->assign('data',$data);
            return view();

        }
    }

    /**
     * 公司信息配置
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function introduce()
    {
        if (request()->method() == "POST"){
            $req = request()->post();
            $data['title'] = trim($req['title']);
            $data['code'] = intval($req['code']);
            $data['introduce'] = trim($req['introduce']);
            $data['contact'] = trim($req['contact']);
            $data['phone'] = trim($req['phone']);
            $data['number'] = trim($req['number']);
            $data['wechat'] = trim($req['wechat']);
            $data['email'] = trim($req['email']);
            $data['address'] = trim($req['title']);
            $data['pubid'] = session('user')['id'];
            $data['pubname'] = session('user')['user'];
            $data['pubtime'] = time();
            $ret = Db::name('company')->where('id',1)->update($data);
            if ($req >= 1){
                $this->success('配置成功');
            }else{
                $this->error('配置失败');
            }
        }else{
            $data = Db::name('company')->find();
            $this->assign('data',$data);
            return view();
        }
    }

    /**
     * 导航信息列表 | 配置父导航状态
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function navigation()
    {

        if (request()->method() == 'POST'){
            $req = request()->post();
            $data['isdelete'] = $req['state'];
            $data['pubid'] = session('user')['id'];
            $data['pubname'] = session('user')['user'];
            $data['pubtime'] = time();
            $rtr = Db::name('type')->where('id',intval($req['id']))->update($data);
            if ($rtr >= 1){
                $json = ajax(200,'配置成功',$rtr);
            }else{
                $json = ajax(500,'配置失败');
            }
            return $json;
        }else{
            $list = Db::name('type')->where('isdelete','>',0)->order('sortd','asc')->select();
            $this->assign('list',$list);
            return view();
        }

    }

    /**
     * 添加栏目
     */
    public function navigation_add()
    {
        $arr = request()->post();
        $data['title'] = trim($arr['title']);
        $data['parentid'] = intval($arr['parentid']);
        $data['sortd'] = intval($arr['sortd']);
        $data['sel'] = intval($arr['sel']);
        $data['pubid'] = session('user')['id'];
        $data['pubname'] = session('user')['user'];
        $data['pubtime'] = time();
        $result = Db::name('type')->insert($data);
        if ($result && $result >= 1){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }


    /**
     * 返回一条父导航信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOneNavigationInfo()
    {
         $id  = request()->post('id');
         $str = Db::name('type')->where('id',intval($id))->find();
         if (!empty($str)){
             $json = ajax(200,'返回成功',$str);
         }else{
             $json = ajax(400,'未获取当前数据');
         }
         return $json;
    }

    /**
     * 编辑导航信息
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editNavigationInfo()
    {
        $req = request()->post();
        $data['title']  = $req['title'];
        $data['sortd']  = $req['sortd'];
        $str = Db::name('type')->where('id',intval($req['id']))->update($data);
        if (!empty($str)){
            $json = ajax(200,'编辑成功',$str);
        }else{
            $json = ajax(400,'编辑失败');
        }
        return $json;
    }

    /**
     * 配置子导航状态
     * @return \think\response\Json
     */
    public function sonNavigationState()
    {
        $req = request()->post();
        $rtr = SonNavigation::sonNavigationState($req['id'],$req['sonState']);
        if ($rtr >= 1){
            $json = ajax(200,'配置成功',$rtr);
        }else{
            $json = ajax(500,'配置失败');
        }
        return $json;
    }

    /**
     * 删除导航
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function navigationDelete()
    {
        $req = request()->post('id');
        $str = Db::name('type')->where('id',intval($req))->update(['isdelete'=>0]);
        if ($str >= 1){
            $json = ajax(200,'删除成功');
        }else{
            $json = ajax(500,'删除失败');
        }
        return $json;
    }

    /**
     * 返回一条父导航信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOneSonNavigationInfo()
    {
        $id  = request()->post('id');
        $str = SonNavigation::getOneSonNavigationInfo($id);
        if (!empty($str)){
            $json = ajax(200,'返回成功',$str);
        }else{
            $json = ajax(400,'未获取当前数据');
        }
        return $json;
    }

    /**
     * 编辑父导航信息
     * @return \think\response\Json
     */
    public function editSonNavigationInfo()
    {
        $req = request()->post();
        $str = SonNavigation::editSonNavigationInfo($req['id'],$req);
        if (!empty($str)){
            $json = ajax(200,'编辑成功',$str);
        }else{
            $json = ajax(400,'编辑失败');
        }
        return $json;
    }



}