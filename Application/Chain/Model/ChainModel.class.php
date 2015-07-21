<?php

/*
 * 审批管理的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */

namespace Chain\Model;

use Think\Model;
use Post\Model\PostModel;
use Examine\Model\ExamineModel;

class ChainModel extends Model {

    private $examineid = null;
    private $firstpost = null;
    private $endpost = null;

    /**
     * @param int 
     */
    public function setExamineId($id) {
        $this->examineid = $id;
    }

    public function setFirstPost($id) {
        $this->firstpost = $id;
    }

    public function setEndPost($id) {
        $this->endpost = $id;
    }

    //根据传入的examineid值，按着链表的方式依次取出审批的过程
    public function getExamine() {
        //根据firstpost利用循环找出审批的头结点
        $examine = array();
        do {
            $data = $this->getNextPost($data);
            $examine[] = $data;
        }while ($data[next_post] != 0);
        var_dump($data);
        //根据岗位编号取出岗位名称 返回审批
//        $data = array();
//        foreach ($examine as $value) {
//            $post = new PostModel;
//            $post->setPostId($value);
//            $data[] = $post->getPostName();
//        }
//        return $data;
    }

    //根据上一结点寻找下一结点
    //id为空时设置id为0作为开始结点，一直递推到结果再为0
    public function getNextPost($id) {
        if ($id == null) {
            $id = 0;
            $map = array();
            $map['pre_post'] = $id;
            $map['examine_id'] = $this->examineid;
            $data = $this->where($map)->find();
            return $data['now_post'];
        } else {
            $map = array();
            $map['pre_post'] = $id;
            $map['examine_id'] = $this->examineid;
            $data = $this->where($map)->find();
            return $data['now_post'];
        }
    }
}    