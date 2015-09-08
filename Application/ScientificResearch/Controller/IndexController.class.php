<?php
namespace ScientificResearch\Controller;
use Admin\Controller\AdminController;
use PublicProject\Model\PublicProjectModel;
use User\Model\UserModel;
use UserDepartmentPost\Model\UserDepartmentPostModel;
use DepartmentPost\Model\DepartmentPostModel;
use Examine\Model\ExamineModel; //审核基础数据表
use PublicProjectDetail\Model\PublicProjectDetailModel;//教工添加公共细节表
use Score\Model\ScoreModel;//分值表
use CategoryOne\Model\CategoryOneModel;//数据模型类别一表
class IndexController extends AdminController {
    public function indexAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    /**
     *  保存数据步骤：
     *  1.判断数据模型类型
     *  2.根据数据模型类型先存类别表，如果没数据，返回错误；有数据返回类别id
     *  3.拼接类别的字符串
     *  4.存公共项目表，返回公共项目表的id
     *  5.存分数表
     * 
     */
    public function savedAction() {

        $PublicProjectDetailM = new PublicProjectDetailModel();
        $PublicProjectDetail = PublicProjectDetailM->
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function auditedAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addAction() {
        //获取当前用户ID
        $userId = get_user_id();

        //获取当前用户部门岗位信息（数组）
        $UserDepartmentPostM = new UserDepartmentPostModel();
        $userDepartmentPosts = $UserDepartmentPostM->getListsByUserId($userId);

        //获取当前岗位下，对应的可用审核流程
        $ExamineM = new ExamineModel();
        $examineLists = $ExamineM->getListsByNowPosts($userDepartmentPosts);

        $projectM = new PublicProjectModel();
        $project = $projectM->init();
        $nameM = new UserModel();
        $name = $nameM->getAllName();

        //传值
        $this->assign("examineLists",$examineLists);
        $this->assign('name',$name);
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
