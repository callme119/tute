<?php

/*
 * 审批链表对应的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Examine\Model;
use Think\Model;
use Chain\Model\ChainModel;
use Post\Model\PostModel;
class ExamineModel extends Model{
    //保存create信息
    public function save(){
        $this->create();
        $this->add();
    }
    
    //根据审批表与审批链表取的审理流程信息
    public function index() {
        $chain = new ChainModel;
        //取出审批对应的基本信息
        $data = array();
        $data = $this->select();
        var_dump($data);
        //根据对应的num firstpost endpost取出整个审批流程
        $examine = array();
        $chain->setExamineId(2);
        $chain->setFirstPost(2);
        $chain->setEndPost(4);
        $examine = $chain->getExamine();
//        foreach ($data as $key => $value) {
//            $chain->setExamineId($value['id']);
//            $examine = $chain->getExamine();
//        }
    }
    
    //取出所有post名称供V层选择
    public function add(){
        //取岗位名称
        $post = new PostModel;
        $Info = $post->getPostInfo();
        $postname = array();
        foreach ($Info as $value) {
            $postname[] = $value['name'];
        }
        return $postname;
    }
    
}