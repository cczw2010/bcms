<?php
/**
 * 手记模块统一处理类,纯静态类
 */
final class Module_Diary{
	const TNAME = 't_diary';
	const MINTITLE = 3;

 	public static $statuss = array('未发布','已发布');

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
 	 * 删除内容
 	 * @param  int $id 内容id
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
	 * 获取内容列表
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
	 * （编辑|新增）
	 * @param array $arrs 编辑的键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 返回$id
	 */
	static public function setItem($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		// 要判断必填

		$check = FormVerify::rule(
			array(FormVerify::len($arrs['title'],self::MINTITLE),'标题字数不能少于'.self::MINTITLE),
			array(FormVerify::must($arrs['title']),'文章内容不能为空')
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