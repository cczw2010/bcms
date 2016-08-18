<?php
	include('wwwheader.php');
?>
<div class="wraper">
	<div class="userbox clearfix">
		<form action="" method="post">
			<h3>注册</h3>
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
				<label>邮 箱*:</label>
				<input name="email" type="text" placeholder="请输入邮箱">
			</div>
			<hr>
			<div class="clearfix">
				<input type="submit" name="subbtn" class="submitbtn" value="提 交">
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