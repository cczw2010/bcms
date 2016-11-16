<?php
// 喜欢（赞）模块数据库记录类
class Module_Like{
	const TNAME = 't_like';

	/**
 	 * 根据id获取内容
 	 * @param  int $id 内容id
 	 * @return array
 	 */
	static public function getItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '内容不存在';
		}
		return $ret;
	}
	/**
	 * 获取列表
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
	 * 新增,改方法不判断登陆 请自行判断
	 * @param array $arrs 编辑的键值对
	 * @return 返回$id
	 */
	static public function setItem($arrs){
		$ret = array('code'=> -1,'msg'=>'');
		// 新增时必填项判断
		$check = FormVerify::rule(
			array(isset($arrs['userid']) && FormVerify::must($arrs['userid']),'用户id不能为空'),
			array(isset($arrs['objid']) && FormVerify::must($arrs['objid']),'操作对象id不能为空'),
			array(isset($arrs['objtype']) && FormVerify::must($arrs['objtype']),'操作对象类型不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		$arrs['createdate'] = $_SERVER['REQUEST_TIME'];
		$arrs['ip'] = Helper::getClientIp();
		if ($id = $GLOBALS['db']->insert(self::TNAME,$arrs)) {
			$ret['code'] = 1;
			$ret['data'] = $id;
			$ret['msg'] = '操作成功';
		}else{
			$ret['msg'] = '更新失败';
		}
		return $ret;
	}
	/**
 	 * 删除内容
 	 * @param  int $id 日志id
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
			$ret['msg']='内容不存在';
		}
		return $ret;
	}

	/**
	 * 批量删除内容,慎重
	 * @param  array $conds 条件,不能为空 防止误清空
	 * @return array
	 */
	static public function delItems($conds){
		$ret = array('code'=>-1,'msg'=>'');
		if (empty($conds)) {
			$ret['msg'] = '条件不能为空';
		}else{
			$GLOBALS['db']->delete(self::TNAME,$conds);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}
		return $ret;
	}
}
