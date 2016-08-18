<?php
/**
 * 辅助信息类
 */
class Helper{
	// 获取客户端ip
	static function getClientIp(){
		if(getenv('HTTP_CLIENT_IP')) { 
			$oip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
			$oip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR')) { 
			$oip = getenv('REMOTE_ADDR');
		} else { 
			$oip = $_SERVER['REMOTE_ADDR'];
		}
		return $oip;
	}
	//根据类名生成实例，主要是解析配置文件的时候用
	static function refClass($classname,$args=array()){
		$classname = ucfirst($classname);//首字母大写
		$refl = new ReflectionClass($classname);
		if (!empty($args)) {
			return $refl->newInstance($args);
		}else{
			return $refl->newInstance();
		}
	}
	/**
	 * 随机加密
	 * @param  string $txt 要加密的英文字符串,你要是想死你就加密中文
	 * @param  string $key 自定义混淆字符串前缀,必须与decodeString中的$key保持一致.强烈建议不要用默认的防止被破解。当然如果你自己都不想解密，那么就设成每次随机吧。
	 * @return string      加密后的字符串
	 */
	static function encodeString($txt,$key='') {
		$key = empty($key)?'zw_prefix':$key;
		$klen = strlen($key);
		// 混淆字符串 原串，下面是打散后的
		// $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`[],./;~!@#$%^&*()_+-={}|:?"><\\';
		$chars = 'u*n}t;`~g%XDHC71wPSZEjy?JW([^<c\\=e2K:>Y9QGdlh#I),Urzb"A@MvFTm.3!5{6]-&q8RkNBV0L/$Oaspix_o4+|f';
		$chars = substr($chars, $klen).$key.substr($chars, 0,$klen);
		// 开始混淆
		$tmp = '';
		$txt = $txt;
		for ($i=0,$len =strlen($txt); $i<$len; $i++) {
			$ch = $txt[$i];
			$ch = strpos($chars, $ch)+$i%$klen;
			$tmp .= chr($ch);
		}
		$tmp = base64_encode($tmp);
		return str_replace('=', '', $tmp);
	}

	/**
	 * 解密
	 * @param  string $txt encodeRandString加过密的字符串
	 * @param  string $key 自定义混淆字符串前缀,必须与encodeString中的$key保持一致.强烈建议不要用默认的防止被破解。
	 * @return string      解密后的字符串
	 */
	static function decodeString($txt,$key='') {
		$key = empty($key)?'zw_prefix':$key;
		$klen = strlen($key);
		// 混淆字符串
		// $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`[],./;~!@#$%^&*()_+-={}|:?"><\\';
		$chars = 'u*n}t;`~g%XDHC71wPSZEjy?JW([^<c\\=e2K:>Y9QGdlh#I),Urzb"A@MvFTm.3!5{6]-&q8RkNBV0L/$Oaspix_o4+|f';
		$chars = substr($chars, $klen).$key.substr($chars, 0,$klen);
		// 开始解密
		$txt = base64_decode($txt);
		$tmp = '';
		for ($i=0,$len =strlen($txt); $i<$len; $i++) {
			$ch = ord($txt[$i])-$i%$klen;
			$tmp .= $chars[$ch];
		}
		return $tmp;
	}
	/**
	 * 生成唯一标志
	 * @return string
	 */
	static function getUniqid(){
		return md5(uniqid('awen-smss',false));
	}

	// 设置session信息
	static function setSession($key,$val){
		$_SESSION[$key] = $val;
	}
	// 获取session信息
	// $desctory  是否同时销毁，默认false
	static function getSession($key,$destory=false){
		$val = isset($_SESSION[$key])?$_SESSION[$key]:'';
		if ($destory) {
			unset($_SESSION[$key]);
		}
		return $val;
	}
	//设置session过期时间
	//$ttl 过期时间（秒）  $path 路径
	static public function setSessionTTL($ttl = 3600, $path = '/') {
      setCookie(session_name(), session_id(), time() + $ttl, $path);
  }
}