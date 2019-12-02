<?php
/*
	mysql 数据库访问操作类 
	配置文件:
	array(
		host=>127.0.0.1, （*）地址
		port=>3366,		 （*）端口
		user=>,			 （*）用户
		pass=>,			 （*）密码
		dbname=>,		  默认库
		ttl=>,		  	//默认缓存时间秒,0代表永不过期,-1不缓存
 	)
*/

class Db_mysql implements Db{
	private static $instance;
	private $dbconn;
	private $result;
	private $sql;
	private $db_host;
	private $db_port;
	private $db_user;
	private $db_pass;
	private $db_name;
	private $keys = null;


	function __construct($config=array()){
		if (isset(self::$instance)) {
			return self::$instance;
		}
		if (empty($config)) {
			throw new Exception('数据库连接错误,请检查配置信息');
		}
		$this->db_host=isset($config['host'])?$config['host']:'localhost';
		$this->db_port=isset($config['port'])?$config['port']:3306;
		$this->db_user=isset($config['user'])?$config['user']:'admin';
		$this->db_pass=isset($config['pass'])?$config['pass']:'';
		$this->db_name=isset($config['dbname'])?$config['dbname']:'';
		//建立连接
		$this->connect();
		//设置编码 （考虑换成其他方式）
		$this->query('set names utf8');
		self::$instance=$this;
	}
	public function connect(){
		if(!isset($this->dbconn)){
			$this->dbconn = mysqli_connect($this->db_host.':'.$this->db_port,$this->db_user,$this->db_pass,$this->db_name);
			if (!$this->dbconn){
				throw new Exception('数据库连接错误: ' . mysqli_error($this->dbconn));
			}
		}
	}
	public function close(){
		if(isset($this->dbconn)){
			if(!@mysqli_close($this->dbconn)){
				throw new Exception('关闭数据库连接失败: ' . mysqli_error($this->dbconn));
			};
		}
	}
	public function selectDb($dbname){
		if(isset($this->dbconn)){
			return !!mysqli_select_db($dbname,$this->dbconn);
		}
		return false;
	}
	public function createDb($dbname){  
		return $this->query('create database '.$dbname);  
	}
	public function getDBInfo(){
		$ret = array();
		// 数据库基础信息
		$result = $this->query('SHOW variables');
		$ret['db'] = $this->fetchAll($result,'Variable_name');

		// 获取所有表的信息
		$result = $this->query('SHOW TABLE STATUS');
		$ret['tables']  = $this->fetchAll($result,'Name');
		
		return $ret;
	}
	public function get_table_info($tablename){
		$result = $this->query('SHOW COLUMNS from '.$tablename);
		$ret = $this->fetchAll($result);
		return $ret;	
	}
	public function getlastsql(){
		return $this->sql;
	}
	public function query($sql){
		$this->sql=$sql;
		$this->result=mysqli_query($this->dbconn,$sql);
		if ($this->result!==false) {
			return $this->result;
		}
		throw new Exception("sql语句运行错误，请检查:".$sql);
		return false;
	}
	public function seek($result,$pos=0){
		$max=$this->numRows($result);
		if ($max>0&&$max>$pos) {
			mysqli_data_seek($result,$pos);
		}
	}
	public function getconn(){
		return $this->dbconn;
	}
	public function free($result){
		$result = !empty($result)?$this->result:$result;
		mysqli_free_result($result);
	}
 	public function affectedRows(){
		return mysqli_affected_rows($this->dbconn);
	}
	public function insertId(){
		return mysqli_insert_id($this->dbconn);
	}
	public function numRows($result){
		$result = !empty($result)?$result:$this->result;
		return mysqli_num_rows($result);
	}
	public function keys($keyarray){
		if (!empty($keyarray)) {
			$this->keys = implode(',',$keyarray);
		}
		return self::$instance;
	}
	public function fetchArray($result){
		return mysqli_fetch_assoc($result);
	}
	public function fetchAssoc($result){
		return mysqli_fetch_row($result);
	}
 	public function fetchAll($result,$index=''){
 		$ret=array();
 		if ($result) {
	 		$this->seek($result);
	 		$index=trim($index);
	 		while ($row=$this->fetchArray($result)) {
	 			if (strlen($index)>0 && isset($row[$index])) {
	 				$ret[$row[$index]]=$row;
	 			}else{
	 				$ret[]=$row;
	 			}
	 		}
 		}
 		return $ret;
 	}
	/*简化select*/
 	// $page 页码如果该值设为-1则表示全部不分页，$psize=每页大小
 	public function select($table,$cond=array(),$index='',$orderby='',$page=1,$psize=20){
 		$vs = array('list'=>array(),'pcnt'=>0);
 		$where = '';
 		if (isset($cond)) {
 			$where=$this->buildWhere($cond);
 		}
 		// 获取总数
 		$cnt = $this->result('select count(*) as num from '.$table.$where);
 		// 页码计算
 		$vs['total'] = $cnt?$cnt:0;
 		$vs['orderby'] = empty($orderby)?'':$orderby;
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
 		
 		// 如果没有超过最大值
 		if ($cnt>$vs['start']) {

			$keys = empty($this->keys)?'*':$this->keys;
 			$this->keys = null;
 			$sql='select '.$keys.' from '.$table.$where.' '.$vs['orderby'].$limit;
	 		$result=$this->query($sql);
	 		$vs['list'] = $this->fetchAll($result,$index);
	 		$vs['pcnt'] = count($vs['list']);
 		}
 		return $vs;
 	}
 	/**
 	 * 返回结果集中某行某列的值
 	 * @param  string|mysqlquery $query mysql字符串或者查询的query结果
 	 * @param  int $row 第几行
 	 * @param  int $field 第几列
 	 * @return value
 	 */
 	public function result($query, $row = 0, $field = 0) {
		if (is_string($query)){
			$result = $this->query($query);
		}else{
			$result = $query;
		}
		if ($result) {
			// 数据量大时效率较低，不过基本是为了取聚合的结果的 就无所谓了
			$result->data_seek($row);
			$datarow = $this->fetchArray($result);
			if(is_numeric($field)){
				return array_values($datarow)[$field];
			}else{
				return $datarow[$field];
			}
		}
		return null;
	}
	/*简化insert*/
 	public function insert($table,$arr){
		$sql = 'INSERT IGNORE INTO `'.$table.'`';  
		if(!is_array($arr)||empty($arr)){  
			throw new Exception("mysql->insert: 请输入参数数组！");  
		}else{  
			$k = '';  
			$v = '';  
			foreach($arr as $key => $value){  
				$k .= "`$key`,";  
				$v .= "'".$value."',";  
			}  
		}  
		$sql = $sql.' ('.substr($k,0,-1).') VALUES ('.substr($v,0,-1).')';
		if ($this->query($sql)) {
			return $this->insertId();
		}else{
			return false;
		}
 	}
 	//简化的update  
	public function update($table,$arr,$cond=''){  
		$sql = 'UPDATE `'.$table.'` SET ';  
		if(!is_array($arr)||empty($arr)){  
			throw new Exception('mysql->update: 请输入参数数组！');  
		}
		foreach($arr as $key => $value){
			if (is_numeric($key)) {
				$sql .= ' '.$value.' ,';  
			}else{
				$sql .= ' `'.$key.'` = "'.$value.'" ,';  
			}
		}  
		$sql = substr($sql,0,-1);
		$sql.=$this->buildWhere($cond);
		return $this->query($sql);
	}  
	//简化的delete  
	public function delete($table,$cond=''){   
		$where=$this->buildWhere($cond);
		$sql = 'DELETE FROM '.$table.' '.$where;
		return $this->query($sql);  
	}
	public function getLastErr(){
		return mysqli_error($this->conn);
	}
	public function buildWhere($cond){
		$where="";
		if (!empty($cond)) {
			if (is_array($cond)) {
			 	$where=" where ";
			 	for ($i=0,$l=count($cond); $i < $l; $i++) {
			 		$key = key($cond);
			 		$val=trim(current($cond));
			 		if (is_numeric($key)) {
			 			$where.=" ".$val;
			 		}else{
				 		//排除值中带有条件表达式的
				 		if(preg_match('/^(>|=|<|!|like|in|not|rlike)/',$val)){
				 			$where.= " `".$key."` ".$val." ";
				 		}else{
				 			$where.= " `".$key."` = '".$val."' ";
				 		}
			 		}
			 		$where.=($i==$l-1)?'':' and';
			 		next($cond);
			 	}  
    	}elseif (is_string($cond)) {
    		$where=" where ".$cond;
    	}
		}
		return $where;
	}
	// 事务开始
	public function transBegin(){
		$this->query('BEGIN');
	}
	// 事务提交
	public function transCommit(){
		$this->query('COMMIT');
	}
	// 事务回滚
	public function transBack(){
		$this->query('ROLLBACK');
	}
}