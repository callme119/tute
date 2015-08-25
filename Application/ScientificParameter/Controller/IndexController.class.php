<?php
namespace ScientificParameter\Controller;
use Admin\Controller\AdminController;
use PublicProject\Model\PublicProjectModel;
class IndexController extends AdminController {
  public function indexAction(){

    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
  }
  //管理员进行公共项目添加的界面
  public function addAction() {
    $append = U('append');
    $this->assign('url',$append);
    $projectM = new \PublicProject\Model\PublicProjectModel();
    $project = $projectM->init();
    $this->assign('project',$project);
    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
  }
  public function addProjectAction() {
    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
  }
  public function projectSetAction() {
   $this->assign('YZBODY',$this->fetch());
   $this->display(YZTemplate);
 }
 public function projectManageAction() {
   $this->assign('YZBODY',$this->fetch());
   $this->display(YZTemplate);
 }
 /***
 * 保存，添加或修改之后，提交到该方法
 * 将获取到的Post数据传递给M层
 */
 public function saveAction()
 {
   $data = I('post.');
   $projectM = new \PublicProject\Model\PublicProjectModel();
   $state = $projectM->saveProject($data);
   if($state == "success"){
    $this->success('新增成功','add');
  }
}
  public function appendAction()
  {
    $pid = I('get.id');
    $projectM = new \PublicProject\Model\PublicProjectModel();
    $res = $projectM->append($pid);
    $this->assign('data',$res);
    
    $data = $this->fetch();
    echo $data;
    
    
  }
}