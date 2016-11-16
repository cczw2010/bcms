<?php
/**
 * 数据管理类（copy的Module_category.class.php）
 * 数据库操作类用了本系统的db.class.php,如果集成到其他环境，可以根据需要修改相应的数据库操作类
 * 本类要求数据表中包含, id,parentId,depth,path,weight（权重，同级排序） 字段
 */
final class Module_Area{
	const TNAME ='t_citys';

 	public static $statuss = array('不启用','启用');
 //数据处理部分==============================
	/**
	 * 获取某数据详情
	 * @param  int $id 获取某个类
	 * @return Array
	 */
	static public function getCate($id){
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
	 * 编辑数据
	 * @param array $arrs 编辑的数据数据键值对,不能包含id;不需要包含path,depth,程序会自己计算
	 * @param int $id 如果有id则为修改，否则为新增,同父类下不能有同名子类
	 * @return 返回$id
	 */
	static public function setCate($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		// 必填项，判断
		$check = FormVerify::rule(
			array(FormVerify::must($arrs['name']),'类名不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		// 根据新增或者修改，判断出实际需要操作的pid
		if ($id>0) {
			// 判断是否存在
			$ret1 = self::getCate($id);
			if ($ret1['code']>0) {
				$item = $ret1['data'];
				// 如果设置了pid,并且pid与当前pid不相等，则声明修改的pid
				if (isset($arrs['parentId']) && $arrs['parentId']!=$item['parentId']) {
					if ($arrs['parentId']!=$id) {
						$newpid = $arrs['parentId'];
					}else{
						$ret['msg'] = '不能以自身为父类';
					}
				}
			}else{
				$ret['msg'] = '要编辑的项目不存在';
			}
		}else{
			$newpid = isset($arrs['parentId'])?$arrs['parentId']:0;
		}
		// 根据实际的pid,并生成path
		if (empty($ret['msg']) && isset($newpid)) {
			if ($newpid>0) {
				// 判断新的newpid是否是当前id的子类
				if ($id>0 && self::isChild($newpid,$id)) {
					$ret['msg'] = '不能将父类移动到他的子类下';
				}else{
					$pret = self::getCate($newpid);
					if ($pret['code']>0) {
						// 当前项的path
						$path = $pret['data']['path'].','.$newpid;
						$depth = $pret['data']['depth']+1;
					}else{
						$ret['msg'] = '父类不存在';
					}
				}
			}elseif($newpid == 0){
				$path = "0";
				$depth = 1;
			}
		}
		// 判断没有错误就执行新增或者修改操作
		if (empty($ret['msg'])) {
			if (isset($path)) {
				$arrs['path'] = $path;
				$arrs['depth'] = $depth;
			}
			// 修改
			if ($id>0) {
				$GLOBALS['db']->update(self::TNAME,$arrs,array('id'=>$id));
				// 如果修改了parentId那么他下面所有的子类数目也要修改！！！！
				if (isset($path)) {
					// 判断$depth的增率
					$oldpath = $item['path'].','.$id;
					$newpath = $path.','.$id;	//注意虽然这和上面都有$fullid，千万别去掉，否则会误修改该id同级元素
					$prefixDepth = $depth - $item['depth'];
					$newdepth = $prefixDepth>=0?'depth+'.$prefixDepth:'depth'.$prefixDepth;
					$sql = 'update t_category set  depth = '.$newdepth.' , path = REPLACE(path,"'.$oldpath.'","'.$newpath.'")  where  path rlike "^'.$oldpath.'(,|$)"';
					$GLOBALS['db']->query($sql);
				}
			}else{
				if(!$id = $GLOBALS['db']->insert(self::TNAME,$arrs)){
					$ret['msg'] = '新增失败，请检查同父类下是否有同名子类';
					return $ret;
				}
			}
			$ret['code'] = 1;
			$ret['msg'] = 'success';
			$ret['data'] = $id;			
		}
		return $ret;
	}
	/**
	 * 检查某类是不是别类的子类（可能非直系子类）
	 */
	static public function isChild($id,$pid=0){
		if ($pid>0) {
			$ret = self::getCate($pid);
			if ($ret['code']>0) {
				$pcate = $ret['data'];
				$ppath = $pcate['path'].','.$pid;
			}
		}elseif($pid==0){
			$ppath = "0";
		}else{
			return false;
		}
		if (isset($ppath)) {
			$result = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id.' and path rlike "^'.$ppath.'(,|$)"');
			$rows = $GLOBALS['db']->fetchAll($result);
			if (count($rows)) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 检查是够存在子类
	 */
	static public function checkChild($id){
		$result = $GLOBALS['db']->query("select id from ".self::TNAME.' where parentId = '.$id);
		$rows = $GLOBALS['db']->numRows($result);
		return !!$rows;
	}
	/**
	 * 删除某类,请注意该操作会删除其下所有子类！！！！！
	 */
	static public function delCate($id){
		$ret = array('code'=>-1,'msg'=>'');
		$cate = self::getCate($id);
		if ($cate['code']>0) {
			$path = $cate['data']['path'].','.$id;
			$GLOBALS['db']->query("delete from ".self::TNAME.' where id='.$id.' or path rlike "^'.$path.'(,|$)"');
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}else{
			$ret['msg']='数据不存在';
		}
		return $ret;
	}
	/**
	 * 获取parent下的所有子类，并按树形结构排列数据(不遍历，不排序，效率高！)带缓存
	 * @param int $pid 父类id
	 * @param int $depth 获取深度，默认0是所有
	 * @param array $cond 条件
	 * @param int $page 页码，-1代表所有数据，不分页
	 * @param int $psize 每页数量
	 * @return array json
	 */
	static public function getChilds($pid=0,$depth=0,$cond=array(),$page=1,$psize=20){
		$ret = array('code'=>-1,'msg'=>'');
 		$vs = array();
		if ($pid>0) {
			$pcate = self::getCate($pid);
			if ($pcate['code']>0) {
				$_depth = $pcate['data']['depth'];
				$_path = $pcate['data']['path'].','.$pid;
			}
		}else{
			$_path = '0';
			$_depth = 0;
		}
		$where = $GLOBALS['db']->buildWhere($cond);
		if (empty($where)) {
			$where = ' where path rlike "^'.$_path.'(,|$)"';
		}else{
			$where .=' and path rlike "^'.$_path.'(,|$)"';
		}
		if (isset($_path)) {
			if ($depth>0) {
				$where.=' and depth<='.($_depth+$depth);
			}
			$where.=' order by moduleid,_order';

			// 获取总数
	 		$cnt = $GLOBALS['db']->result('select count(*) as num,concat(path,",",id) as _order from '.self::TNAME.$where);
	 		// 页码计算
	 		$vs['total'] = $cnt?$cnt:0;
	 		$vs['orderby'] = 'default';
	 		if ($page!=-1 ) {
	 			$vs['page'] = empty($page)?1:$page;
	 			$vs['psize'] = empty($psize)?20:$psize;
	 			$vs['maxpage'] = ceil($cnt/$vs['psize']);
	 			$vs['start'] = ($vs['page'] - 1)*$vs['psize'];
				$limit = ' limit '.$vs['start'].','.$vs['psize'];
	 		}else{
	 			$vs['page'] = 1;
	 			$vs['start'] = 0;
	 			$vs['maxpage'] = 1;
	 			$vs['psize'] = $cnt;
				$limit = '';
	 		}
	 		if ($cnt>0) {
	 			$sql = 'select *,concat(path,",",id) as _order from '.self::TNAME.$where.$limit;
				$query = $GLOBALS['db']->query($sql);
				$vs['items'] = $GLOBALS['db']->fetchAll($query,'id');
	 		}else{
	 			$vs['items'] = array();
	 		}
			$ret['code'] = 1;
			$ret['data'] = $vs;
		}
		return $ret;
	}
	/**
	 * 根据某数据id获取其父类树
	 * 
	 */
	static public function getParents($id){
		$ret = self::getCate($id);
		if ($ret['code']>0) {
			$parents = $GLOBALS['db']->select(self::TNAME,array('id'=>'in ('.$ret['data']['path'].')'),'id','order by depth asc',-1);
			$ret['data']['parents'] = $parents['list'];
		}
		return $ret;
	}
	/**
	 * 根据moduleid获取数据列表数据,封装的getChilds
	 * @param string $moduleid  数据的模块标示
	 * @param int $pid 父类id
	 * @param int $depth 获取深度，默认0是所有
	 * @param array $cond 条件
	 * @param int $page 页码，-1代表所有数据，不分页
	 * @param int $psize 每页数量
	 */
	static public function getChildsByApp($moduleid,$pid=0,$depth=0,$cond=array(),$page=1,$psize=20){
		$cond['moduleid'] = $moduleid;
		return self::getChilds($pid,$depth,$cond,$page,$psize);
	}
	/**
	 * 获取parent下的所有子类，并按树形结构排列数据(排序，增加制表符)
	 * @param array $datas getChilds中返回的数据数据数据
	 * @param int $selectid 初始化选中的数据id
	 * @param int $pIndent 父子间缩进的精度（默认3个字符）
	 * @return html
	 */
	static public function getChildsOptions($datas,$selectid=0,$pIndent=3){
		$tree = '';
		$selectid = empty($selectid)?0:intval($selectid);
		
		if (!empty($datas)) {
			$fItem = current($datas);
			$baseDepth = $fItem['depth']; // 第一个数据的depth为基准depth,默认1
			foreach($datas as $item) {
				if ($selectid>0&&$item['id'] == $selectid) {
					$selected = ' selected="selected" ';
				}else{
					$selected ='';
				}
				$prefix = '';
				if ($item['depth']>$baseDepth) {
					$prefix=array_fill(0,($item['depth']-$baseDepth)*$pIndent,"&nbsp;");
					$prefix= implode($prefix);
					$prefix.='└';
				}
				$tree.= '<option '.$selected.' value="'.$item['id'].'">'.$prefix.$item['name'].'</option>';
			}
		}
		return $tree;
	}

}