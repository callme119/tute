<?php
/**
 * Description of IndexController
 *
 * @author xuao 295184686@qq.com
 */

namespace Post\Controller;
use Admin\Controller\AdminController;
use Post\Model\PostModel;
class IndexController extends AdminController
{
    /**
     * [indexAction 岗位管理列表显示]
     * @return [type] []
     */
    public function indexAction()
    {
        //获取岗位列表
        $postModel = new PostModel;
        $postList = $postModel -> getPostList();
        $url=array("deletePost"=>U('deletePost'),"editPost"=>U('editPost'));
        $postList = add_url($postList,'_url',$url,'id');

        $this ->assign('totalCount',$postModel->getTotalCount());
        //传值
        $this->assign('postList',$postList);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
     * [addPostAction 调用添加岗位页面]
     */
    public function addPostAction(){
        //设置要提交的url
        $subUrl = U('save');
        $this ->assign('subUrl',$subUrl);

        //调用 界面
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
     * [editPostAction 调用编辑岗位页面]
     * @return [type] [description]
     */
    public function editPostAction(){
        $id = I('get.id');
        //获取当前岗位信息
        $postModel = new PostModel;
        $postInfo = $postModel -> getPostInfoById($id);
        $this -> assign('postInfo',$postInfo);

        //设置要提交的url
        $subUrl = U('update?id='.$id);
        $this ->assign('subUrl',$subUrl);

        //传值到前台
        $this->assign('postInfo',$postInfo);
        $this->assign('YZBODY',$this->fetch('addPost'));
        $this->display(YZTemplate);
    }
    //删除岗位
    public function deletePostAction(){
        $id = I('get.id');
        $postModel = new PostModel;
        $state = $postModel ->deletePostById($id);
        if($state){
            $url = U('index');
            $this->success('删除成功',$url);
        }
    }
    //添加保存
     public function saveAction(){
        $postModel = new PostModel;
        $state = $postModel ->addPost();
        if($state){
            $url = U('index');
            $this->success('保存成功',$url);
        }
        
    }
    //编辑保存
    public function updateAction(){
        $postModel = new PostModel;
        $state = $postModel ->updatePost();
        if($state){
            $url = U('index');
            $this->success('保存成功',$url);
        }
    }
    
}
