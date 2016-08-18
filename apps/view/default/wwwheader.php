<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta property="qc:admins" content="75350425521005266141236654" />
	<meta property="wb:webmaster" content="983133f5a8fbbe22" />
	<meta name="description" content="<?=empty($DESCRIPTION)?'默认description':$DESCRIPTION; ?>" />
	<meta name="keywords" content="<?=empty($KEYWORDS)?'默认keywords':$KEYWORDS; ?>" />
	<!-- 通用样式部分 -->
	<link rel="stylesheet" href="/static/dist/css/common.min.css" />
	<link rel="stylesheet" href="/static/dist/css/main.min.css" />
	<!-- 动态加载页面自己定义的样式文件 -->
	<?php
		if (!empty($STYLES)) {
			foreach ($STYLES as $_style) {
				echo '<link rel="stylesheet" href="'.$_style.'" />';
			}
		}
	?>
	<!-- 通用js部分 -->
	<script src='/static/dist/js/jquery-1.8.3.min.js' ></script>
	<script src='/static/dist/js/main.min.js'></script>
	<!-- 动态加载页面自己定义的js文件 -->
	<?php
		if (!empty($SCRIPTS)) {
			foreach ($SCRIPTS as $_script) {
				echo '<script src="'.$_script.'" type="text/javascript"></script>';
			}
		}
	?>
	<title><?=empty($TITLE)?'默认title':$TITLE; ?></title>
</head>
<body>