<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="bcms 后台管理系统" />
		<meta name="email" content="71752352@qq.com" />
    <meta name="Author" content="awen" />
    <meta name="Version" content="1.0" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!-- 360极速浏览器 -->
    <meta name="renderer" content="webkit" />
		<title><?php echo $GLOBALS['config']['sitename'];?>后台管理系统 v1.0</title>
		<link type="image/x-icon" rel="shortcut icon" href="/static/dist/img/favicon.ico">
		<!-- basic styles-->
		<link rel="stylesheet" href="/datas/ace1.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="/datas/ace1.2/css/font-awesome.min.css">
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/datas/ace1.2/css/font-awesome-ie7.min.css" />
		<![endif]-->
		<!-- page specific plugin styles -->
		<!-- fonts -->
		<link rel="stylesheet" href="/datas/ace1.2/css/ace-fonts.css" />
		<!-- ace styles -->
		<link rel="stylesheet" href="/datas/ace1.2/css/ace.min.css" />
		<link rel="stylesheet" href="/datas/ace1.2/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="/datas/ace1.2/css/ace-skins.min.css" />
		<link rel="stylesheet" href="/datas/ace1.2/css/bootstrap-timepicker.css" />
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/datas/ace1.2/css/ace-ie.min.css" />
		<![endif]-->
		<!-- inline styles related to this page -->
		<!-- ace settings handler -->
		<script src="/datas/ace1.2/js/ace-extra.min.js"></script>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="/datas/ace1.2/js/html5shiv.js"></script>
		<script src="/datas/ace1.2/js/respond.min.js"></script>
		<![endif]-->
		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='/datas/ace1.2/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>
		<!-- <![endif]-->
		<!--[if IE]>
		<script type="text/javascript">
		 window.jQuery || document.write("<script src='/datas/ace1.2/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
	</head>
	<body>
		<div class="navbar navbar-default" id="navbar">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="#" class="navbar-brand">
						<small>
							<i class="icon-leaf"></i>
					 		<?php echo $GLOBALS['config']['sitename'];?>后台管理系统
						</small>
					</a><!-- /.brand -->
				</div><!-- /.navbar-header -->
				<?php if (isset($user)): ?>					
				<div class="navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="/datas/ace1.2/avatars/avatar2.png"/>
								<span class="user-info">
									<small>Welcome,</small>
									<?php echo $user['username'];?>
								</span>
								<i class="icon-caret-down"></i>
							</a>
							<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a class="ajaxbtn" href="<?php echo Uri::build('manage/user','repass');?>">
										<i class="icon-cog"></i>修改密码
									</a>
								</li>
								<li class="divider"></li>
								<li>
									<a href="<?php echo Uri::build('manage/user','logout'); ?>">
										<i class="icon-off"></i>退出
									</a>
								</li>
							</ul>
						</li>
					</ul><!-- /.ace-nav -->
				</div><!-- /.navbar-header -->
				<?php endif ?>
			</div><!-- /.container -->
		</div>