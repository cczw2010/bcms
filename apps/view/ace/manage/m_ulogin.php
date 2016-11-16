<?php
	include('m_header.php');
?>
	<div class="row">
		<div class="space-30"></div>
		<div class="col-sm-10 col-sm-offset-1">
			<?php if (!empty($error)): ?>
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">
					<i class="icon-remove"></i>
				</button>
				<strong>
					<i class="icon-bell"></i>
					 <?php echo $error; ?>
				</strong>
				<br>
			</div>
			<?php endif ?>
			<div class="login-container">
				<div class="position-relative">
					<div id="login-box" class="login-box widget-box no-border visible">
						<div class="widget-body">
							<div class="widget-main">
								<h4 class="header blue lighter bigger">
									<i class="icon-coffee green"></i>登陆
								</h4>
								<div class="space-6"></div>
									<form action="" method="post">
									<fieldset>
										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input name="username" type="text" class="form-control" placeholder="Username">
												<i class="icon-user"></i>
											</span>
										</label>
										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<input name="password" type="password" class="form-control" placeholder="Password">
												<i class="icon-lock"></i>
											</span>
										</label>
										<label class="block clearfix">
											<span class="block input-icon input-icon-right">
												<img class="captcha" src="<?php echo Uri::build('widget','captcha'); ?>"/>
												<input class="inline" name="captcha" type="text" class="form-control" placeholder="">
											</span>
										</label>
										<div class="space"></div>
										<div class="clearfix">
											<button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
												<i class="icon-key"></i>
												Login
											</button>
										</div>
										<div class="space-4"></div>
									</fieldset>
								</form>
							</div><!-- /widget-main -->
						</div><!-- /widget-body -->
					</div><!-- /login-box -->
				</div><!-- /position-relative -->
			</div>
		</div><!-- /.col -->
	</div>
	<script>
  if (location.pathname!="/manage/manager/login") {
  	location.href = "/manage/manager/login";
  }
	</script>
<?php
	include('m_footer.php');
?>