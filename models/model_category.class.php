<?php
/**
 * 分类管理处理类，
 * 本类要求数据表中包含, id,parentId,depth,path,weight（权重，同级排序） 字段
 */
final class Model_Category extends Model_Common{

 	public static $statuss = array('不启用','启用');

 	public function __construct($tname){
 		parent::__construct($tname);
 	}

 	/**
 	 * 对外开放初始化方法，不做成单例，因为本类可能被多个同类数据表调用
 	 * @param  [type] $tname [description]
 	 * @return [type]        [description]
 	 */
 	static public function init($tname){
 		return new self($tname);
 	}
	/**
	 * @Override
	 * 编辑数据,重写
	 * @param 	array $arrs 编辑的数据数据键值对,不能包含id;不需要包含path,depth,程序会自己计算
	 * @param 	int 	$id 	如果有id则为修改，否则为新增,同父类下不能有同名子类
	 * @return  int 	$id
	 */
	public function setItem($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		// 根据新增或者修改，判断出实际需要操作的pid
		if ($id>0) {
			// 判断是否存在
			if ($item = $this->getItem($id)) {
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
				if ($id>0 && $this->isChild($newpid,$id)) {
					$ret['msg'] = '不能将父类移动到他的子类下';
				}else{
					if ($pitem = $this->getItem($newpid)) {
						// 当前项的path
						$path = $pitem['path'].','.$newpid;
						$depth = $pitem['depth']+1;
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
				$GLOBALS['db']->update($this->TNAME,$arrs,array('id'=>$id));
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
				if(!$id = $GLOBALS['db']->insert($this->TNAME,$arrs)){
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
	 * @Override
	 * 删除某类,请注意该操作会删除其下所有子类！！！！！
	 * @return array
	 */
	public function delItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		if ($item = $this->getItem($id)) {
			$path = $item['path'].','.$id;
			$GLOBALS['db']->query("delete from ".$this->TNAME.' where id='.$id.' or path rlike "^'.$path.'(,|$)"');
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}else{
			$ret['msg']='数据不存在';
		}
		return $ret;
	}
	/**
	 * 检查某类是不是别类的子类（可能非直系子类）
	 * @return boolean
	 */
	public function isChild($id,$pid=0){
		if ($pid>0) {
			if ($pitem = $this->getItem($pid)) {
				$ppath = $pitem['path'].','.$pid;
			}
		}elseif($pid==0){
			$ppath = "0";
		}else{
			return false;
		}
		if (isset($ppath)) {
			$result = $GLOBALS['db']->query('select * from '.$this->TNAME.' where id='.$id.' and path rlike "^'.$ppath.'(,|$)"');
			if ($GLOBALS['db']->numRows($result)>0) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 检查是够存在子类
	 * @return boolean
	 */
	public function checkChild($id){
		$result = $GLOBALS['db']->query("select id from ".$this->TNAME.' where parentId = '.$id);
		$rows = $GLOBALS['db']->numRows($result);
		return !!$rows;
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
	public function getChilds($pid=0,$depth=0,$cond=array(),$page=1,$psize=20){
		$ret = array('code'=>-1,'msg'=>'');
 		$vs = array();
		if ($pid>0) {
			if ($pitem = $this->getItem($pid)) {
				$_depth = $pitem['depth'];
				$_path = $pitem['path'].','.$pid;
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
			$where.=' order by appid,_order';

			// 获取总数
	 		$cnt = $GLOBALS['db']->result('select count(*) as num,concat(path,",",id) as _order from '.$this->TNAME.$where);
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
	 			$sql = 'select *,concat(path,",",id) as _order from '.$this->TNAME.$where.$limit;
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
	 * 根据id获取某类的父类树
	 * @return array
	 */
	public function getParents($id){
		$ret = array();
		if ($item = $this->getItem($id)) {
			$parents = $GLOBALS['db']->select($this->TNAME,array('id'=>'in ('.$item['path'].')'),'id','order by depth asc',-1);
			$ret = $parents['list'];
		}
		return $ret;
	}
	/**
	 * 获取parent下的所有子类，并按树形结构排列数据(排序，增加制表符)
	 * @param array $datas getChilds中返回的数据数据数据
	 * @param int $selectid 初始化选中的数据id
	 * @param int $pIndent 父子间缩进的精度（默认3个字符）
	 * @return html
	 */
	public function getChildsOptions($datas,$selectid=0,$pindent=3){
		$tree = '';
		$selectid = empty($selectid)?0:intval($selectid);
		
		if (!empty($datas)) {
			$fitem = current($datas);
			$baseDepth = $fitem['depth']; // 第一个数据的depth为基准depth,默认1
			foreach($datas as $item) {
				if ($selectid>0&&$item['id'] == $selectid) {
					$selected = ' selected="selected" ';
				}else{
					$selected ='';
				}
				$prefix = '';
				if ($item['depth']>$baseDepth) {
					$prefix=array_fill(0,($item['depth']-$baseDepth)*$pindent,"&nbsp;");
					$prefix= implode($prefix);
					$prefix.='└';
				}
				$tree.= '<option '.$selected.' value="'.$item['id'].'">'.$prefix.$item['name'].'</option>';
			}
		}
		return $tree;
	}
}