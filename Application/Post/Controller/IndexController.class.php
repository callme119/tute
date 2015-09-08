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
        $url=array(
            "addPost"=>U('addPost'),
            "editPost"=>U('editPost'),
            );
        $this->assign('url',$url);
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
     public function saveOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    public function addOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    
}
