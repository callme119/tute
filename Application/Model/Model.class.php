<?php
/**
 * 模型MODEL
 */
namespace Model;

class Model {
    protected $data = [];

    /**
     * 获取data变量的中数据 
     * @param    string                   $key 键值
     * @return   array | mix | string                        
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-11-23T09:34:31+0800
     */
    public function getData($key = '') {
        if (empty($key)) {
            return $this->data;
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            return '';
        }
    }

    /**
     * 设置data数据
     * @param    array                   $data 
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T13:22:48+0800
     */
    public function setData($data) {
        if (is_array($data)) {
            $this->data = $data;
        }
    }
}