<?php
/**
 * 辅助信息类
 */
class Sysinfo{
	// 获取系统信息
	static function getSystemInfo(){
		$ret = array(
				'OS_info'=> php_uname(),    			//获取系统类型及版本号
	      'OS_system'=> php_uname('s'),    	//只获取系统类型
	      'OS_version'=> php_uname('r'),   	//只获取系统版本号
	      'PHP_sapi'=> php_sapi_name(),			//获取PHP运行方式
	      'PHP_version'=> PHP_VERSION,			//获取PHP版本
	      'PHP_path'=> DEFAULT_INCLUDE_PATH,   //获取PHP安装路径
	      'Zend_version'=> Zend_Version(),	//获取Zend版本
			);
		return $ret;
	}

	// 获取站点信息
	static function getSiteInfo(){
		$ret = array(
				'Server_protocol'=>getenv('SERVER_PROTOCOL'),	//传输协议
				'Server_root' => getenv('DOCUMENT_ROOT'),			//服务器文档根目录
				'Server_port' => getenv('SERVER_PORT'),				//服务器端口
				'Server_host' => $_SERVER["HTTP_HOST"],				//(返回值为域名或IP)获取Http请求中Host值
				'Server_ip' => GetHostByName($_SERVER['SERVER_NAME']),				//获取服务器IP
				'Server_engine' => $_SERVER['SERVER_SOFTWARE'],								//获取服务器IP
				'Server_lan' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],					//获取服务器IP
				'Client_ua' => getenv('HTTP_USER_AGENT'),			//用户UA
				'Client_ip' => $_SERVER['REMOTE_ADDR'],				//获取客户端IP
			);
		return $ret;
	}
}
?>