<?php
/*
 * 
 */

namespace Examine\Controller;
use Admin\Controller\AdminController;
use Post\Model\PostModel;
use Examine\Model\ExamineModel;
class IndexController extends AdminController{
     /**
      * 通过post模块取所有岗位信息传给用户点选
      * 无返回值
      */
     public function  newExamineAction(){
        //调用post模块中的getPostInfo方法
        //传给V层
        $post = new PostModel;
        $data = $post->select();
        $this->assign("post",$data);
        $this->assign('YZBODY',$this->fetch('newexamine'));
        $this->display(YZTemplate);
    }

    //初始化审批列表
    public function  indexAction(){
        $model = new ExamineModel;
        $data = $model->index();
        $this->assign('examine',$data);
        $this->assign('YZBODY',$this->fetch('examinelist'));
        $this->display(YZTemplate);
    }
    
    //添加审批流程
    public function saveAction() {
        //先将post过来的岗位信息存审批对应的链表信息
        $post = I('post.chain');
        $model = new ExamineModel;
        //获取该审批对应的头结点id
        $id = $model->saveChain($post);
        $name = I('post.name');
        $model->save($id,$name);
    }
    /**
     * 通过GET过来的流程ID值，查找当前用户，当前流程下的下一环节审核人员
     * @param [string] $get.id 传入的用户选择的流程ID值
     * @return [二维数组] 下一环节审核人员ID及姓名
     * todo:重写get_user_id()。原因，此时如果判断出用户未登陆，不应该直接跳出，而应该返回FALSE。
     */
    public function getExamineUsersAction()
    {   
        //初始化数据
        $examineId = I("get.id");
        $state = 'success';

        //判断空值 
        if( empty($id) )
        {
            $state = 'error';
            $data['message'] = '未传入正确的ID值，或传入的ID值有误';
        }

        //程序开始执行
        else
        {
            $userId = get_user_id();

            //取用户在当前审核流程下适用的岗位集合（一组数组）
            //取在此岗位下，用户的部门及上级部门（三维数组）。
            //
            $data = array();
        }

        //返回数据
        $return = array('state' => $state,'data'=> $data);
        $this->ajaxReturn($return);
    }
}
    