<?php
//-----------------------------url 解析
Class Uri{
	const key_p = "_p";				//controller的二级目录
	const key_c = "_c";				//controller的key
	const key_m = "_m";				//method的key
	public static $uritype;
	private static $def_c;
	private static $def_m;
	private $params;
	private static $instance = null;
	private function __construct(){
		$uritype = $GLOBALS['config']['uritype'];

		self::$uritype = empty($uritype)?'/':$uritype;
		self::$def_c = $GLOBALS['config']['def_c'];
		self::$def_m = $GLOBALS['config']['def_m'];
		$this->params = $this->resolve();
	}
	//将url解析并将返回结果并,不包括解析?后面 除了controller和method以外的参数,
	private function resolve(){
		$ret = array('params'=>array());
		$ret[self::key_c] = self::$def_c;
		$ret[self::key_m] = self::$def_m;
		// 友好url的链接第一个GET参数取出并干掉，因为是是rewrite后的uri
		$uri = array_shift($_GET);
		if($pos = strpos($uri,'?')){
			$uri = substr($uri,0,$pos);
		}
		$uri = trim($uri,'/');
		$uparams = empty($uri)?array():explode(self::$uritype,$uri);
		$uricount = count($uparams);
		$idx= 0;
		if ($uricount>$idx) {
			// 增加二级control目录判断
			$_f = $GLOBALS['path_app'].DIRECTORY_SEPARATOR.$GLOBALS['config']['folder_c'].DIRECTORY_SEPARATOR.$uparams[$idx];
			if (is_dir($_f)) {
				$ret[self::key_p] = $uparams[$idx];
				$idx++;
			}else{
				$ret[self::key_p] = '.';
			}
			// unset($_GET['_rewurl']);
		}
		if ($uricount>$idx) {
			$ret[self::key_c] = $uparams[$idx];
			$idx++;
		}
		if ($uricount>$idx) {
			$ret[self::key_m] = $uparams[$idx];
			$idx++;
		}
		// params
		$ret['params'] = array_splice($uparams, $idx);
		// 解析$_GET		
		if (isset($_GET[self::key_c])) {
			$ret[self::key_c] = $_GET[self::key_c];
		}
		if (isset($_GET[self::key_m])) {
			$ret[self::key_m] = $_GET[self::key_m];
		}
		return $ret;
	}
	// 初始化，单例模式
	static function init(){
		if (self::$instance==null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	// 获取解析后的参数
	static function getParams(){
		return self::$instance->params;
	}
	/**
	 * 根据当前url类型，构建reaet url(或者参数query)
	 * @param  string $c 			controller,如果$c为空那么就不是完整url,只是拼接参数
	 * @param  string $m     	页面对应的方法
	 * @param  array $params  参数值数组
	 * @param  boolean $redirect 	是否直接跳转
	 * @return string|null        友好url或直接跳转
	 */
	static function build($c,$m='index',$params=array(),$redirect=false){
		$urls = array();
		// 如果$c为空那么就不是完整url,只是拼接参数
		if (!empty($c)) {
			$urls[] = $c;
			$urls[] = $m;
		}
		if (is_array($params) && count($params)>0) {
			$urls = array_merge($urls,$params);
		}
		$url = count($urls)>0?('/'.implode(self::$uritype, $urls)):'';
		if ($redirect && !empty($url)) {
			Uri::redirect($url);
		}
		return $url;
	}

	/**
	 * url跳转,注意前面不能有任何输出，高发错误就是有空格等输出
	 * @param	$uri string	要跳转的url,默认是当前地址刷新
	 * @param $method	boolean	location|redirect
	 * @param	$http_response_code int	状态码 默认302跳转
	 * @return	null
	 */
	static function redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		$uri=strlen($uri)>0?$uri:$this->buildUrl();
		switch($method)
		{
			case 'refresh': header("Refresh:0;url=".$uri);
				break;
			default: header("Location: ".$uri, TRUE, $http_response_code);
				break;
		}
		exit;
	}
	/**
	 * 手动设置前一个页面,默认当前页
	 */
	static function setPrevPage($url=''){
		$url = empty($url)?$_SERVER['REQUEST_URI']:$url;
		Helper::setSession('x_prevPage',$url);
	}
	/**
	 * 返回前一个页面,顺序是session中设定的->$_SERVER['HTTP_REFERER']->根目录
	 */
	static function getPrevPage(){
		$url = Helper::getSession('x_prevPage',true);
		if (empty($url)) {
			$url = empty($_SERVER['HTTP_REFERER'])?'/':$_SERVER['HTTP_REFERER'];
		}
		return $url;
	}

	/**
	 * 安全GET 去除html,xml标签，并使用反斜线引用字符串
	 * @param  mixed $key     GET中的键值
	 * @param  mixed $default 不存在或者为空时的默认值
	 * @return mixed 
	 */
	static function get($key,$default=false){
		if (isset($_GET[$key])) {
			if (is_array($_GET[$key])) {
				$ret = $_GET[$key];
			}else{
				$ret = trim($_GET[$key]);
				$ret = filter_var($ret, FILTER_SANITIZE_STRING);
				$ret= strlen($ret)==0?$default:$ret;
			}
			// $ret = addslashes(strip_tags($ret));
		}else{
			$ret = null;
		}
		$ret= is_null($ret)?$default:$ret;
		return $ret;
	}

	/**
	 * 安全POST 去除html,xml标签，并使用反斜线引用字符串，待完善
	 * @param  mixed $key     POST中的键值
	 * @param  mixed $default 不存在或者为空时的默认值
	 * @return mixed 
	 */
	static function post($key,$default=false){
		if (isset($_POST[$key])) {
			if (is_array($_POST[$key])) {
				$ret = $_POST[$key];
			}else{
				$ret = trim($_POST[$key]);
				$ret = filter_var($ret, FILTER_SANITIZE_STRING);
				$ret= strlen($ret)==0?$default:$ret;
			}
		}else{
			$ret = null;
		}
		$ret= is_null($ret)?$default:$ret;
		return $ret;
	}
	/**
	 * 安全request 去除html,xml标签，并使用反斜线引用字符串，待完善
	 * @param  mixed $key     POST中的键值
	 * @param  mixed $default 不存在或者为空时的默认值
	 * @return mixed 
	 */
	static function request($key,$default=false){
		if (isset($_REQUEST[$key])) {
			if (is_array($_REQUEST[$key])) {
				$ret = $_REQUEST[$key];
			}else{
				$ret = trim($_REQUEST[$key]);
				$ret = filter_var($ret, FILTER_SANITIZE_STRING);
				$ret= strlen($ret)==0?$default:$ret;
			}
		}else{
			$ret = null;
		}
		$ret= is_null($ret)?$default:$ret;
		return $ret;
	}

	// 文件路径转网站路径,注意只转换站内文件
	static function path2url($path){
		$url = false;
		if (file_exists($path)) {
			$url = realpath($path);
			$url = str_replace(BASEPATH, '', $url);
		}
		return $url;
	}
}