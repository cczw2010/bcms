<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active">
			<a data-toggle="tab" href="#serverinfo">服务器信息</a>
		</li>
		<li class="">
			<a data-toggle="tab" href="#dbinfo">数据库信息</a>
		</li>
		<li class="">
			<a data-toggle="tab" href="#tableinfo">数据表信息</a>
		</li>
	</ul>
	<div class="tab-content">
		<div id="serverinfo" class="tab-pane active">
			<div class="widget-box transparent">
				<div class="widget-body">
					<div class="widget-main no-padding">
						<table class="table table-bordered table-striped">
							<tbody>
								<tr><td>服务器内核</td><td><?=$sinfo['OS_system'];?></td></tr>
								<tr><td>服务器版本</td><td><?=$sinfo['OS_version'];?></td></tr>
								<tr><td>php模式</td><td><?=$sinfo['PHP_sapi'];?></td></tr>
								<tr><td>php安装路径</td><td><?=$sinfo['PHP_path'];?></td></tr>
								<tr><td>php版本</td><td><?=$sinfo['PHP_version'];?></td></tr>
								<tr><td>Zend版本</td><td><?=$sinfo['Zend_version'];?></td></tr>
								<tr><td>服务器类型</td><td><?=$winfo['Server_engine'];?></td></tr>
								<tr><td>站点路径</td><td><?=$winfo['Server_root'];?></td></tr>
								<tr><td>www端口</td><td><?=$winfo['Server_port'];?></td></tr>
								<tr><td>服务器语言</td><td><?=$winfo['Server_lan'];?></td></tr>
								<tr><td>客户端平台</td><td><?=$uainfo->platform;?></td></tr>
								<tr><td>浏览器类型</td><td><?=$uainfo->browser;?></td></tr>
								<tr><td>浏览器版本</td><td><?=$uainfo->version;?></td></tr>
								<tr><td>useragent</td><td><?=$uainfo->agent;?></td></tr>
							</tbody>
						</table>
					</div><!-- /widget-main -->
				</div><!-- /widget-body -->
			</div>
		</div>
		<div id="dbinfo" class="tab-pane">
			<div class="widget-box transparent">
				<div class="widget-body">
					<div class="widget-main no-padding">
						<table class="table table-bordered table-striped">
							<tbody>
								<tr><td>最大连接数</td><td><?=$dbinfo['db']['max_connections']['Value'];?></td></tr>
								<tr><td>安装路径</td><td><?=$dbinfo['db']['basedir']['Value'];?></td></tr>
								<tr><td>当前版本</td><td><?=$dbinfo['db']['version']['Value'];?></td></tr>
								<tr><td>数据库字符集</td><td><?=$dbinfo['db']['character_set_database']['Value'];?></td></tr>
								<tr><td>连接超时</td><td><?=$dbinfo['db']['connect_timeout']['Value'];?></td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div id="tableinfo" class="tab-pane">
			<div class="widget-box transparent">
				<div class="widget-body">
					<div class="widget-main no-padding">
						<table class="table table-bordered table-striped">
							<tbody>
							<?php
							foreach ($dbinfo['tables'] as $tk => $tv) {
								echo '<tr><td>'.$tk.'</td>
								<td>记录数：'.$tv['Rows'].' ; 数据量:'.($tv['Data_length']/1024).'k</td></tr>';
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>