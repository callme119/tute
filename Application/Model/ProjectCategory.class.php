<?php
/**
 * 项目类别
 */
namespace Model;
use ProjectCategory\Model\ProjectCategoryModel;         // 项目类别数据表

class ProjectCategory extends Model {
    public function __construct($id = 0) {
        $id = (int)$id;
        $ProjectCategoryModel = new ProjectCategoryModel;
        $data = $ProjectCategoryModel->where('id = ' . $id)->find();
        if ($data !== null) {
            $this->data = $data;
        }
    }
}