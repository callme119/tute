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
use DataModel\Model\DataModelModel;                     //数据模型
use DataModel\Logic\DataModelLogic;                     //数据模型
use DataModelDetail\Logic\DataModelDetailLogic;         //数据模型详情
use DataModelDetail\Model\DataModelDetailModel;         //数据模型扩展信息
use ExamineDetail\Model\ExamineDetailModel;             //审核扩展信息
use Workflow\Model\WorkflowModel;                       //工作流表
use WorkflowLog\Model\WorkflowLogModel;                 //工作流扩展表

use ProjectCategoryRatio\Model\ProjectCategoryRatioModel;   //项目类别系数表
use ProjectDetail\Logic\ProjectDetailLogic;             //项目扩展信息
use ProjectDetail\Model\ProjectDetailModel;             //项目扩展信息
use Workflow\Service\WorkflowService;                   //审核流程
use Cycle\Logic\CycleLogic;                             //周期表

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

        //取项目表信息
        $ProjectM = new ProjectModel();

        // $projects = $ProjectM->getListsByUserIdType($userId , $type);;
        $projects = $ProjectM->getListsJoinProjectCategoryByUserIdType($userId , $type);
        $totalCount = $ProjectM->getTotalCount();
        
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
        //dump(I('post.'));
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
            $data['message'] = "please select project";
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

        $ProjectM = new ProjectModel();
        $projectId = $ProjectM->save($userId,$cycleId);
        if($projectId === false)
        {
            $this->error = "数据添加发生错误，代码" . $this->getError();
            $this->_empty();
        }

        $examineId = (int)I('post.chain_id');
        $checkUserId = (int)I('post.examine_id');

        $WorkflowS = new WorkflowService();
        if(!$WorkflowS->add($userId , $examineId , $projectId, $checkUserId , $commit = "申请"))
        {
            //删除项目信息
            $this->error = $WorkflowS->getError();
            $this->_empty();
        }

        $ScoreM = new ScoreModel();
        $Score = $ScoreM->save($userId,$projectId);
        if($Score === false)
        {
            $this->error = "数据添加发生错误，代码" . $this->getError();
            $this->_empty();
        }
        
        $projectDetailM = new projectDetailModel();
        $projectDetail = $projectDetailM->save($projectId,$dataModelDetailRoots);
        if($projectDetail === false)
        {
           $this->error = "数据添加发生错误，代码" . $this->getError();
           $this->_empty();

        }
        else
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
        $projectCategoryTree = $ProjectCategoryL->getSonsTreeById($pid=0,$type='ScientificResearch');
        $projectCategory = tree_to_list($projectCategoryTree , $id , '_son' );

        //获取当前用户部门岗位信息（数组）
        $UserDepartmentPostM = new UserDepartmentPostModel();
        $userDepartmentPosts = $UserDepartmentPostM->getListsByUserId($userId);

        //获取当前岗位下，对应的可用审核流程
        $ExamineM = new ExamineModel();
        $examineLists = $ExamineM->getListsByNowPosts($userDepartmentPosts);

        // $projectM = new ProjectCategoryModel();
        // $project = $projectM->init();
        
        $nameM = new UserModel();
        $name = $nameM->getAllName();

        //传值
        $this->assign("examineLists",$examineLists);
        $this->assign('name',$name);
        $this->assign('project',$projectCategory);
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

            //取项目数据模型信息
            $DataModelM = new DataModelModel();
            $dataModelId = $project['data_model_id'];
            if( !$dataModel = $DataModelM->where("id = $dataModelId")->find())
            {
                E("当前记录选取的数据模型ＩＤ为$dataModelId,但该ＩＤ在数据库中未找到匹配的记录", 1);    
            }

            //取数据模型字段信息
            $DataModelL = new DataModelLogic();
            $dataModelCommon = $DataModelL->getCommonLists();

            //取项目模型扩展信息
            $dataModelDetailM = new DataModelDetailModel();
            $dataModelDetail = $dataModelDetailM->getListsByDataModelId($dataModelId);

            //取项目扩展信息
            $ProjectDetailL = new ProjectDetailLogic();
            $projectDetail = $ProjectDetailL->getListsByProjectId($projectId);

            //取审核信息
            $WorkflowM = new WorkflowModel();
            $workflow = $WorkflowM->where("project_id = $projectId")->find();

            //取审核扩展信息
            $workflowId = $workflow["id"];
            $WorkflowLogM = new WorkflowLogModel();
            $workflowLog = $WorkflowLogM->getListsByWorkflowId($workflowId);
            
            $todoList = $WorkflowLogM->getTodoListByWorkflowId($workflowId);

            $score = W("Project/getRatioById",array($project[id]));
            //取审核扩展信息
            $this->assign("project",$project);
            $this->assign("dataModel",$dataModel);
            $this->assign("dataModelDetail",$dataModelDetail);
            $this->assign("projectDetail",$projectDetail);
            $this->assign("workflowLog",$workflowLog);
            $this->assign("todoList",$todoList);
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


    
