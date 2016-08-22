<?php
	include('head.php');
?>
<!-- 其他页头部 -->
<div class="elseWrapper">
	<div class="elseBannerWrapper">
		<div class="elseIndexBanner clears">
			<div class="elseIndexLogo"><img src="../../static/web/image/else-logo.png" alt=""></div>
			<ul class="elseIndexNav">
				<li class="<?php echo ($type == 'index') ? 'active' : '' ; ?>"><a href="../home/index">首页</a></li>
				<li class="<?php echo ($type == 'about') ? 'active' : '' ; ?>"><a href="../home/about">关于我们</a></li>
				<li class="<?php echo ($type == 'detail') ? 'active' : '' ; ?>"><a href="../product/detail">产品详情</a></li>
				<li class="<?php echo ($type == 'didi') ? 'active' : '' ; ?>"><a href="../didi/design">滴滴动态</a></li>
			</ul>
		</div>
	</div>
</div>
