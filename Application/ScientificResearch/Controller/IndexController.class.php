<?php
namespace ScientificResearch\Controller;
use Admin\Controller\AdminController;
use PublicProject\Model\PublicProjectModel;
class IndexController extends AdminController {
    public function indexAction(){
        //$this->assign('YZBODY',$this->fetch(T('scientificResearch')));

        //$this->show('scientificResearch');
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function savedAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function auditedAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addAction() {
        $projectM = new PublicProjectModel();
        $project = $projectM->init();
        $this->assign('project',$project);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function auditprocessAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function submittedAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function auditsuggestionAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
 *  通过js传过来id，追加select的内容
 *  1.判断穿过来的id的type是否为0（如果为0还有子项目）
 *  2.如果type为0，pid=id取库 
 */
  public function appendAction()
  {
    $return = array('status' =>"" ,'data'=>"" );
    $id = I('get.id');

    $projectM = new PublicProjectModel();
    $type = $projectM->getTypeById($id);

    $pid = $id;//id作为pid取值
    $res = $projectM->append($pid);
    $this->assign('data',$res);
    $return['data'] = $this->fetch(T('ScientificParameter@Index/append'));
    $return['status'] = $type['type'];
    //echo $data;
    $this->ajaxReturn($return);
  
  }
}
