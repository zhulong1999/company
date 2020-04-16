<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/13 0013
 * Time: 10:32
 */
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends  Controller
{
    /**
     * 公共调用部分
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        //头部导航
        $wheretype['isdelete'] = 1;
        $wheretype['parentid'] = 0;
        $type = Db::name('type')->where($wheretype)->field('id,title,sel,parentid')->select();
        $this->assign('type', $type);
        //轮播图
        $wherebanner['isdelete'] = 1;
        $wherebanner['type'] = 1;
        $banner = Db::name('banner')->where($wherebanner)->field('id,images,title')->select();
        $this->assign('banner', $banner);
        //底部信息
        $this->assign('company', Db::name('company')->find());
        $this->assign('site', Db::name('site')->find());
    }

    /**
     * 首页
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //企业简介
        $where_qiye_jianjie['isdelete'] = 1;
        $where_qiye_jianjie['parentid'] = 2;
        $qiye_jianjie = Db::name('article')->where($where_qiye_jianjie)->field('aid,title,content,parentid')->order('sortd', 'desc')->limit(5)->select();
        $this->assign('qiye_jianjie', $qiye_jianjie);
        //精品展示
        $where_jingping_zhanshi['isdelete'] = 1;
        $where_jingping_zhanshi['typeid'] = 20;
        $jingping_zhanshi = Db::name('article')->where($where_jingping_zhanshi)->field('aid,images,title')->order('sortd', 'desc')->limit(8)->select();
        $this->assign('jingping_zhanshi', $jingping_zhanshi);
        //行业动态
        $where_hangye_dongtai['isdelete'] = 1;
        $where_hangye_dongtai['parentid'] = 4;
        $hangye_dongtai = Db::name('article')->where($where_hangye_dongtai)->order('sortd', 'desc')->field('aid,parentid,title,pubtime,typeid')->select();
        $this->assign('hangye_dongtai', $hangye_dongtai);
        $wherelink['isdelete'] = 1;
        $wherelink['type'] = 2;
        //友情链接
        $link = Db::name('banner')->where($wherelink)->order('sortd', 'desc')->field('id,title,link')->select();
        $this->assign('link', $link);
        return view('/index');
    }

    /**
     * 内容页
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function about()
    {
        $tid = request()->param('tid');
        $this->assign('tid', $tid);
        //分类
        $parent_type = Db::name('type')->where("isdelete = 1 and parentid=$tid")->order('sortd', 'desc')->field('id,title')->select();
        $this->assign('parent_type', $parent_type);
        $aid = request()->param('aid');
        if (empty($aid)) {
            $article = Db::name('article')->where("isdelete = 1 and parentid=$tid")->order('sortd', 'desc')->find();
        } else {
            $article = Db::name('article')->where("isdelete = 1 and typeid=$aid")->order('sortd', 'desc')->find();
        }
        $this->assign('data', $article);
        $this->assign('aid', $aid);
        return view('/about');
    }

    /**
     * 图片列表页
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function imglist()
    {
        $tid = request()->param('tid');
        $fist_type = Db::name('type')->where("isdelete=1 and parentid=$tid")->order('sortd', 'desc')->value('id');
        $this->assign('tid', $tid);
        //分类
        $parent_type = Db::name('type')->where("isdelete=1 and parentid=$tid")->order('sortd', 'desc')->field('id,title')->select();
        $this->assign('parent_type', $parent_type);
        $aid = intval(request()->param('aid'));
        if (empty($aid)) {
            $article = Db::name('article')->where("isdelete=1 and typeid=$fist_type")->order('sortd', 'desc')->paginate(9);
            $type_title = Db::name('type')->where("isdelete = 1 and parentid=$tid")->value('title');
        } else {
            $article = Db::name('article')->where("isdelete = 1 and typeid=$aid")->order('sortd', 'desc')->paginate(9);
            $type_title = Db::name('type')->where("isdelete = 1 and id=$aid")->value('title');
        }

        $this->assign('data', $article);
        $this->assign('page', $article->render());
        $this->assign('aid', $aid);
        $this->assign('type_title', $type_title);
        return view('/imglist');
    }

    /**
     * 列表页
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _list()
    {
        $tid = request()->param('tid');
        $aid = intval(request()->param('aid'));
        $this->assign('tid', $tid);
        $this->assign('aid', $aid);
        //默认选择
        $fist_type = Db::name('type')->where("isdelete = 1 and parentid = $tid")->order('sortd', 'desc')->value('id');
        //分类
        $parent_type = Db::name('type')->where("isdelete=1 and parentid =$tid")->order('sortd', 'desc')->select();
        $this->assign('parent_type', $parent_type);
        if (empty($aid)) {
            $list = Db::name('article')->where("isdelete = 1 and typeid = $fist_type")->order('sortd', 'desc')->paginate(10);
            $type_title = Db::name('type')->where("isdelete = 1 and parentid = $tid")->value('title');
        } else {
            $list = Db::name('article')->where("isdelete = 1 and typeid = $aid")->order('sortd', 'desc')->paginate(10);
            $type_title = Db::name('type')->where("isdelete = 1 and id = $aid")->value('title');
        }
        $this->assign('page', $list->render());
        $this->assign('type_title', $type_title);
        $this->assign('list', $list);
        return view('/list');
    }

    /**
     * 文章详细页
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info()
    {
        $aid = request()->param('aid');
        $data = Db::name('article')->where("isdelete = 1 and aid = $aid")->find();
        $this->assign('data', $data);
        //访问量
        $aa = Db::name('article')->where("isdelete = 1 and aid = $aid")->setInc('num', rand(1, 9));
        //获取下一篇
        $aid =$aid+1;
        $this->assign('push',Db::name('article')->where("isdelete=1 and aid=".$aid)->find());
        //上一篇
        $aid = $aid - 2;
        $this->assign('jian',Db::name('article')->where("isdelete=1 and aid=".$aid)->find());
        if ($data['artype'] == 2){
            return view('/msg');

        }else{
            return view('/info');

        }
    }



    /**
     * 留言
     * @return \think\response\Json
     */
    public function ly()
    {
        $arr = request()->post();
        $data['name'] = trim($arr['name']);
        $data['phone'] = intval($arr['phone']);
        $data['em'] = trim($arr['em']);
        $data['address'] = trim($arr['address']);
        $data['info'] = trim($arr['info']);
        $data['ip'] = request()->server()['REMOTE_ADDR'];
        $data['time'] = time();
        if ($data['phone'] == 0 && strlen($data['phone']) != 11) return ajax(300,'手机号码有误!');
        if ($data['info'] == '') return ajax(300,'请输入留言');
        $result = Db::name('liuyan')->insert($data);
        if ($result && $result == 1){
            $json = ajax(200,'留言成功');
        }else{
            $json = ajax(500,'留言失败');
        }
        return $json;
    }

    public function demo()
    {
        echo strtotime(date('Y-m-d H:i:s'));
    }

}