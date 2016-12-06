<?php
/**
 * 系统配置信息
 */
namespace Model;
use Config\Model\ConfigModel;         // 系统配置信息

class Config extends Model {
    private $ConfigModel;           // 配置实体

    public function __construct($title = '') {
        $title = (string)$title;
        $this->ConfigModel = new ConfigModel;
        $data = $this->ConfigModel ->where("title = '$title'")->find();
        if ($data !== null) {
            $this->data = $data;
        }
    }

    /**
     * 通过时间戳获取 时间字符串
     * @param    timestamp                   $timestamp 时间戳
     * @return   string                              格式化后的字符串
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T13:23:31+0800
     */
    static public function getDateByTimestamp($timestamp) {
        return date('Y-m-d', $timestamp);
    }   

    /**
     * 将日期格式的字符串更新到当前对象中
     * @param    datatime                   $dataString 
     * @return   true                               
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T09:09:33+0800
     */
    public function updateValueFromDateString($dataString) {
        $timestamp = strtotime($dataString);
        $data = array();
        $data['value'] = $timestamp;
        $title = $this->getData('title');
        $map = array('title' => $title);
        $status = $this->ConfigModel->where($map)->data($data)->save();
        return true;
    }
}