<?php
	include('wwwheader.php');
?>
<style>
	.messagebox{
		margin: 30px auto 20px;
		padding: 10px;
		width: 300px;
		border: 1px solid #ccc;
		border-radius: 5px;
	}
</style>
<div class="wrap">
	<div class="messagebox">
		<h3><?=$msg;?></h3>
		<hr class="mt10 mb10">
		<div class="ccenter">
			<a href="javascript:void(0);" onclick="history.back();">返回上一页</a> | 
			<a href="/">去首页</a>
			<?php if (!empty($url)): ?>
			 | <a href="<?=$url;?>">立即跳转</a>
			<?php endif ?>
		</div>
		<div class="cright <?=$timeout==-1?'hidden':'';?>"><span id="timerttl" class="red"><?=$timeout;?></span>秒后跳转</div>
	</div>
</div>
	<script>
	var _ttl = <?=$timeout;?>,
		url = "<?=$url;?>";
	function msgtimer(){
		document.getElementById('timerttl').innerHTML=--_ttl;
		if (_ttl<=0) {
			if (url) {
				location.href=url;
			}else{
				history.back();
			}
		}
		setTimeout(msgtimer,1000);
	}
	if (_ttl>-1) {
		setTimeout(msgtimer,1000);
	};
	</script>
<?php
	include('wwwfooter.php');
?>