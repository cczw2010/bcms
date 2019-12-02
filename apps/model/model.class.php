<?php
// model的基础类，提供model表的通用增删改查操作
class Modelcommon{
  // 操作表
  protected $table = "";
  /**
   * 设置当前model操作的table表
   */
  public function setTable($tablename){
    $this->table = $tablename;
  }

  /**
 	 * 根据id获取内容
 	 * @param  int $id 内容id
 	 * @return array|FALSE
 	 */
	public function getItemById($id){
    return $this->getItem(['id'=>$id]);
  }

  /**
 	 * 根据条件获取内容
 	 * @param  array|string db类中的检索条件
 	 * @return array|FALSE
 	 */
	public function getItem($cond){
    $where = $GLOBALS['db']->buildWhere($cond);
		$result = $GLOBALS['db']->query('select * from '.$this->table.$where);
		return $GLOBALS['db']->fetchArray($result);
  }

  /**
	 * 获取内容列表
	 * @param  array	$cond 查询条件
   * @param  array  $keys 返回的数据key 指表，默认为全部
   * @param  int 		$page 页码（-1代表全部）
   * @param  int 		$psize 每页数量,默认10
	 * @param  string $indexColumn 可以这只数组索引的列比如id
	 * @param  string $orderby 排序条件字符串
	 * @return array
	 */
	function getItems($cond,$keys=null,$page=1,$psize=10,$indexColumn='',$orderby=''){
    return $GLOBALS['db']->keys($keys)->select($this->table,$cond,$indexColumn,$orderby,$page,$psize);
  }
  
  /**
	 * （编辑|新增）
	 * @param array $arrs 编辑的键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 新增成功返回id, 更新成功返回TRUE 新增失败和更新失败返回FALSE
	 */
	public function setItem($arrs,$id=0){
		if ($id>0) {
			// 判断是否存在
			$cnt = $GLOBALS['db']->result('select count(*) from '.$this->table.' where id='.$id);
			if ($cnt==0) {
        // id不存在
				return false;
			}else{
				//更新数据
				return $GLOBALS['db']->update($this->table,$arrs,array('id'=>$id));
			}
		}
		return $GLOBALS['db']->insert($this->table,$arrs);
	}
  
  /**
 	 * 删除内容
 	 * @param  int $id 内容id
 	 * @return int 影响的行数
 	 */
	public function delItem($id){
		$GLOBALS['db']->query("delete from ".$this->table.' where id='.$id);
		return $GLOBALS['db']->affectedRows();
  }
  


}