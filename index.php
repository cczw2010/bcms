<?php
//by awen
date_default_timezone_set('Asia/Shanghai') ;
//定义应用的绝对路径
define('BASEPATH', dirname(__FILE__));
// system核心目录
define('SYSDIR', BASEPATH.DIRECTORY_SEPARATOR.'system');

// 设置内部字符编码
mb_internal_encoding('UTF-8');
// 打开短标签支持
ini_set('short_open_tag','1');
// 启动session
if (!isset($_SESSION)) {
	session_start();
}
// 加载通用基础函数
require(SYSDIR.DIRECTORY_SEPARATOR.'common.php');
// 程序入口
Core_App::load();
