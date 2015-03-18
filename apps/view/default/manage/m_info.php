<div class="tabbox dbmain">
	<ul class="boxs">
		<li class="tablabel active">服务器信息</li>
		<li class="tablabel">数据库信息</li>
		<li class="tablabel">数据表信息</li>
		<li class="tablabel">数据备份</li>
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
		<div class="tabbody">
			<div class="info"><span class="xicon mr10">R</span> tips:首先数据库的配置不能是空密码哦，另外导出的sql文件里可能会有一条警告信息，去掉就可以正常使用了</div>
			<form action="<?php echo Uri::build('manage/setting','dbback') ;?>">
			<table class="tablebox" width="400" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th colspan="2">数据库备份</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="120">mysqldump路径：</td>
						<td>
							<div>（linux基本都能直接调用，windows一般没有配到path里就不能直接调用，需要完整路径。值得注意的是路径中别有空格）</div>
							<input type="text" name="dumppath" value="<?php echo isset($dumppath)?$dumppath:'mysqldump'; ?>">
						</td>
					</tr>
					<tr>
						<td>备份路径：</td>
						<td>
							<div>（不建议修改，默认站点根目录backup目录，请注意目标文件夹的读写权限）</div>
							<input type="text" name="savepath" value="<?php echo isset($savepath)?$savepath:'backup'; ?>">
						</td>
					</tr>
					<tr>
						<td>备份内容：</td>
						<td>
							<select name="savetype" id="savetype">
								<option value="all">数据库结构和数据</option>
								<option value="structure">数据库结构</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="ccenter" colspan="2">
							<input type="button"  name="submitbtn" class="submitbtn" value="备 份">
						</td>
					</tr>
				</tbody>
			</table>
			</form>		
		</div>
	</div>
</div>
<script>
	buildTab('.dbmain');
</script>