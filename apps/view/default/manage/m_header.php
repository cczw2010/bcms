<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8" />
		<title>BCMS后台管理系统 v1.0</title>
		<meta name="description" content="bcms 后台管理系统" />
		<meta name="email" content="71752352@qq.com" />
    <meta name="Author" content="awen" />
    <meta name="Version" content="1.0" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!-- 360极速浏览器 -->
    <meta name="renderer" content="webkit" />
		<link type="image/x-icon" rel="shortcut icon" href="/static/dist/img/favicon.ico">
		<!-- basic styles-->
		<link rel="stylesheet" href="/static/dist/css/common.min.css" />
		<link rel="stylesheet" href="/static/dist/css/manager.min.css?t=<?php echo time(); ?>" />
		<link rel="stylesheet" href="/static/dist/css/normalize.min.css" />
		<link rel="stylesheet" href="/datas/uploadify/uploadify.css">
		<link rel="stylesheet" href="/datas/datepicker/css/default.css">
		<link rel="stylesheet" href="/datas/zebra_dialog/css/flat/zebra_dialog.css">
		<!-- basic scripts -->
		<script src='/static/dist/js/jquery-1.8.3.min.js'></script>
		<script src="/static/dist/js/main.min.js"></script>
		<script src="/datas/tinymce/tinymce.min.js"></script>
		<script src="/datas/uploadify/jquery.uploadify.min.js"></script>
		<script src="/datas/datepicker/javascript/zebra_datepicker.min.js"></script>
		<script src="/datas/zebra_dialog/javascript/zebra_dialog.js"></script>
	</head>
	<body>
		<!-- 顶部 -->
		<div class="topbar posrel clearfix p10">
			<div class="shbox-l f20 ">
				 BCMS后台管理系统
			</div>
			<div class="shbox-flex cright">
				<?php 
				if (isset($user)) {
					echo '欢迎您！'.$user['username'].' |
					 <span class="ajaxbtn dialogbtn"  data-url="'.Uri::build('manage/user','repass').'">修改密码</span> | <a href="'.Uri::build('manage/user','logout').'" title="退出">退出</a>';
				}else{
					echo '未登录！';
				}
				?>
			</div>
		</div>