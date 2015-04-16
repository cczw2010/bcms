<?php
/**
 * 通用数据处理基类
 */

class Model_Common{

	public $TNAME;				//*表名（必须）
	public $APPNAME;			//模块名

	/**
	 * 构造函数
	 * @param string $tname 表名
	 */
	function __construct($tname){
		$this->TNAME = $tname;
	}
	
	/**
 	 * 根据id获取内容
 	 * @param  int $id 内容id
 	 * @return array||false
 	 */
	public function getItem($id){
		$query = $GLOBALS['db']->query('select * from '.$this->TNAME.' where id='.$id);
		return $GLOBALS['db']->fetchArray($query);
	}
	/**
 	 * 删除内容
 	 * @param  int $id 内容id
 	 * @return int 操作所影响的记录行数
 	 */
	public function delItem($id){
		$GLOBALS['db']->query("delete from ".$this->TNAME.' where id='.$id);
		return $GLOBALS['db']->affectedRows();
	}

	/**
	 * 获取内容列表
	 * @param  array|string	$cond 查询条件
	 * @param  string 			$orderby 排序条件字符串
	 * @param  int 					$page 页码（-1代表全部）
	 * @param  int 					$psize 每页数量
	 * @return array
	 */
	function getItems($cond='',$orderby='',$page=1,$psize=10){
		return $GLOBALS['db']->select($this->TNAME,$cond,'id',$orderby,$page,$psize);
	}

	/**
	 * （编辑|新增)
	 * @param 	array $arrs 		编辑的键值对,不能包含id;
	 * @param 	int 	$id 			如果有id则为修改，否则为新增
	 * @return  int|boolean   	id(成功)|false（失败）
	 */
	public function setItem($arrs,$id=0){
		$ret = false;
		if ($id>0) {
			// 判断是否存在
			$cnt = $GLOBALS['db']->result('select count(*) from '.$this->TNAME.' where id='.$id);
			if ($cnt>0) {
				//更新数据
				if ($GLOBALS['db']->update($this->TNAME,$arrs,array('id'=>$id))) {
					$ret = $id;
				}
			}
		}else{
			$ret = $GLOBALS['db']->insert($this->TNAME,$arrs);
		}
		return $ret;
	}
}