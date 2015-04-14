<div class="tabbox dbmain">
	<ul class="boxs">
		<li class="tablabel active">数据库备份</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
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