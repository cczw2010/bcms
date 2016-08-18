<?php
	include('wwwheader.php');
?>
<div class="content ccenter mt20 pt20">
	<div class="mt20">
		<img src="<?=$cover;?>" alt=""><br>
		<span><?=$nickname;?></span>
	</div>
	<div class="mt20"><a class="f14" href="<?php echo Uri::build('user','appbind'); ?>">关联已有账号</a></div>
	<div class="mt10"><a class="f14" href="<?php echo Uri::build('user','appnew'); ?>">完善信息建立新账号</a></div>
	<div class="mt10"><a class="f14" href="<?php echo Uri::build('user','appauto'); ?>">以后完善,立即登陆</a></div>
</div>
<?php
	include('wwwfooter.php');
?>