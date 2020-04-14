<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2019/8/7
 * Time: 17:38
 */
namespace app\admin\controller;
use app\admin\model\Artseo;
use app\admin\model\SonNavigation;
use think\Controller;
use think\Db;
use think\Model;
use think\Session;

/**
 * 文章操作类
 * Class Article
 * @package app\admin\controller
 */
class Article extends Controller
{

    /**
     * 文章列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function articlelist()
    {
        $info = trim(request()->get('info'));

        $list = Db::name('article')->order('aid','desc')
            ->alias('a')
            ->where('a.isdelete','>',0)
            ->where('a.title','like',"%$info%")
            ->join('type b','a.typeid = b.id')
            ->field('aid,b.id,a.title,b.title as typetitle,a.isdelete,a.pubtime,a.pubname,a.pubid,a.sortd,a.keywords,images')
            ->paginate(10,false,['query'=>['info'=>$info]]);



        $this->assign('list', $list);
        $this->assign('page',$list->render());
        return view();
//        dump($list);
    }

    /**
     * 查看指定文章内容
     * @return \think\response\View
     */
    public function showonearticeinfo()
    {
        $this->assign('content',Db::name('article')->where('aid',intval(request()->get('id')))->value('content'));
        return view();
    }

    /**
     * 发布文章
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function addarticleinfo()
    {
        if (request()->method() == 'POST'){
            $req = request() ->param();
            $info = array(
                "typeid" => intval($req['head']['typeid']),
                "parentid" =>1,
                "title" => trim($req['head']['title']),
                "sortd" => trim($req['head']['sortd']),
                "images" => trim($req['headImage']),
                "content" => trim($req['content']),
                'keywords'=>trim($req['head']['keywords']),
                'description' => trim($req['head']['description']),
                'pubid' => session('user')['id'],
                'pubname' => session('user')['user'],
                'pubtime'=> time(),
            );
            $ret = Db::name('article')->insert($info);
            if ($ret >= 1){
                $json = ajax(200,'发布成功');
            }else{
                $json = ajax(500,'发布失败！');
            }
            return $json;
        }else{

            $this->assign('SonNavigationList',Db::name('type')->where('isdelete',1)->field('id,title,parentid')->select());//渲染导航
            return view();
        }
    }

    /**
     * 高级设置 选择文章
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selArticle()
    {
        //高级设置 选择文章
        $id = request()->post('id');
        $ret = \app\admin\model\Article::getTypeArticleInfo($id);
        if (!empty($ret)){
            $json = ajax(200,'返回成功',$ret);
        }else{
            $json = ajax(400,'暂无数据');
        }
        return $json;
    }

    /**
     * 通过文章id返回文章SEO信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleKey()
    {
        $id = request()->post('id');
        $ret = Artseo::getOneArticleKey($id);
        if (!empty($ret)){
            $json = ajax(200,'返回成功',$ret);
        }else{
            $json = ajax(400,'暂无数据');
        }
        return $json;
    }

    /**
     * 设置 文章SEO
     */
    public function seniorSet()
    {
        $req = request()->post();
        $ret = Artseo::setArticleKey($req['id'],$req);;
        if ($ret >= 1){
            $this->success('设置成功',null,null,1);
        }else{
            $this->error('设置失败',null,null,1);
        }
    }

    /**
     * 配置文章状态
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setArticleState()
    {
        $req = request()->post();
        $rtr = Db::name('article')->where('aid',intval($req['id']))->update(['isdelete'=>intval($req['articleState'])]);
        if ($rtr >= 1){
            $json = ajax(200,'配置成功',$rtr);
        }else{
            $json = ajax(500,'配置失败');
        }
        return $json;
    }

    /**
     * 删除文章
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function articleDelete()
    {
        $id = request()->post('id');
        $str = Db::name('article')->where('aid',intval($id))->update(['isdelete'=>0]);
        if ($str >= 1){
            $json = ajax(200,'删除成功');
        }else{
            $json = ajax(500,'删除失败');
        }
        return $json;

    }


    /**
     * 修改文章
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function editonearticeinfo()
    {
        if (request()->method() == 'POST'){
            $req = request() ->param();
            $info = array(
                "title" => trim($req['head']['title']),
                'typeid' => intval($req['head']['typeid']),
                "keywords" => trim($req['head']['keywords']),
                "description" =>trim( $req['head']['description']),
                "sortd" => intval($req['head']['sortd']),
                "images" => trim($req['images']),
                "content" => trim($req['content']),
                'pubid' => session('user')['id'],
                'pubname' => session('user')['user'],
                'pubtime'=> time(),

            );
            $ret =  Db::name('article')->where('aid',intval($req['id']))->update($info);
            if ($ret >= 1){
                $json = ajax(200,'发布成功');
            }else{
                $json = ajax(500,'发布失败！');
            }
            return $json;
        }else{
            $where['aid'] = intval(request()->get('id'));
            $this->assign('SonNavigationList',Db::name('type')->where('isdelete',1)->select());
            $data = Db::name('article')->where($where)->find();
            $this->assign('vo',$data);
            return view();
        }

    }

    /**
     * 批量删除文章
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function batchDelete()
    {
        $id = input('id/a');
        $ret = Db::name('article')->whereIn('aid',$id)->update(['isdelete'=>0]);

        if ($ret >= 1){
            $json = ajax(200,'删除成功',$ret);
        }else{
            $json = ajax(500,'删除失败');
        }
        return $json;

    }


    public function up()
    {
        $file = request()->file('file');
        if($file){
            $info = $file->move(ROOT_PATH . 'public/static' . DS . 'uploads');
            if($info){
                $logo = '/static/uploads/'.$info->getSaveName();
                $data =  array(
                    'errno'=>0,
                    'data'=>[$logo]
                );
                return json_encode($data);
//                return ajax(200,'上传成功','/static/uploads/'.$logo);
            }else{
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }

    }

}

?>