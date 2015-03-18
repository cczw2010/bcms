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
		1 全部缓存：包括模板缓存，系统数据缓存和用户自定义的数据缓存<br>
		1 模板缓存：使用文件缓存,如果配置中没有使用模板，那么自然没有模板缓存<br>
		2 系统数据缓存：使用文件缓存,只处理系统的数据缓存，目前自动缓存并变更后自动更新数据（如果你直接改的数据库，那么缓存是不会自动更新的）的模块有:<br>
			&nbsp;&nbsp;&nbsp;1.支付模块->支付配置信息
			&nbsp;&nbsp;&nbsp;2.第三方登陆模块->第三方登陆列表
			&nbsp;&nbsp;&nbsp;3.敏感词模块->敏感词
			&nbsp;&nbsp;&nbsp;4.邮件模块->smtp邮件配置
	</div>
</div>