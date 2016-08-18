<div class="tabbox">
	<?php
	if (!empty($msg)) {
		echo	'<div class="info"><span class="xicon mr10">W</span>'.$msg.'</div>';
	}
	?>
	<div class="mt20">
		<span data-url="/manage/setting/cache?op=<?php echo $GLOBALS['config']['view']['group'];?>" class="xicon ajaxbtn cacherbox">清空模板缓存</span>
		<span data-url="/manage/setting/cache?op=<?php echo $GLOBALS['config']['db']['group'];?>" class="xicon ajaxbtn cacherbox">清空数据缓存</span>
		<span data-url="/manage/setting/cache?op=all" class="xicon ajaxbtn cacherbox">清空所有缓存</span>
	</div>
	<hr class="mt20">
	<div class="info"><span class="xicon mr10">R</span> tips: <br>
		1 模板缓存：使用文件缓存,如果配置中没有使用模板引擎，那么自然没有模板缓存<br>
		2 数据缓存：使用默认缓存,只处理系统配置中的数据缓存组中的缓存，（db类的getdata方法也在其中）。有些系统配置是自动更新缓存的不需要处理，具体如下:<br>
			&nbsp;&nbsp;&nbsp;1.支付模块->支付配置信息
			&nbsp;&nbsp;&nbsp;2.第三方登陆模块->第三方登陆列表
			&nbsp;&nbsp;&nbsp;3.敏感词模块->敏感词
			&nbsp;&nbsp;&nbsp;4.邮件模块->smtp邮件配置
	</div>
</div>