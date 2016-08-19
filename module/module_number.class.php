<?php
/**
 * 数字模块统一管理类  v2.0
 * 通过appid来判断分类的对象类型
 * 数据库操作类用了本系统的db.class.php,如果集成到其他环境，可以根据需要修改相应的数据库操作类
 * 本类要求分类表中包含, id,parentId,depth,path,weight（权重，同级排序） 字段
 */
final class Module_Number
{
	const APPID = 51;
	const APPNAME = '数字模块';
	const TNAME ='t_number';
	
	static function getItem($id=1)
	{
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '未查到数据';
		}
		return $ret;
	}

	static public function setItem($arrs,$id=0)
	{
		$ret = array('code'=> -1,'msg'=>'');
		// 要判断必填
		$check = FormVerify::rule(
			array(FormVerify::must($arrs['need_user_num']),'需求客数字必填'),
			array(FormVerify::must($arrs['designer_num']),'设计师数字必填')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		if ($id>0) {
			// 判断是否存在
			$cnt = $GLOBALS['db']->result('select count(*) from '.self::TNAME.' where id='.$id);
			if ($cnt==0) {
				$ret['msg'] = '要编辑的内容不存在';
			}else{
				//更新数据
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
}
