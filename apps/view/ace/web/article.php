<?php
	include('header.php');
?>
<div class="dynamicState">
	<ul class="stateNav clears">
		<li class="<?php echo ($type_two == 'design') ? 'active' : '' ; ?>"><a href="design">滴滴设计告白</a></li>
		<li class="<?php echo ($type_two == 'activity') ? 'active' : '' ; ?>"><a href="activity">活动消息</a></li>
		<li class="<?php echo ($type_two == 'report') ? 'active' : '' ; ?>"><a href="report">媒体报道</a></li>
	</ul>
	<div class="detail">
		<div class="detailTit">
			<h2><?php echo $data['title']?></h2>
			<p><?php echo date("Y年m月d日", $data['createdate']); ?></p>
		</div>
		<div class="detailMsg">
			<?php echo $data['content']?>
		</div>
	</div>
</div>
<?php
	include('footer.php');
?>