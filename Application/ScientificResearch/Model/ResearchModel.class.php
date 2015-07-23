<?php

/*
 * 审批链表对应的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace ScientificResearch\Model;
use Think\Model;
class ResearchModel extends Model{
    public function getResarchInfoById($id) {
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->find();
    }
}
