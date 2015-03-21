<?php
/*
	mysql 数据库访问操作类  by awen
	配置文件:
	array(
		host=>127.0.0.1, （*）地址
		port=>3366,		 （*）端口
		user=>,			 （*）用户
		pass=>,			 （*）密码
		dbname=>,		  默认库
		group=>,		  缓存组
 	)
*/

class Db_mysql implements db{
	private static $instance;
	private $dbconn;
	private $result;
	private $sql;
	private $db_host;
	private $db_port;
	private $db_user;
	private $db_pass;
	private $db_name;
	private $cache_group;

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
		$this->cache_group=isset($config['group'])?$config['group']:'db';
		//建立连接
		$this->connect();
		//设置编码 （考虑换成其他方式）
		$this->query('set names utf8');
		self::$instance=$this;
	}
	public function connect(){
		if(!isset($this->dbconn)){
			$this->dbconn = mysql_connect($this->db_host.':'.$this->db_port,$this->db_user,$this->db_pass);
			if (!$this->dbconn){
				throw new Exception('数据库服务器连接错误: ' . mysql_error());
			}else{
				if (strlen($this->db_name)>0) {
					if (!$this->selectDB($this->db_name)) {
						throw new Exception('数据库连接错误: ' . mysql_error());
					}
				}
			}
		}
	}
	public function close(){
		if(isset($this->dbconn)){
			if(!@mysql_close($this->dbconn)){
				throw new Exception('关闭数据库连接失败: ' . mysql_error());
			};
		}
	}
	public function selectDB($dbname){
		if(isset($this->dbconn)){
			return !!mysql_select_db($dbname,$this->dbconn);
		}
		return false;
	}
	public function createDB($dbname){  
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
	public function getTableInfo($tablename){
		$result = $this->query('SHOW COLUMNS from '.$tablename);
		$ret = $this->fetchAll($result);
		return $ret;	
	}
	public function getLastSql(){
		return $this->sql;
	}
	public function query($sql){
		$this->sql=$sql;
		$this->result=mysql_query($sql);
		if ($this->result!==false) {
			return $this->result;
		}
		throw new Exception("sql语句运行错误，请检查:".$sql);
		return false;
	}
	public function seek($result,$pos=0){
		$max=$this->numRows($result);
		if ($max>0&&$max>$pos) {
			mysql_data_seek($result,$pos);
		}
	}
	public function getConn(){
		return $this->dbconn;
	}
	public function free($result){
		$result = !empty($result)?$result:$this->result;
		mysql_free_result($result);
	}
 	public function affectedRows(){
		return mysql_affected_rows();
	}
	public function insertId(){
		return mysql_insert_id();
	}
	public function numRows($result){
		$result = !empty($result)?$result:$this->result;
		return mysql_num_rows($result);
	}
	public function fetchArray($result){
		return mysql_fetch_assoc($result);
	}
	public function fetchAssoc($result){
		return mysql_fetch_row($result);
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
	/**
	 * 简化select
	 * @param  string  				$table   表名
	 * @param  array|string   $cond    条件数组,或者条件字符串
	 * @param  string  				$index   返回结果数组的key索引字段，默认自动数字索引
	 * @param  string  				$orderby 排序方式
	 * @param  integer 				$page    页码如果该值设为-1则表示全部不分页
	 * @param  integer 				$psize   每页大小
	 * @return array					结果数组
	 */
 	public function select($table,$cond='',$index='',$orderby='',$page=1,$psize=20){
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
 			$sql='select * from '.$table.$where.' '.$vs['orderby'].$limit;
	 		$result=$this->query($sql);
	 		$vs['list'] = $this->fetchAll($result,$index);
	 		$vs['pcnt'] = count($vs['list']);
 		}
 		return $vs;
 	}

 /**
 * 封装的带缓存(使用默认的缓存)的db的select方法,只比select多一个ttl参数
 * @param  string  				$table   表名
 * @param  array|string   $cond    条件数组,或者条件字符串
 * @param  string  				$index   返回结果数组的key索引字段，默认自动数字索引
 * @param  string  				$orderby 排序方式
 * @param  integer 				$page    页码如果该值设为-1则表示全部不分页
 * @param  integer 				$psize   每页大小
 * @param  integer				$ttl	 	缓存时间（秒），	默认0不缓存，
 * @return array					结果数组
 */
	public function selectCache($table,$cond='',$index='',$orderby='',$page=1,$psize=20,$ttl=0){
		$data=false;
		$cachekey;
		if ($ttl>0) {
			$_cond = $this->buildWhere($cond);
			$cachekey = $_cond.'-'.$index.'-'.$orderby.'-'.$page.'-'.$psize;
			$cachekey = $table.'-'.md5($cachekey);

			$data = $GLOBALS['cache']->get($this->cache_group,$cachekey);
			if ($data) {
				return $data;
			}
		}
		$data = $this->select($table,$cond,$index,$orderby,$page,$psize);
		if ($ttl>0) {
			$GLOBALS['cache']->set($this->cache_group,$cachekey,$data,$ttl);
		}
		return $data;
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
			$query = $this->query($query);
		}
		if ($query) {
			// 数据量大时效率较低，不过基本是为了取聚合的结果的 就无所谓了
			return mysql_result($query, $row, $field);
		}else{
			return null;
		}
	}
	/**
	 * 简化insert
	 * @param  string 	$table 表名
	 * @param  string 	$arr   插入的数据
	 * @return int|boolean        成功返回插入生成的索引id,失败false
	 */
 	public function insert($table,$arr){
		$sql = 'INSERT INTO `'.$table.'`';  
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
 	/**
 	 * 简化的update
   * @param  string 				$table 表名
	 * @param  array 					$arr   数据数组，可以为key-value方式，也可以没有key直接字符串 如 ’name="test"‘|"name"=>"test"
 	 * @param  array|string   $cond  条件数组，或者条件字符串(不带where)
 	 * @return 更新结果
 	 */
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
		return mysql_error();
	}
	public function buildWhere($cond){
		$where="";
		if (isset($cond)) {
			if (is_array($cond) && !empty($cond)) {
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
    	}elseif (is_string($cond) && !empty($cond)) {
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
	public function transRollback(){
		$this->query('ROLLBACK');
	}
}