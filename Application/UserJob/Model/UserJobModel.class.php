<?php

/*用户与工作对应Model
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace UserJob\Model;
use Think\Model;
class UserJobModel extends Model{
    private $userid = null;
    
    /**
     * 
     * @param 无返回值
     */
    public function setUserId($id) {
        $this->userid = $id;
    }
    
    public function index($id) {
        $map = array();
        $map[id] = $id;        
        $data = $this->where($map)->select();
        var_dump($data);
    }
    /**
     * 根据传入的userid初始化待办工作数据
     * @param int $id
     * @return array
     */
    public function getJobIdByUserId() {   
        $map = array();
        $map[user_id] = $this->userid;
        $data = $this->where($map)->select();
        return $data;
    }
}