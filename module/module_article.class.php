<?php
/**
 * 内容模块统一处理类,纯静态类
 */
final class Module_Article{
	const APPID = 2;
	const APPNAME = '内容模块';
	const TNAME = 't_article';
	const MINTITLE = 3;
	//将内容的封面图当做附件存储，对应的表中的objtype
	const ATTACHTYPE = 'articlecover';

 	public static $statuss = array('未发布','已发布');

 	/**
 	 * 根据id获取内容
 	 * @param  int $id 内容id
 	 * @return array
 	 */
	static public function getItem($id){
		$ret = array('code'=>-1,'msg'=>'');

		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetch_array($query)) {
			$ret['code'] = 1;
			// 封面图
			$covers = Module_Attach::getItems(array('objid'=>$id,'objtype'=>self::ATTACHTYPE),'order by id',-1);
			$item['covers'] = $covers['list'];
			// 抽出第一个作为主封面图，后期可配置
			$item['cover'] = current($covers['list']);

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
			// 删除关联的附件
			Module_Attach::delItems(array('objid'=>$id,'objtype'=>self::ATTACHTYPE));
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affected_rows();
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
		$ret = $GLOBALS['db']->select(self::TNAME,$cond,'id',$orderby,$page,$psize);
		foreach ($ret['list'] as $id => &$item) {
			// 封面图
			$covers = Module_Attach::getItems(array('objid'=>$id,'objtype'=>self::ATTACHTYPE),'order by id',-1);
			$item['covers'] = $covers['list'];
			// 抽出第一个作为主封面图，后期可配置
			$item['cover'] = current($covers['list']);
		}
		return $ret;
	}

	/**
	 * （编辑|新增）文章，不处理封面图，请单独用setCovers处理
	 * @param array $arrs 编辑的键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 返回$id
	 */
	static public function setItem($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		// 要判断必填

		$check = FormVerify::rule(
			array(FormVerify::len($arrs['title'],self::MINTITLE),'文章标题字数不能少于'.self::MINTITLE),
			array(FormVerify::must($arrs['title']),'文章内容不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		$arrs['content'] = addslashes(stripslashes($arrs['content']));
		// 过滤敏感词
		$arrs['content'] = Module_Sword::filter($arrs['content']);
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
	/**
	 * 设置成品图
	 * @param array  $coverids 附件id数组（与coverpaths一一对应，值为空的项代表新增）
	 * @param array  $coverpaths 附件成品图路径部分数组
	 * @param array  $covernames 附件成品图文件名部分数组
	 * @param array  $coveronames 附件成品图原文件名部分数组
	 * @param int $objid  内容id
	 * @return boolean 成功失败
	 */
	static public function setCovers($coverids=array(),$coverpaths=array(),$covernames=array(),$coveronames=array(),$objid){
		$attachs = Module_Attach::getItems(array('objid'=>$objid,'objtype'=>self::ATTACHTYPE),false,-1);
		$attachs = $attachs['list'];
		$attachids = array_keys($attachs);
		// 不在$coverids数组中的成品图是要删除的
		$dels = array();
		foreach ($attachs as $attachid => $attach) {
			if (!in_array($attachid, $coverids)) {
				$dels[]=$attachid;
			}
		}
		// 遍历当前coverids，不为空的修改，为空的新增
		foreach ($coverids as $idx=>$coverid) {
			$arrs = array(
				'objtype'=>self::ATTACHTYPE,
				'objid'=>$objid,
				'lastdate'=>$_SERVER['REQUEST_TIME']
				);
			if (!empty($coverpaths[$idx])) {
				$arrs['fpath'] = $coverpaths[$idx];
				$arrs['fname'] = $covernames[$idx];
				$arrs['oname'] = $coveronames[$idx];
				if (empty($coverid)) {
					// 新增
					$arrs['createdate'] = $_SERVER['REQUEST_TIME'];
					$arrs['status'] = 1;
					$coverid=0;
				}
				$ret = Module_Attach::setItem($arrs,$coverid,true);
				if ($ret['code']<0) {
					return false;
				}
			}elseif(!empty($coverid)){
				// 路径为空是要删除的
				$dels[]=$coverid;
			}
		}
		// 删除
		if (!empty($dels)) {
			Module_Attach::delItems(array('id'=>'in ('.implode(',', $dels).')'));
		}
		return true;
	}

}