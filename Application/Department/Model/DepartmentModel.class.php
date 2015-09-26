<?php
namespace Department\Model;
use Think\Model;
use Think\Model\RelationModel;
class DepartmentModel extends Model
{
	/*protected $_link = array(
        		'Post'=>array(
		            'mapping_type'      => self::MANY_TO_MANY,
		            'class_name'        => 'Post',
		            'foreign_key'       =>  'department_id',
    			'relation_foreign_key'  =>  'post_id',
    			'relation_table'    =>  'yunzhi_department_post'
		            ),
        	);
	*
	 * [test 关联模型的测试方法]需要继承 Think\Model\RelationModel
	 * 经测试有效，多对多的关联方式，对于本表和中间表都有操作，
	 * 对于关联的表只进行查找，不进行操作
	 * 详细资料参考thinkphp官方文档 http://document.thinkphp.cn/manual_3_2.html#relation_model
	 * @return [type] [description]
	 */
	public function test(){
		$data = array();
		$data['name'] = "ce测部门";
		$data['parent_id'] = '0';
		$data['Post'] = array(
			'id' => '11',
			);
		$result = $this -> relation(true) -> add($data);
		var_dump($result);
		exit();
	}
	/**
	 * [getDepartmentList 获取部门列表，就是将树转化为列表
	 * 然后根据分页信息截取返回部门列表]
	 * @param  integer $rootDepatrmentId [description]
	 * @param  [type]  $layer            [description]
	 * @param  string  $keyWord          [description]
	 * @return [type]                    [description]
	 */
	public function getDepartmentList($rootDepatrmentId = 0,$layer,$keyWord = '_son'){
		$departmentTree = $this -> getDepartmentTree($rootDepatrmentId,$layer,$keyWord);
		$departmentList = tree_to_list($departmentTree,1,$keyWord,'_level','order');
		//设置总条数
		$this -> totalCount = count($departmentList);
		//截取该页的信息
		$departmentList = array_slice($departmentList , ($this->p-1)*$this->pageSize , $this->pageSize);
		return $departmentList;
	}
	/**
	 * [getDepartmentTree 获取部门树形结构]
	 * @param  integer $rootDepatrmentId [根元素id]
	 * @param  [type]  $layer            [层级]
	 * @param  string  $keyWord          [子集元素的key值]
	 * @return [type]                    [部门树形结构]
	 * xuao 295184686@qq.com
	 */
	public function getDepartmentTree($rootDepatrmentId = 0,$layer,$keyWord = '_son'){
		//设置根元素id,获取一级元素
		$map['parent_id'] = $rootDepatrmentId;
		$data = $this -> where($map) -> select();

		//根据层级获得树形结构的所有元素
		if($layer--){
			foreach ($data as $key => $value) {
				//以一级元素为根元素查找新的树
				$rootDepatrmentId = $value['id'];
				//通过/递归找到所有元素
				$data[$key][$keyWord] = $this -> getDepartmentTree($rootDepatrmentId,$layer,'_son');
			}
		}

		return $data;
		
	}
	/**
	 * [getDepartmentInfoById 通过id获取部门信息]
	 * @param  [type] $id [id值]
	 * @return [type]     [返回部门信息]
	 */
	public function getDepartmentInfoById($id){
		$map['id'] = $id;
		$data =  $this  -> where($map) -> find();
		return $data;
	}
	/**
	 * [updateDepartment 更新部门]
	 * @return [type] [返回状态]
	 */
	public function updateDepartment(){
		$data = I('post.');
		$data['id'] = I('get.id');
		$state = $this -> save($data);
		//返回值为影响条数。如果为0，说明数据未修改，但是保存可以成功
		if($state == 0){
			$state = true;
		}
		$_POST['id'] = $data['id'];
		return $state;
	}

	public function addDepartment(){

		$data = I('post.');
		$state = $this -> add($data);
		//设置post供添加部门-岗位使用
		$_POST['id'] = $state;
		return $state;
		//添加成功返回添加的部门id
	}

	/**
	 * [addDepartment 删除部门]
	 */
	public function deleteDepartment(){
		$map['id'] = I('get.id');
		//delete方法的返回值是删除的记录数，如果返回值是false则表示SQL出错，返回值如果为0表示没有删除任何数据。
		$state = $this -> where($map) ->delete();
		return $state;
	}

	/**
	 * 通过部门ID，返回本部门及上级部门组成的列表。
	 * @param  array $currentDepartments 包括有 部门ID 的多维数组
	 * @param  string $keyWord            keywork下标
	 * @return 数组                     [一维数组，只包括返回数组的ID]
	 */
	public function getParentTreeByLists($currentDepartments , $keyWord = "department_id")
	{
		$return = array();
		foreach($currentDepartments as $currentDepartment)
		{
			$return[] = $this->getParentTreeByList($currentDepartment , $keyWord);
		}
		return $return;


	}
	/**
	 * 通过包括有部门关键字的数据，获取该部门及上级部门的ID
	 * @param  [array] $currentDepartment [一组数据]
	 * @param  string $keyWord           [关键字]
	 * @return [array]                    [本级及上级部门树]
	 */
	public function getParentTreeByList($currentDepartment , $keyWord = "department_id")
	{
		$parentId = $currentDepartment[$keyWord];
		do
		{
			$map[id] = $parentId;
			$data = $this->where($map)->find();
			if(count($data))
			{
				$return[$data[id]] = $data[id];
				$parentId = $data['parent_id'];
			}
			else
			{
				$parentId = "0";
			}
		}
		while($parentId != "0" );
		return $return;
	}
}