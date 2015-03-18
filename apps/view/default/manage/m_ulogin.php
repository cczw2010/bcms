<?php
	include('m_header.php');
?>
<div class="wraper">
	<div class="centerbox300 line30">
		<form action="" method="post">
			<h3>登陆</h3>
			<hr>
			<ul>
				<li>用户名*:</li>
				<li><input name="username" type="text" placeholder="请输入用户名"></li>
				<li>密 码*:</li>
				<li><input name="password" type="password" placeholder="请输入密码"></li>
				<li>验证码*:</li>
				<li>
					<input name="captcha" type="text" class="captchainput" placeholder="请输入验证码">
					<br><img onclick="flushCaptch(this)" class="captcha" data-osrc="<?php echo Uri::build('user','captcha'); ?>" src="<?php echo Uri::build('user','captcha'); ?>"/>
				</li>
				<li>
					<input type="submit" name="subbtn" class="submitbtn" value="登 陆">
				</li>
			</ul>
			<hr>
			<div class="clearfix">
			</div>
			<div class="mt10 ccenter red">
				<?php if(isset($error)){echo $error;} ?>
			</div>
		</form>
	</div>
</div>