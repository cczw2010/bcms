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
		echo '<p>都说2C产品已是红海一片，2B的海是蓝的吗？Design for Enterprise 星际迷航“企业号的设计图 作者 | 范</p>';
		echo '<div class="readAll clears">';
		echo '<span>'.date("Y年m月d日", $v['createdate']).'</span>';
		echo '<a href="article_detail?id='.$v['id'].'">阅读全文</a>';
		echo '</div>';
		echo '</div>';
	}
	?>

	<div class="page">
		<ul class="clears">
			<?php echo $pageDiv; ?>
		</ul>
	</div>
	<!-- <div class="dynamicStateList">
		<h3>设计2B产品与2C产品的差异</h3>
		<p>都说2C产品已是红海一片，2B的海是蓝的吗？Design for Enterprise 星际迷航“企业号的设计图 作者 | 范</p>
		<div class="readAll clears">
			<span>2016年6月11日</span>
			<a href="">阅读全文</a>
		</div>
	</div> -->
	<!-- <div class="page">
		<ul class="clears">
			<li><a href="">首页</a></li>
			<li><a href="">&lt;</a></li>
			<li><a href="">1</a></li>
			<li><a href="">2</a></li>
			<li><a href="">3</a></li>
			<li><a href="">···</a></li>
			<li><a href="">9</a></li>
			<li><a href="">10</a></li>
			<li><a href="">&gt;</a></li>
			<li><a href="">尾页</a></li>
		</ul>
	</div> -->
</div>
<?php
	include('footer.php');
?>