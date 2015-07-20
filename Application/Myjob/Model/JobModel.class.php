<?php

/*
 * 我的工作的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Myjob\Model;
use Think\Model;
use UserJob\Model\UserJobModel;
class JobModel extends Model{
     private $jobid = null;
     private $userid = null;
    /**
     * 设定map数组
     * @param array $map
     */
    public function setJobId($jid) {
        $this->jobid = $jid;
    }
    public function setUserId($id) {
        $this->userid = $id;
    }
    
     /**
     * 根据map取待办项目数据
     * @param int $id
     * @return array
     */
    public function getUnfinishedIdByJobId() {   
        $map = array();
        $map['id'] = $this->jobid;
        $map[finished_state] = 0;
        $data = $this->where($map)->find();
        return $data['research_id'];
    }
    
    /**
     * author : denghaoyang
     * 根据uerid取出对应待办项目信息并初始化界面
     * @param type int
     * @return array 待办项目信息
     */
   
    public function getUnfinishedInfoByUserId() {
       //根据C层传入的userid取出jobid
       $userjob = new UserJobModel;
       $userjob->setUserId($this->userid);
       $data = $userjob->getJobIdByUserId();
       var_dump($data);
       //根据取出的jobid再取出researchid
       $researchid = array();
       foreach($data as $value){
           $this->setJobId($value['job_id']);
           $id = $this->getUnfinishedIdByJobId();
           if($id != null){
               $researchid[] = $id;
           }
       }
       var_dump($researchid);
       //根据取出的researchid取出research信息
       //getResearchInfoById;
       //输出图像
       
       
    }  
}