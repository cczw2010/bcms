<?php
	include('wwwheader.php');
?>
<div class="wraper">
	<div class="userbox clearfix">
		<form action="" method="post">
			<h3>登陆</h3>
			<hr>
			<div class="userli">
				<label>用户名*:</label>
				<input name="username" type="text" placeholder="请输入用户名">
			</div>
			<div class="userli">
				<label>密 码*:</label>
				<input name="password" type="password" placeholder="请输入密码">
			</div>
			<div class="userli">
				<label>验证码*:</label>
				<input name="captcha" type="text" class="captchainput" placeholder="请输入验证码">
				<img onclick="flushCaptch(this)" class="captcha" data-osrc="<?php echo Uri::build('widget','captcha'); ?>" src="<?php echo Uri::build('widget','captcha'); ?>"/>
			</div>
			<hr>
			<div class="clearfix">
				<span class="thirdapp"><?=$thirdapp;?></span>
				<input type="submit" name="subbtn" class="submitbtn" value="登 陆">
			</div>
			<div class="mt10 ccenter red">
				<?php if(isset($error)){echo $error;} ?>
			</div>
		</form>
	</div>
</div>
	
<?php
	include('wwwfooter.php');
?>