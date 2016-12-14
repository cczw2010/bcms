<?php
// log日志数据库记录类
class Module_Log{
	const TNAME = 't_log';

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
	 * 获取日志列表
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
	 * 管理员新增日志 不可编辑， 不可用于用户，用户请用文本日志
	 * @param array $arrs 编辑的键值对,不能包含id;
	 * @param boolean $ismanager 是否管理员日志;
	 * @return 返回$id
	 */
	static public function setItem($arrs){
		$ret = array('code'=> -1,'msg'=>'');
		// 必填项，判断
		$check = FormVerify::rule(
			array(FormVerify::must($arrs['message']),'消息不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		if (!isset($arrs['userid'])) {
			$user = Module_Manager::getLoginUser();
			if (!empty($user)) {
				$arrs['userid'] = $user['id'];
				$arrs['username'] = $user['username'];
			}else{
				$arrs['userid'] = 0;
				$arrs['username'] = '';
			}
		}
		if (!isset($arrs['username'])) {
			$arrs['username'] = '';
		}
		$arrs['message'] = addslashes(stripslashes($arrs['message']));
		$arrs['createdate'] = $_SERVER['REQUEST_TIME'];
		$arrs['ip'] = Helper::getClientIp();

		$id = $GLOBALS['db']->insert(self::TNAME,$arrs);
		if (empty($id)) {
			$ret['msg'] = '新增失败';
		}else{
			$ret['code'] = 1;
			$ret['data'] = $id;
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
	 * 批量删除日志
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
