<?php
/**
 * Description of IndexController
 *
 * @author xuao
 */

namespace Post\Controller;
use Admin\Controller\AdminController;
use Post\Model\PostModel;
class IndexController extends AdminController
{
    public function indexAction()
    {
        $postModel = new PostModel;
        $postList = $postModel -> getPostList();
        $url=array("deletePost"=>U('deletePost'),"editPost"=>U('editPost'));
        $postList = add_url($postList,'_url',$url,'id');

        $this->assign('postList',$postList);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addPostAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function editPostAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
     public function saveAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    public function updateAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    
}
