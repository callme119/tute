<?php
/*
 * examine审核流程
 */

namespace Examine\Controller;
use Admin\Controller\AdminController;
use Post\Model\PostModel;
use Examine\Model\ExamineModel;
use Chain\Logic\ChainLogic; //审核结点表
use Chain\Model\ChainModel; 
use User\Model\UserModel; //用户表
use Common\Classes;
class IndexController extends AdminController{
     /**
      * 通过post模块取所有岗位信息传给用户点选
      * 无返回值
      */
     public function  newExamineAction(){
        //调用post模块中的getPostInfo方法
        //传给V层  
        $post = new PostModel;
        $page = I('get.p');
        $data = $post->select();
        $this->assign('page',$page);
        $this->assign("post",$data);
        $this->assign("js",$this->fetch('newexamineJs'));
        $this->assign('YZBODY',$this->fetch('newexamine'));
        $this->display(YZTemplate);
    }

    //初始化审批列表
    public function  indexAction(){
        $model = new ExamineModel;
        $count = $model->getListsCount();
        $page = I('get.p');
        //当初始化page为空时赋值
        if($page == null);{
            $page = 1;
        }
        //当输入page值超过最大页数时,将最大页数值代替默认值
        if($page > (int)$count/C('PAGE_SIZE')){
            $page = (int)$count/C('PAGE_SIZE');
        }
        $data = $model->index($page);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->assign('examine',$data);
        $this->assign('YZBODY',$this->fetch('examinelist'));
        $this->display(YZTemplate);
    }
    
    //添加审批流程
    public function saveAction() {
        //先将post过来的岗位信息存审批对应的链表信息
        $post = I('post.chain');

        if(count($post) == 1){
            $this->error('审批岗位不能为空,请重新添加审批岗位',U('Examine/Index/newexamine'));
        }
        $model = new ExamineModel;
        $page = I('get.p');
        //获取该审批对应的头结点id
        $id = $model->saveChain($post);
        $name = I('post.tittle');
        $model->saveExamine($id,$name);
        redirect_url(U('index').'?id='.$page);
    }
    /**
     * 通过GET过来的流程ID值，查找当前用户，当前流程下的下一环节审核人员
     * @param [string] $get.id 传入的用户选择的流程CHAIN_ID值
     * @return [二维数组] 下一环节审核人员ID及姓名
     * todo:重写get_user_id()。原因，此时如果判断出用户未登陆，不应该直接跳出，而应该返回FALSE。
     */
    public function getExamineUsersAction()
    {   
        //初始化数据
        $examineId = I("get.id");
        $state = 'success';
        //判断空值 
        if( empty($examineId) )
        {
            $state = 'error';
            $data['message'] = '未传入正确的ID值，或传入的ID值有误';
        }

        //程序开始执行
        else
        {   
            $ExamineM = new ExamineModel();
            $map = array();
            $map['id'] = $examineId;
            $examine = $ExamineM->where($map)->find();
            $chainId = $examine['chain_id'];
            $userId = get_user_id();

            //取用户在当前审核流程下适用的岗位集合（一组数组）
            $ChainL = new ChainLogic();
            $checkUsers = $ChainL->getNextExaminUsersByUserIdAndId($userId,$chainId);

            //根据用户ID，取用户其它信息
            $userM = new UserModel();
            $users = $userM->getListsByLists($checkUsers,'user_id','id,name');

            $this->assign("users",$users);
            $this->assign("name",I("get.name"));
            $html = $this->fetch();
        }

        //返回数据
        $return = array('state' => $state,'html'=> $html);
        $this->ajaxReturn($return);
    }

    //冻结审批流程
    public function freezenAction(){
        $id = I('get.id');
        $p = I('get.p');
        $model = new ExamineModel;
        $model->freezen($id);
        redirect_url(U('Examine/Index/index'.'?p='.$p));
    }  
}
    