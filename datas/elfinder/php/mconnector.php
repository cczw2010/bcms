<?php
session_start();
date_default_timezone_set('Asia/ShangHai') ;
error_reporting(0); // Set E_ALL for debuging

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0      // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

/**
 * modify by awen
 * 将配置文件抽离到bcms框架中的配置文件中,并且根据登陆用户id定位路径(能到这的肯定登陆了)
 */
$sconfig = require('../../../config.php');
$spath = $sconfig['elfinder']['path'];
$surl = $sconfig['elfinder']['url'];

require('../../../module/module_user.class.php');
$suser = Module_User::getLoginUser(true);
$ufolder = Module_User::getAlbumBase($suser['id']);
$uppath = $spath.'/'.$ufolder.'/';
if (is_dir($spath) && !file_exists($uppath)) {
	// 创建每个用户自己的图片目录
	mkdir($uppath);
	chmod($uppath, 0755);
}
$upurl = $surl.'/'.$ufolder.'/';
$opts =  array(
		'debug' => $sconfig['elfinder']['debug'],
		'uploadRename'=>$sconfig['elfinder']['uploadRename'],	//是否重命名（为awen扩展的配置）
		'roots' => array(
			array(
				'driver'        => 'LocalFileSystem',   // 文件驱动 (REQUIRED) 不需要改变
				'path'          => $uppath, 		// 文件浏览器path (REQUIRED)
				'URL'           => $upurl, 			// 文件访问路径 (REQUIRED)
				'accessControl' => 'access'             // 隐藏以.开头的文件或者文件夹
			)
		)
	);
// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

