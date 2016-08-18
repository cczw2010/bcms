<?php
/**
 * 用户组模块统一管理类,纯静态类，值得注意的是
 * 用户数据表中group的默认值 0为普通用户，-1代表匿名用户。这两个组是写死的，
 * 不能增删改，不入分组库
 */
final class Module_Group{
	const APPID = 3;
	const APPNAME = '用户组模块';
	const TNAME = 't_group';
	const GROUP_SUPER = -1;
	const GROUP_GENERAL = 0;
	// 分组类型 0 管理员分组， 1 普通用户分组
	const TYPE_USER = 1;
	const TYPE_MANAGER = 0;
	public static $types = array('管理员分组','用户分组');
	/**
	 * 判断是否管理员（注意超级管理员组GROUP_SUPER不在检测之列）
	 * @param int $groupid  用户的分组id
	 * @return 如果是自定义的管理员返回权限列表字符串，如果不是返回FALSE
	 */
	static public function isManager($groupid){
		$groupid = intval($groupid);
		if($groupid!=self::GROUP_GENERAL){
			$ret = self::getGroup($groupid);
			if ($ret['code']>0&&$ret['data']['types']==self::TYPE_MANAGER) {
				return $ret['data']['rights'];
			}
		}
		return 0;
	}
	/**
 	 * 根据id获取分组
 	 * @param  int $id 内容id
 	 * @return array
 	 */
	static public function getGroup($id){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '分组不存在';
		}
		return $ret;
	}
	/**
	 * 获取全部用户组数组,'0'=>'普通用户'为固定项，不可修改删除
	 * @param  array $cond 查询条件
	 * @return array
	 */
	static public function getGroups($cond=array(),$index='id'){
		$rets = $GLOBALS['db']->select(self::TNAME,$cond,$index,'',-1);
		// if (empty($cond)) {
			// $rets['list'][0] = array('id'=>0,'name'=>'默认用户','desc'=>'注册用户的默认权限,可访问前端页面，不可访问后台页面','rights'=>'');
		// }
		return $rets;
		// $list = array_merge($vs['list'],self::$fixGroups);
	}
	/**
	 * 编辑用户组
	 * @param array $arrs 编辑的用户组数据键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 返回$id
	 */
	static public function setGroup($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		if ($id>0) {
			// 如果存在必填项，判断
			if (isset($arrs['name']) && empty($arrs['name'])) {
				$ret['msg'] = '名称不能为空';
			}else{
				// 判断是否存在
				$cnt = $GLOBALS['db']->result('select count(*) from '.self::TNAME.' where id='.$id);
				if ($cnt==0) {
					$ret['msg'] = '要编辑的项目不存在';
				}
			}
		}
		// 判断同名,分组是给后台用的，准有name
		if (empty($ret['msg'])) {
			$sql = 'select count(*) from '.self::TNAME.' where name="'.$arrs['name'].'"';
			if ($id>0) {
				$sql.=' and id!='.$id;
			}
			$cnt = $GLOBALS['db']->result($sql);
			if ($cnt>0) {
				$ret['msg'] = '存在同名分组，请重新修改分组名称！';
			}
		}
		// 插入或者更新数据
		if (empty($ret['msg'])) {
			if ($id>0) {
				$GLOBALS['db']->update(self::TNAME,$arrs,array('id'=>$id));
			}else{
				// 新增判断必填项
				if (empty($arrs['name'])) {
					$ret['msg'] = '名称不能为空';
				}else{
					$id = $GLOBALS['db']->insert(self::TNAME,$arrs);
				}
			}
			if ($id>0) {
				$ret['code'] = 1;
				$ret['data'] = $id;
			}
		}
		return $ret;
	}
	/**
	 * 删除用户组
	 * @param  int $id  用户组id
	 */
	static public function delGroup($id){
		$ret = array('code'=>-1,'msg'=>'');
		$group = self::getGroup($id);
		if ($group['code']>0) {
			$GLOBALS['db']->query("delete from ".self::TNAME.' where id='.$id);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}else{
			$ret['msg']=$group['msg'];
		}
		return $ret;
	}
}