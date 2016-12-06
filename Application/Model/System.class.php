<?php
/**
 * 系统是
 */
namespace Model;
class System extends Config {
    public function __construct() {
        parent::__construct('closing_time');
    }

    /**
     * 检测系统是否开放
     * @return   boolen                    开放：true; 关闭:false
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T11:12:45+0800
     */
    public function checkSystemIsOpen() {
        $timestamp = (int)$this->getData('value');
        if (time() <= $timestamp) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断是否临近系统关关时间
     * @param    integer                  $time 间隔的时间设定
     * @return   boolen                         
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T10:46:46+0800
     */
    public function nearToClosingTime($time = 86400) {
        $timestamp = (int)$this->getData('value');
        if ((time() <= $timestamp) && (($timestamp - time()) < $time)) {
            return true;
        } else {
            return false;
        }
    }
}