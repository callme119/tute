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
    /**
     * 根据传入的用户点击的信息存入数组再加上从界面上获取的岗位数量进行存库
     * @param array $array
     * 无返回值
     */
    public function saveChain($post){

        //上下结点初始化
        $preId = 0;
        $nextId = 0;
        $ChainM = new ChainModel;
        foreach ($post as $key => $value) {

             //传值 
            $data = array();
            $data['pre_id'] = $preId;
            $data['now_post'] = $value;
            $data['next_id'] = $nextId;

            $ChainM->create($data);
            $nextId = $ChainM->add();

            //取出首结点信息
            if($key == 0)
            {
                $startId = $nextId;
            }

            //上个结点不为0，则更新上个结点信息 。
            if($preId != 0)
            {   
                $data = array();
                $data['id'] = $preId;
                $data['next_id'] = $nextId;
                $ChainMM = M('Chain');
                $ChainMM->data($data)->save();
                echo $ChainM->getLastSql();
                dump($data);
            }

            //初始化下一循环
            $preId = $nextId;
            $nextId = 0;
        }
        return $startId;
    }
    
    //根据审批表与审批链表取的审理流程信息
    public function index($str = "->") {
        $chain = new ChainModel;
        //取出审批对应的基本信息
        $data = array();
        $data = $this->select();
        //根据对应的num firstpost endpost取出整个审批流程
        $examine = array();
       foreach ($data as $key => $value) {
           $chain->setFirstId($value['chain_id']);
           $examine[] = $chain->getExamine();
       }
       $string = array();
       foreach ($examine as $key => $value) {
           foreach ($value as $k => $val) {
            if($k)
               $string[$key] .= $str  . $val;
            else
                $string[$key] .= $val;
           }
       }
       foreach ($data as $key => $value) {
           $data[$key][string] = $string[$key];
       }
       return $data;
    }
    
    //取出所有post名称供V层选择
    public function getPostName(){
        //取岗位名称
        $post = new PostModel;
        $Info = $post->getPostInfo();
        $postname = array();
        foreach ($Info as $value) {
            $postname[] = $value['name'];
        }
        return $postname;
    }
    
    //生成随机数作为Examineid并与数据库中的id值比较
    public function createRand() {
        //生成从0到9999的随机数
        $data = array();
        $data = $this->select();
        //将生成数与数据库中编号数据对比
        foreach ($data as $value) {
            do{
                $id = rand(0000,9999);
            }while ($id != $value['id']);
        }
        return $id;
    }
    
    //存储审批的链表信息后再存储对应的审批信息
    // @param int $id
    // @param string $name
    // 无返回值
    public function saveExamine($id,$name){
        $data = array();
        $data['chain_id'] = $id;
        $data['name'] = $name;
        
        $this->add($data);
    }

    //获取所有审批流程名称及编号
    public function getLists(){
        $lists = $this->field('id,name')->select();
        return $lists;
    }
    /**
     * 根据传入的包括岗位信息的获取审核流程基础信息
     * @param  array $userDepartmentPosts 包括有keyword的二维数组
     * @param  string $keyWord             代表岗位关键字的KEYWORD
     * @return array                      二维数组
     * panjie 3792535@qq.com
     */
    public function getListsByNowPosts($userDepartmentPosts , $keyWord = "post_id")
    {
        if(!is_array($userDepartmentPosts))
        {
            $this->error = "传入参数非数组";
            return false;
        }

        $return = array();
        foreach($userDepartmentPosts as $key => $value)
        {
            if(!isset($value[$keyWord]))
            {
                $this->error = "传入数据KEY值定义错误";
                return false;
            }

            $map['now_post'] = $value[$keyWord];
            $map['a.state'] = 0;
            $field['a.id'] = 'id';
            $field['a.name'] = 'name';
            $field['a.chain_id'] = 'chain_id';
            $data = $this->alias('a')->field($field)->where($map)->join("left join __CHAIN__ b on a.chain_id = b.id")->select();
            foreach($data as $v)
            {
                $return[$v['id']] = $v;
            }  
        }
        return $return;
    }
}