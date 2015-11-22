<?php
/**
 * 科研项目
 */
namespace ScientificResearch\Controller;
use Admin\Controller\AdminController;
use Chain\Logic\ChainLogic;                             //审核链
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
use Workflow\Logic\WorkflowLogic;                       //工作流
use WorkflowLog\Model\WorkflowLogModel;                 //工作流扩展表
use WorkflowLog\Logic\WorkflowLogLogic;                 //工作流扩展信息表
use ProjectCategoryRatio\Model\ProjectCategoryRatioModel;//项目类别系数表
use ProjectDetail\Logic\ProjectDetailLogic;             //项目扩展信息
use ProjectDetail\Model\ProjectDetailModel;             //项目扩展信息
use Workflow\Service\WorkflowService;                   //审核流程
use Cycle\Logic\CycleLogic;                             //周期表
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;            //项目类别系数
use ScientificResearch\Model\Index\IndexModel;                //index 模型
use ScientificResearch\Model\Index\AddModel;                //add 模型

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
        // $projects = $ProjectM->getListsJoinProjectCategoryByUserIdType($userId , $type);
        $ScoreL = new ScoreLogic();
        $projects= $ScoreL->getListsJoinProjectCategoryByUserIdType($userId , $type);
        $totalCount = $ScoreL->getTotalCount();

        $indexM = new IndexModel();

        //传值
        $this->assign("indexM", $indexM);
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
        header("Content-type: text/html; charset=utf-8");
        $userId = get_user_id();                //当前用户
        $projectId = (int)I('post.project_id'); //项目ID
        $checkUserId = I('post.check_user_id'); //传入下一审核人

        //判断用户是提交还是保存操作
        $type = (I('post.type') === 'submit') ? "submit" : "save";

        if ($type === 'submit')
        {

            //查询当前项目是否已经选过审核流程
            $WorkflowL = new WorkflowLogic();
            if ($workflow = $WorkflowL->getListByProjectId($projectId))
            {
                //取审核链信息，得到审核人员
                $ChainL = new ChainLogic();
                if ( !$checkUsers = $ChainL->getNextExaminUsersByUserIdAndId($userId , $workflow['chain_id']))
                {
                    $this->error = "取出审核人员信息发生错误，错误信息:" . $ChainL->getError();
                    $this->_empty();
                    return false;
                } 

                $isCheckUser = false;
                foreach($checkUsers as $checkUser)
                {
                    if ($checkUser['user_id'] == $checkUserId)
                    {
                        $isCheckUser = true;
                        break;
                    }
                }

                if ($isCheckUser === false)
                {
                    $this->errors[] = "传入审核人员信息有误或未传入审核人信息";
                    return false;
                }
            }
        }
        
        //接收projectId, 如果存在。则进行合规验证
        if( $projectId  )
        {              
            //查询当前project是否存在
            $ProjectL = new ProjectLogic();
            if( !$project = $ProjectL->getListbyId($projectId) )
            {
                $this->error = "当前记录不存在或已删除";
                $this->_empty();
            }
           
            //查询当前用户是否有当前project的修改权限
            if ($userId != $project[user_id])
            {
                $this->error = "用户userId为" . $userId . "与项目申请用户"  . $project[user_id] . "不符";
                $this->_empty();
            }
        }

        //取当前周期id
        $cycleLogicL = new CycleLogic();
        $cycleLogic = $cycleLogicL->getCurrentList();
        $cycleId = $cycleLogic['id'];

        //取项目类别信息
        $projectCategoryId = I('post.project_category_id');
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

        //存项目信息.save方法实现的功能为：如果存在数据，则更新。不存在，则添加
        $ProjectM = new ProjectModel();
        if( !$projectId = $ProjectM->save($userId, $cycleId) )
        {
            $this->error = "数据添加发生错误，代码" . $this->getError();
            $this->_empty();
        }

        //用户为提交操作
        if($type === 'submit')
        {
            
            
            //存在当前工作流信息，则证明为退回项目后修改
            if ( $workflow )
            {
                //获取当前审核结点信息
                $WorkflowLogL = new WorkflowLogLogic();
                $workflowLog = $WorkflowLogL->getCurrentListByWorkflowId($workflow[id]);

                //进行权限判断，即用户现在是否有权限对该审核结点进行操作
                if($workflowLog['user_id'] != $userId)
                {
                    $this->error = "对不起，无此操作权限";
                    $this->_empty();
                    return;
                }
                
                //更新审核流程结点
                if(!$WorkflowLogL->saveCommited($workflowLog[id], $checkUserId))
                {
                    $this->error = $WorkflowLogL->getError();
                    $this->_empty();
                    return;
                }
                
            }        
            //未选审核流程，则证明用户第一次提交.
            else
            {
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
            }
            
        }

        //删除分值表
        $ScoreL = new ScoreLogic();
        $ScoreL->deleteByProjectId($projectId);

        //存分数信息,如果是团队信息，存团队，如果不是，存个人
        //团队的话还要判断是不是包含自己，不包含分值百分比变为零
        $ScoreM = new ScoreModel();      
        $isTeam = $projectCategory['is_team'];
        if($isTeam == 1)
        {
            $name = I('post.name');

            //判断团队成员只有自己
            //name为空时，存操作人自己
            if((count(array_unique($name))<2 && array_unique($name)[0]==0) || (count($name)<2 && in_array($userId, $name))){
                $ScoreL = new ScoreLogic();
                if(!$ScoreL->addByUserIdProjectIdScorePercent($userId , $projectId))
                {
                    E("添加分数信息时发生错误，错误信息：" . $ScoreL->getError());
                }
            }
            else{

                //判断是否团队添加了同一个人两次
                if (count($name) != count(array_unique($name))) {
                    $this->error = "不能添加同一个人两次";
                    $this->_empty();
                }   

            //判断团队中是否有提交人本人
            //如果没有加入，他的占比为0
                if(!in_array($userId, $name)){
                    $addOneself = $ScoreM->addOneself($projectId,$userId);
                }

                $Score = $ScoreM->save($projectId);
                if($Score === false)
                {
                    $this->error = "数据添加发生错误，代码" . $ScoreM->getError();
                    $this->_empty();
                }
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

        //删除项目扩展信息
        $ProjectDetailL = new ProjectDetailLogic();
        $ProjectDetailL->deleteByProjectId($projectId);

        //存新的项目扩展信息
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

        //取项目信息
        $projectId = (int)(I('get.id'));
        $ProjectL = new ProjectLogic();
        $project = $ProjectL->getListById($projectId);

        $AddM = new AddModel();
        //如果存在项目信息，则取出。
        //不存在，则为添加
        if($project !== false)
        {

            $AddM->setProject($project);
            $AddM->setUserId($userId);

            if( !$AddM->isEdit() )
            {
                E("记录" . $projectId . "失效：当前项目已提交或已删除");
            }
        }

    $ProjectCategoryL = new ProjectCategoryLogic();
    $projectCategoryTree = $ProjectCategoryL->getSonsTreeById($pid=0,$type=CONTROLLER_NAME);
    $projectCategorys = tree_to_list($projectCategoryTree , $id , '_son' );

    //获取当前用户部门岗位信息（数组）
    $UserDepartmentPostM = new UserDepartmentPostModel();
    $userDepartmentPosts = $UserDepartmentPostM->getListsByUserId($userId);
    
    // dump($userDepartmentPosts);
    //获取当前岗位下，对应的可用审核流程
    $ExamineM = new ExamineModel();
    $examineLists = $ExamineM->getListsByNowPosts($userDepartmentPosts);

    $nameM = new UserModel();
    $name = $nameM->getAllName();

        //传值
    $this->assign("examineLists",$examineLists);
    $this->assign('name',$name);
    $this->assign("AddM",$AddM);
    $this->assign('projectCategorys',$projectCategorys);
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
            $project = $ProjectM->getListById($projectId);
            if( $project == null )
            {
                E("项目记录不存在", 1);    
            }    

            //取用户分值分配信息
            $ScoreL = new ScoreLogic();
            $score = $ScoreL->getListByProjectIdUserId($projectId , $userId);
            if($score == null)
            {
                E("您无权查看该记录", 1);    
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
        
        //判断是否传入项目ID
        $projectId = (int)I('get.projectId');
        
        //取项目数据详情
        $ProjectDetailL = new ProjectDetailLogic();
        $projectDetail = $ProjectDetailL->getListsByProjectId($projectId);
        // dump($projectDetail);

        //assign
        $this->assign("projectDetail",$projectDetail);
        $this->assign("dataModelId",$dataModelId);
        $this->assign("dataModelDetailRoots",$dataModelDetailRoots);
        $this->assign("dataModelDetailSons",$dataModelDetailSons);
        $this->assign("ProjectCategoryRatios",$ProjectCategoryRatios);
        $data['status'] = "success";
        $data['isTeam'] = $projectCategory['is_team'];
        $data['message'] = $this->fetch('Index/dataModelDetail');
        // header("Content-type:text/html;charset=utf-8");
        // echo $data[message];
        // die();
        $this->ajaxReturn($data);
        
    }

    /**
     * 删除项目的扩展信息
     * @param  [type] $projectid [description]
     * @return [type]            [description]
     */
    private function deleteExtendsDatas($projectid)
    {

        //删除工作流表
        //删除工作流详情表
        //删除分值记录表
    }
}



