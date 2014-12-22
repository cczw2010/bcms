<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8" />
		<title>SSMS后台管理系统 v1.0</title>
		<meta name="description" content="ssms 后台管理系统" />
		<meta name="email" content="71752352@qq.com" />
    <meta name="Author" content="awen" />
    <meta name="Version" content="1.0" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link type="image/x-icon" rel="shortcut icon" href="/static/dist/img/favicon.ico">
		<!-- basic styles-->
		<link rel="stylesheet" href="/static/dist/css/common.min.css" />
		<link rel="stylesheet" href="/static/dist/css/manager.min.css" />
		<link rel="stylesheet" href="/static/dist/css/normalize.min.css" />
		<link rel="stylesheet" href="/datas/uploadify/uploadify.css">
		<link rel="stylesheet" href="/datas/datepicker/css/default.css">
		<!-- basic scripts -->
		<script src='/static/dist/js/jquery-1.8.3.min.js'></script>
		<script src="/static/dist/js/main.min.js"></script>
		<script src="/datas/tinymce/tinymce.min.js"></script>
		<script src="/datas/uploadify/jquery.uploadify.min.js"></script>
		<script src="/datas/datepicker/javascript/zebra_datepicker.min.js"></script>
	</head>
	<body class="boxs boxorientv">
		<!-- 顶部 -->
		<div class="topbar boxs">
			<div class="flex6 f20 pl10">
				 后台管理系统(html5版本)
			</div>
			<div class="flex"><?php  echo date('Y-m-d H:i');?></div>
			<div class="flex">
				<?php 
				if ($user) {
					echo '欢迎您！'.$user['username'].' |
					 <span class="ajaxbtn" data-url="'.Uri::build('manage','pueditinfo',array($user['id'])).'">修改个人信息</span> | 
					 <span class="ajaxbtn" data-url="'.Uri::build('manage','purepass').'">修改密码</span> | <a href="'.Uri::build('user','logout').'" title="退出">退出</a>';
				}else{
					echo '未登录！';
				}
				?>
			</div>
		</div>