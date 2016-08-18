<?php
if (!empty($msg)) {
	echo	'<div class="alert alert-info"><i class="icon-alert"></i>'.$msg.'</div>';
}
?>
<div class="row">
	<div class="space-10"></div>
	<a class="ajaxbtn btn btn-info btn-lg" href="/manage/setting/cache?op=<?php echo $GLOBALS['config']['view']['group'];?>">清空模板缓存</a>
	<a class="ajaxbtn btn btn-info btn-lg" href="/manage/setting/cache?op=<?php echo $GLOBALS['config']['db']['group'];?>">清空数据缓存</a>
	<a class="ajaxbtn btn btn-info btn-lg" href="/manage/setting/cache?op=all" >清空所有缓存</a>
</div>
<div class="space-10"></div>
<blockquote>
	<ol>
		<li>模板缓存：使用文件缓存,如果配置中没有使用模板引擎，那么自然没有模板缓存</li>
		<li>数据缓存：使用默认缓存,只处理系统配置中的数据缓存组中的缓存</li>
	</ol>
</blockquote>
