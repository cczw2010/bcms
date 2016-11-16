<?php
// 属性模块
class Module_Prop{
	const TNAME = 't_prop';
	const TPITEM = 't_prop_item';
 	public static $statuss = array('不启用','启用');

 	/**
 	 * 根据id获取属性
 	 * @param  int $id 属性id
 	 * @return array
 	 */
	static public function getItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '属性不存在';
		}
		return $ret;
	}
	/**
	 * 获取属性列表
	 * @param  array	$cond 查询条件
	 * @param  string $orderby 排序条件字符串
	 * @param  int 		$page 页码（-1代表全部）
	 * @param  int 		$psize 每页数量
	 * @return array
	 */
	static function getItems($cond=array(),$orderby='',$page=1,$psize=10){
		return $GLOBALS['db']->select(self::TNAME,$cond,'id',$orderby,$page,$psize);
	}

	/**
	 * （编辑|新增）属性
	 * @param array $arrs 编辑的键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 返回$id
	 */
	static public function setItem($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		// 必填项，判断
		$check = FormVerify::rule(
			array(FormVerify::must($arrs['name']),'属性名称不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}	
		
		if ($id>0) {
			// 判断是否存在
			$cnt = $GLOBALS['db']->result('select count(*) from '.self::TNAME.' where id='.$id);
			if ($cnt==0) {
				$ret['msg'] = '要编辑的属性不存在';
			}else{
				// 更新数据
				$result = $GLOBALS['db']->update(self::TNAME,$arrs,array('id'=>$id));
				if (!$result) {
					$ret['msg'] = '更新失败';
				}
			}
		}else{
			$id = $GLOBALS['db']->insert(self::TNAME,$arrs);
			if (empty($id)) {
				$ret['msg'] = '新增失败';
			}
		}
		if ($id>0 && empty($ret['msg'])) {
			$ret['code'] = 1;
			$ret['data'] = $id;
		}
		return $ret;
	}
	/**
 	 * 删除属性
 	 * @param  int $id 属性id
 	 * @return array
 	 */
	static public function delItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		$cate = self::getItem($id);
		if ($cate['code']>0) {
			$GLOBALS['db']->query("delete from ".self::TNAME.' where id='.$id);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}else{
			$ret['msg']='属性不存在';
		}
		return $ret;
	}

	/**
	 * 批量删除属性
	 * @param  array $cond 条件
	 * @return array
	 */
	static public function delItems($cond){
		$ret = array('code'=>-1,'msg'=>'');
		$GLOBALS['db']->delete(self::TNAME,$cond);
		$ret['code'] = 1;
		$ret['data'] = $GLOBALS['db']->affectedRows();
		return $ret;
	}
}