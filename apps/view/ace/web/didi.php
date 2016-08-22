<?php
	include('header.php');
?>
<div class="dynamicState">
	<ul class="stateNav clears">
		<li class="<?php echo ($type_two == 'design') ? 'active' : '' ; ?>"><a href="design">滴滴设计告白</a></li>
		<li class="<?php echo ($type_two == 'activity') ? 'active' : '' ; ?>"><a href="activity">活动消息</a></li>
		<li class="<?php echo ($type_two == 'report') ? 'active' : '' ; ?>"><a href="report">媒体报道</a></li>
	</ul>
	<?php
	foreach ($list as $k => $v) {
		echo '<div class="dynamicStateList">';
		echo '<h3>'.$v['title'].'</h3>';
		echo '<p>'.$v['summary'].'</p>';
		echo '<div class="readAll clears">';
		echo '<span>'.date("Y年m月d日", $v['createdate']).'</span>';
		echo '<a href="article_detail?type_two='.$type_two.'&id='.$v['id'].'">阅读全文</a>';
		echo '</div>';
		echo '</div>';
	}
	?>
	<div class="page">
		<ul class="clears">
			<?php echo $pageDiv; ?>
		</ul>
	</div>
</div>
<?php
	include('footer.php');
?>
