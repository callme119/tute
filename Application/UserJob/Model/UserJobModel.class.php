<?php

/*用户与工作对应Model
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace UserJob\Model;
use Think\Model;
class UserJobModel extends Model{
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
    public function getJobIdByUserId($Id) {   
        $map[user_id] = $id;
        return $this->where($map)->select();
    }
}