<?php 
/**
 * 附件表
 * 如果是图片附件将生成c240,c120,p240,p120前缀的4种缩略图(c正方形，p等比)
 */
final class Module_Attach{
	const TNAME = 't_attach';
 	public static $statuss = array('不启用','启用');

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
	 * 获取附件列表
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
	 * （编辑|新增）附件
	 * @param array $arrs 编辑的键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @param boolean $resize 如果是图片文件，是否自动生成缩略图
	 * @return 返回$id
	 */
	static public function setItem($arrs,$id=0,$resize=false){
		$ret = array('code'=> -1,'msg'=>'');
		// 必填项，判断
		$check = FormVerify::rule(
			array(FormVerify::must($arrs['objid']),'目标id不能为空'),
			array(FormVerify::must($arrs['fpath']),'附件地址不能为空'),
			array(FormVerify::must($arrs['objtype']),'目标对象不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		// 自动获取文件类型
		$fileurl = BASEPATH.$arrs['fpath'].'/'.$arrs['fname'];
		if (!is_file($fileurl)) {
			$ret['msg'] = '文件不存在';
			return $ret;
		}
		$fileinfo = pathinfo($fileurl);
		$arrs['oext'] = $fileinfo['extension'];
		$arrs['osize'] = filesize($fileurl);
		// 如果是图片就获取宽高
		$oext = strtolower($arrs['oext']);
		if (in_array($oext, array('jpg','gif','jpeg','png','bmp'))) {
			$finfo = @getimagesize($fileurl);
			if ($finfo) {
				$arrs['owidth'] = $finfo[0];
				$arrs['oheight'] = $finfo[1];
			}
		}
		if ($id>0) {
			// 判断是否存在
			$cnt = $GLOBALS['db']->result('select count(*) from '.self::TNAME.' where id='.$id);
			if ($cnt==0) {
				$ret['msg'] = '要编辑的内容不存在';
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
			// 如果是图片并且需要生成缩略图
			if($resize&&isset($arrs['owidth'])){
				$c240 = SImage::csize($fileurl,240);
				$p240 = SImage::psize($fileurl,240);
				SFile::move($c240,$arrs['fpath'].'/c240_'.$arrs['fname']);
				SFile::move($p240,$arrs['fpath'].'/p240_'.$arrs['fname']);
			}
			$ret['code'] = 1;
			$ret['data'] = $id;
		}
		return $ret;
	}
	/**
 	 * 删除内容
 	 * @param  int $id 附件id
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
	 * 批量删除附件
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

	// 批量处理某一objtype,objid的删除，新增，变更
	// 并按照当前数组顺序插入orderidx
	static function setPics($objtype,$objid,$ids=array(),$paths=array(),$names=array(),$descs=array(),$linkss=array()){
		$attachs = Module_Attach::getItems(array('objid'=>$objid,'objtype'=>$objtype),false,-1);
		$attachs = $attachs['list'];
		$attachids = array_keys($attachs);
		// 不在$ids数组中的成品图是要删除的
		$dels = array();
		foreach ($attachs as $attachid => $attach) {
			if (!in_array($attachid, $ids)) {
				$dels[]=$attachid;
			}
		}
		// 遍历当前ids，不为空的修改，为空的新增
		$order = 0;
		foreach ($ids as $idx=>$id) {
			$arrs = array(
				'objtype'=>$objtype,
				'objid'=>$objid,
				'orderid'=>++$order,
				'lastdate'=>$_SERVER['REQUEST_TIME']
				);
			if (!empty($paths[$idx])) {
				$arrs['fpath'] = $paths[$idx];
				$arrs['fname'] = $names[$idx];
				$arrs['desc'] = $descs[$idx];
				$arrs['links'] = $linkss[$idx];
				if (empty($id)) {
					// 新增
					$arrs['createdate'] = $_SERVER['REQUEST_TIME'];
					$arrs['status'] = 1;
					$id=0;
				}
				$ret = Module_Attach::setItem($arrs,$id,false);
				if ($ret['code']<0) {
					return false;
				}
			}elseif(!empty($id)){
				// 路径为空是要删除的
				$dels[]=$id;
			}
		}
		// 删除
		if (!empty($dels)) {
			Module_Attach::delItems(array('id'=>'in ('.implode(',', $dels).')'));
		}
		return true;
	}
}