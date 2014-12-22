<?php
//by awen
date_default_timezone_set('Asia/Shanghai') ;
//定义应用的绝对路径
define('BASEPATH', dirname(__FILE__));
// system核心目录
define('SYSDIR', BASEPATH.DIRECTORY_SEPARATOR.'system');

// 加载通用基础函数
require(SYSDIR.DIRECTORY_SEPARATOR.'common.php');
// 程序入口
Core_App::load();
