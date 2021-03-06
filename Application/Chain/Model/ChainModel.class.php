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

    public function setFirstId($id) {
        $this->firstid = $id;
    }

    public function setEndId($id) {
        $this->endid = $id;
    }

    //根据传入的examineid值，按着链表的方式依次取出审批的过程
    public function getExamine($id) {

        $postid = array();
        $data = $this->getListById($id);
        $postid[] = $data['now_post'];

        do {
            $data = $this->getNextPost($id);
            $id = $data[id];
            $postid[] = $data['now_post'];
        }while ($data[next_id] != 0);
        //根据岗位编号取出岗位名称 返回审批
       $examine = array();
       foreach ($postid as $value) {
           $post = new PostModel;
           $info = $post->getPostInfoById($value);
           $examine[] = $info['name'];
       }
       return $examine;
    }

    //根据上一结点寻找下一结点
    //id为空时设置id为0作为开始结点，一直递推到结果再为0
    public function getNextPost($id) {
        if ($id == null) {
            $id = $this->firstid;
            $map['id'] = $id;
            $data = $this->where($map)->find();
            return $data;
        } else {
            $map = array();
            $map['pre_id'] = $id;
            $data = $this->where($map)->find();
            return $data;
        }
    }
    
    /**
     * 根据传入的数组、总数、审批的id值存储链表信息
     * @param array $array
     * @param int $num
     * @param int $examineid
     * 无返回值
     */
    public function save($array,$num,$examineid) {
        for($i=0;$i<$num;$i++){
            $data = array();
            //进行判断，看是否是初始链表与结束链表，初始链表头结点为0，结束链表尾结点为0
            if($i==0){
                $data[pre_post] = 0;
                $data[now_post] = $array[0];
                $data[next_post] = $array[1];
            }else if($i == $num-1){
                $data[pre_post] = $array[$i-1];
                $data[now_post] = $array[$i];
                $data[next_post] = 0;
            }else{
                $data[pre_post] = $array[$i-1];
                $data[now_post] = $array[$i];
                $data[next_post] = $array[$i+1];
            }
            //存入链表信息
            $data[examine_id] = $examineid;
            $this->add($data);        
        }
    }

    /**
     * 通过ＩＤ获取下一条审核记录。
     * @param  string $workflowId id
     * @return array             一组数组。如果本条为最后一条，则返回ＮＵＬＬ
     * panjie 3792535@qq.com
     * date:2015.9.3
     */
    public function getNextListById($id = null)
    {
        if($id === null || !is_string($id))
        {
            $this->error = "未传入ID值，或传入的ID值不符合规范";
            return false;
        }
        $map['pre_id'] = $id;
        $data = $this->where($map)->find();
        if($data != false)
        {
            return $data;
        }
        else
        {
            return null;
        }
    }
    /**
     * 通过ID值，查找单条数据信息
     * @param  string $id id
     * @return array     一组数组，包括本条所有信息
     */
    public function getListById($id = null)
    {
        if($id === null || !is_string($id))
        {
            $this->error = "未传入ID值，或传入的ID值不符合规范";
            return false;
        }
        $map['id'] = $id;
        return $this->where($map)->find();
    }
}    