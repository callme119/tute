<?php
/**
 * 科研项目
 */
namespace ScientificResearch\Controller;
use Admin\Controller\AdminController;
use ProjectCategory\Logic\ProjectCategoryLogic;         //项目类别
use ProjectCategory\Model\ProjectCategoryModel;         //项目类别表
use User\Model\UserModel;                               //用户表
use UserDepartmentPost\Model\UserDepartmentPostModel;   //用户部门岗位表
use DepartmentPost\Model\DepartmentPostModel;           //部门岗位表
use Examine\Model\ExamineModel;                         //审核基础数据表
use Project\Model\ProjectModel;                         //教工添加公共细节表
use Project\Logic\ProjectLogic;                         //项目表
use Score\Model\ScoreModel;                             //分值表
use Score\Logic\ScoreLogic;                             //项目分值 
use DataModel\Model\DataModelModel;                     //数据模型
use DataModel\Logic\DataModelLogic;                     //数据模型
use DataModelDetail\Logic\DataModelDetailLogic;         //数据模型详情
use DataModelDetail\Model\DataModelDetailModel;         //数据模型扩展信息
use ExamineDetail\Model\ExamineDetailModel;             //审核扩展信息
use Workflow\Model\WorkflowModel;                       //工作流表
use Workflow\Loigc\WorkflowLoigc;                       //工作流
use WorkflowLog\Model\WorkflowLogModel;                 //工作流扩展表
use ProjectCategoryRatio\Model\ProjectCategoryRatioModel;//项目类别系数表
use ProjectDetail\Logic\ProjectDetailLogic;             //项目扩展信息
use ProjectDetail\Model\ProjectDetailModel;             //项目扩展信息
use Workflow\Service\WorkflowService;                   //审核流程
use Cycle\Logic\CycleLogic;                             //周期表
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;            //项目类别系数

class IndexController extends AdminController {
    /**
     * 初始化
     * @return [type] [description]
     * panjie 
     * 3792535@qq.com
     */
    public function indexAction(){
        //取用户信息
        $userId = get_user_id();
        $type = CONTROLLER_NAME;
        
        //取分值分布\项目\类别等联合信息
        $ScoreL = new ScoreLogic();
        $projects= $ScoreL->getListsJoinProjectCategoryByUserIdType($userId , $type);
        $totalCount = $ScoreL->getTotalCount();

        //传值
        $this->assign("totalCount",$totalCount);
        $this->assign("projects",$projects);
        $this->assign('YZBODY',$this->fetch('Index/index'));
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
    public function saveAction() {

        $userId = get_user_id();

        //取当前周期id
        $cycleLogicL = new CycleLogic();
        $cycleLogic = $cycleLogicL->getCurrentList();
        $cycleId = $cycleLogic['id'];

        $projectCategoryId = I('post.project_category_id');
        //取项目类别信息
        $ProjectCategoryM = new ProjectCategoryModel();
        if( !$projectCategory = $ProjectCategoryM->getListById($projectCategoryId) )
        {
            $this->error = "please select projectCategory";
            $this->_empty();
        }

         //取数据模型扩展信息
        $dataModelId = (int)$projectCategory['data_model_id'];
        $DataModelDetailL = new DataModelDetailLogic();
        $dataModelDetailRoots = $DataModelDetailL->getRootListsByDataModelId($dataModelId);
        if($dataModelDetailRoots === false)
        {
            $this->error = $DataModelDetailL->getError();
            $this->_empty();
        }

        //存项目信息
        $ProjectM = new ProjectModel();
        $projectId = $ProjectM->save($userId,$cycleId);
        if($projectId === false)
        {
            $this->error = "数据添加发生错误，代码" . $this->getError();
            $this->_empty();
        }

        // 存工作流信息
        $examineId = (int)I('post.examine_id');
        $checkUserId = (int)I('post.check_user_id');
        $WorkflowS = new WorkflowService();
        if(!$WorkflowS->add($userId , $examineId , $projectId, $checkUserId , $commit = "申请"))
        {
            //删除项目信息
            $this->error = $WorkflowS->getError();
            $this->_empty();
        }

        //存分数信息,如果是团队信息，存团队，如果不是，存个人
        $ScoreM = new ScoreModel();
        $isTeam = $projectCategory['is_team'];
        if($isTeam == 1)
        {
            $Score = $ScoreM->save($projectId);
            if($Score === false)
            {
                $this->error = "数据添加发生错误，代码" . $ScoreM->getError();
                $this->_empty();
            }  
        }
        else
        {
            $ScoreL = new ScoreLogic();
            if(!$ScoreL->addByUserIdProjectIdScorePercent($userId , $projectId))
            {
                E("添加分数信息时发生错误，错误信息：" . $ScoreL->getError());
            }
        }
    
        if($dataModelId!==1){
            $projectDetailM = new projectDetailModel();
            $projectDetail = $projectDetailM->save($projectId,$dataModelDetailRoots);
            if($projectDetail === false)
            {
               $this->error = "数据添加发生错误，代码" . $this->getError();
               $this->_empty();

            }
        }
        
        $this->success("操作成功",'index');
    }
    public function auditedAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addAction() {
        //获取当前用户ID
        $userId = get_user_id();

        $ProjectCategoryL = new ProjectCategoryLogic();
        $projectCategoryTree = $ProjectCategoryL->getSonsTreeById($pid=0,$type=CONTROLLER_NAME);
        $projectCategory = tree_to_list($projectCategoryTree , $id , '_son' );

        //获取当前用户部门岗位信息（数组）
        $UserDepartmentPostM = new UserDepartmentPostModel();
        $userDepartmentPosts = $UserDepartmentPostM->getListsByUserId($userId);

        //获取当前岗位下，对应的可用审核流程
        $ExamineM = new ExamineModel();
        $examineLists = $ExamineM->getListsByNowPosts($userDepartmentPosts);
        
        $nameM = new UserModel();
        $name = $nameM->getAllName();

        //传值
        $this->assign("examineLists",$examineLists);
        $this->assign('name',$name);
        $this->assign('project',$projectCategory);
        $this->assign("js",$this->fetch('Index/addJs'));
        $this->assign('YZBODY',$this->fetch('Index/add'));
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

        $projectM = new ProjectCategoryModel();
        $type = $projectM->getTypeById($id);

    $pid = $id;//id作为pid取值
    $res = $projectM->append($pid);
    $this->assign('data',$res);
    $return['data'] = $this->fetch(T('ScientificParameter@Index/append'));
    $return['status'] = $type['type'];
    //echo $data;
    $this->ajaxReturn($return);

}

    /**
     * 查看项目详情
     * @return id 项目ＩＤ
     */
    public function detailAction()
    {
        try
        {
            //取用户信息
            $userId = get_user_id();

            //取项目基础信息
            $projectId = I('get.id');
            $ProjectM = new ProjectModel();
            if( !$project = $ProjectM->getListByIdUserId($projectId , $userId) )
            {
                E("用户无权限查看该记录", 1);    
            }    

            $this->assign("projectId",$projectId);
            $this->assign("YZBODY",$this->fetch('Index/detail'));
            $this->display(YZTemplate);
            
            // $project
            //取项目审核信息
        }
        catch(\Think\Exception $e)
        {
            $this->error = $e;
            $this->_empty();
        }
    }

    public function dataModelDetailAjaxAction()
    {
        $projectCategoryId = I('get.projectCategoryId');
        $data = array('isTeam'=>'0','status'=>'error','message'=>'');

        //取项目类别信息
        $ProjectCategoryM = new ProjectCategoryModel();
        if( !$projectCategory = $ProjectCategoryM->getListById($projectCategoryId) )
        {
            $data['message'] = "id not correct";
            $this->ajaxReturn($data);
        }

        //取数据模型扩展信息
        $dataModelId = $projectCategory['data_model_id'];
        $DataModelDetailL = new DataModelDetailLogic();
        $dataModelDetailRoots = $DataModelDetailL->getRootListsByDataModelId($dataModelId);
        if($dataModelDetailRoots === false)
        {
            $this->error = $DataModelDetailL->getError();
            $this->_empty();
        }
        
        //取当前模型记录对应的select子节点。
        $dataModelDetailSons = $DataModelDetailL->getSonListsByDataModelId($dataModelId);
        if($dataModelDetailSons === false)
        {
            $this->error = $DataModelDetailL->getError();
            $this->_empty();
        }
       

        //取项目类别系数信息（以data_model_detailID为KEY）
        $ProjectCategoryRatioM = new ProjectCategoryRatioModel();
        $ProjectCategoryRatios = $ProjectCategoryRatioM->getListsByProjectCategoryId($projectCategoryId);
        


        //assign
        $this->assign("dataModelId",$dataModelId);
        $this->assign("dataModelDetailRoots",$dataModelDetailRoots);
        $this->assign("dataModelDetailSons",$dataModelDetailSons);
        $this->assign("ProjectCategoryRatios",$ProjectCategoryRatios);
        $data['status'] = "success";
        $data['isTeam'] = $projectCategory['is_team'];
        $data['message'] = $this->fetch('Index/dataModelDetail');
        $this->ajaxReturn($data);
        
    }
}


    
