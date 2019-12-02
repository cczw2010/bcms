<?php
/**
 * 采用国家统计局数据,不支持编辑,所以可做都做成缓存，杠杠的
 * 本类要求数据表中包含, id,parentId,depth,path,weight（权重，同级排序） 字段
 */
final class Module_Origin{
	const TNAME ='t_origin';
 	public static $statuss = array('不启用','启用');
	/**
	 * 获取
	 * @param  int $id 获取某个对象
	 * @return Array|false
	 */
	static public function getItem($id){
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		return $GLOBALS['db']->fetchArray($query);
	}
	/**
	 * 检查某类是不是别类的直系子类
	 */
	static public function isChild($id,$pid=0){
		return $GLOBALS['db']->result('select count(*) from '.self::TNAME.' where id='.$id.' and parentid = '.$pid);
	}
	/**
	 * 获取符合条件的所有对象（cache）
	 * @param array $cond 条件
	 * @param int $page 页码，-1代表所有数据，不分页
	 * @param int $psize 每页数量
	 * @return json
	 */
	static public function getItems($cond=array(),$page=1,$psize=20){
		$where = $GLOBALS['db']->buildWhere($cond);
		// 获取总数
 		$cnt = $GLOBALS['db']->result('select count(*) as num from '.self::TNAME.$where);
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
 			$sql = 'select * from '.self::TNAME.$where.$limit;
			$query = $GLOBALS['db']->query($sql);
			$vs['items'] = $GLOBALS['db']->fetchAll($query,'id');
 		}else{
 			$vs['items'] = array();
 		}

		return $vs;
	}
}