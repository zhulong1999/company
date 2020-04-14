<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2019/8/12
 * Time: 10:03
 */
namespace app\admin\controller;
use app\admin\model\Friendship;
use app\admin\model\Wheelimage;
use think\Controller;
use think\Db;

/**
 * 扩展相关类
 * Class Extend
 * @package app\admin\controller
 */
class Extend extends Controller
{
    /**
     * 显示轮播图和上传
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wheelimage()
    {
        if (request()->method() == "POST"){
            $arr = request()->post();
            $data['title'] = trim($arr['title']);
            $data['sortd'] = intval($arr['sortd']);
            $data['type'] = intval($arr['type']);
            if(intval($arr['type']) == 1) {
                $data['images'] = trim($arr['images']);
            }
            $data['pubid'] = session('user')['id'];
            $data['pubname'] = session('user')['user'];
            $data['pubtime'] = time();
            $result = Db::name('banner')->insert($data);
            if ($result >= 1){
                $this->success("配置成功");
            }else{
                $this ->error('配置失败');
            }
        }else{
            $where['isdelete']=1;
            $where['type'] = 1;
            $this->assign('list',Db::name('banner')->where($where)->select());
            return view();
        }

    }

    /**
     * 删除轮播图
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delWheel()
    {
        $id  = request()->post('id');
        $ret = Db::name('banner')->where('id',intval($id))->update(['isdelete'=>0]);
        if ($ret >= 1){
            $json = ajax(200,'删除成功',$ret);
        }else{
            $json = ajax(500,'删除失败',$ret);
        }
        return $json;
    }

    /**
     * 友情链接操作
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function friendship()
    {
        if (request()->method() == 'POST'){
            $req = request()->post();
            $arr = request()->post();
            $data['title'] = trim($arr['title']);
            $data['sortd'] = intval($arr['sortd']);
            $data['link'] = trim($arr['link']);
            $data['type'] =2;
            $data['pubid'] = session('user')['id'];
            $data['pubname'] = session('user')['user'];
            $data['pubtime'] = time();
            $ret = Db::name('banner')->insert($data);
            if ($ret >= 1){
                $this->success("添加成功");
            }else{
                $this ->error('添加失败');
            }
        }else{
            $where['isdelete']=1;
            $where['type'] = 2;
            $this->assign('list',Db::name('banner')->where($where)->select());
            return view();
        }

    }

    /**
     * 删除轮播图
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delFriendship()
    {
        $id  = request()->post('id');
        $ret = Db::name('banner')->where('id',intval($id))->update(['isdelete'=>0]);
        if ($ret >= 1){
            $json = ajax(200,'删除成功',$ret);
        }else{
            $json = ajax(500,'删除失败',$ret);
        }
        return $json;
    }
}