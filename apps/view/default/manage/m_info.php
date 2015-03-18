<div class="tabbox dbmain">
	<ul class="boxs">
		<li class="tablabel active">服务器信息</li>
		<li class="tablabel">数据库信息</li>
		<li class="tablabel">数据表信息</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<ul class="group_list">
				<li><span class="shbox-l">服务器内核</span><span class="shbox-flex"><?=$sinfo['OS_system'];?></span></li>
				<li><span class="shbox-l">服务器版本</span><span class="shbox-flex"><?=$sinfo['OS_version'];?></span></li>
				<li><span class="shbox-l">php模式</span><span class="shbox-flex"><?=$sinfo['PHP_sapi'];?></span></li>
				<li><span class="shbox-l">php安装路径</span><span class="shbox-flex"><?=$sinfo['PHP_path'];?></span></li>
				<li><span class="shbox-l">php版本</span><span class="shbox-flex"><?=$sinfo['PHP_version'];?></span></li>
				<li><span class="shbox-l">Zend版本</span><span class="shbox-flex"><?=$sinfo['Zend_version'];?></span></li>
				<li><span class="shbox-l">服务器类型</span><span class="shbox-flex"><?=$winfo['Server_engine'];?></span></li>
				<li><span class="shbox-l">站点路径</span><span class="shbox-flex"><?=$winfo['Server_root'];?></span></li>
				<li><span class="shbox-l">www端口</span><span class="shbox-flex"><?=$winfo['Server_port'];?></span></li>
				<li><span class="shbox-l">服务器语言</span><span class="shbox-flex"><?=$winfo['Server_lan'];?></span></li>
				<li><span class="shbox-l">客户端平台</span><span class="shbox-flex"><?=$uainfo->platform;?></span></li>
				<li><span class="shbox-l">浏览器类型</span><span class="shbox-flex"><?=$uainfo->browser;?></span></li>
				<li><span class="shbox-l">浏览器版本</span><span class="shbox-flex"><?=$uainfo->version;?></span></li>
				<li><span class="shbox-l">useragent</span><span class="shbox-flex"><?=$uainfo->agent;?></span></li>
			</ul>
		</div>
		<div class="tabbody">
			<ul class="group_list">
				<li><span class="shbox-l">最大连接数</span><span class="shbox-flex"><?=$dbinfo['db']['max_connections']['Value'];?></span></li>
				<li><span class="shbox-l">安装路径</span><span class="shbox-flex"><?=$dbinfo['db']['basedir']['Value'];?></span></li>
				<li><span class="shbox-l">当前版本</span><span class="shbox-flex"><?=$dbinfo['db']['version']['Value'];?></span></li>
				<li><span class="shbox-l">数据库字符集</span><span class="shbox-flex"><?=$dbinfo['db']['character_set_database']['Value'];?></span></li>
				<li><span class="shbox-l">连接超时</span><span class="shbox-flex"><?=$dbinfo['db']['connect_timeout']['Value'];?></span></li>
			</ul>
		</div>
		<div class="tabbody">
			<ul class="group_list">
				<?php
				foreach ($dbinfo['tables'] as $tk => $tv) {
					echo '<li><span class="shbox-l">'.$tk.'</span>
					<span class="shbox-flex">记录数：'.$tv['Rows'].' ; 数据量:'.($tv['Data_length']/1024).'k</span></li>';
				}
				?>
			</ul>
		</div>
	</div>
</div>
<script>
	buildTab('.dbmain');
</script>