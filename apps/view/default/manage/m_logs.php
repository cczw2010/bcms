<div class="tabbox groupmain">
	<div class="info"><span class="xicon mr10">R</span> tips:该处记录了一些后端进行的修改数据库的操作日志</div>
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active">log日志列表</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<div class="filter">
				<form action="/manage/setting/logs/">
				<span class="xicon mr10">!</span>筛选：
				<label class="ml20"  for="">用户名：</label>
				<input type="text" name="username" size="10" value="<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'' ?>">
				<label class="ml20"  for="">模块：</label>
				<input type="text" name="modulename" size="10" value="<?php echo isset($_REQUEST['modulename'])?$_REQUEST['modulename']:'' ?>">
				<label class="ml20"  for="">关键字：</label>
				<input type="text" name="key" size="10" value="<?php echo isset($_REQUEST['key'])?$_REQUEST['key']:'' ?>">
				<label class="ml20"  for="">时间：</label>
				<input type="text" id="createdate" name="createdate" size="10" value="<?php echo isset($_REQUEST['createdate'])?$_REQUEST['createdate']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="ml20 submitbtn"  type="button" value="提交">
				</form>
			</div>
			<hr>
			<table class="tablebox" width="100%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="80">用户</th>
						<th width="100">ip</th>
						<th width="100">模块</th>
						<th width="80">关键字</th>
						<th width="">日志</th>
						<th width="120">创建时间</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($logs['list'] as $log) {
							echo '<td>'.$log['username'].'</td>
										<td>'.$log['ip'].'</td>
										<td>'.$log['modulename'].'</td>
										<td>'.$log['key'].'</td>
										<td>'.$log['message'].'</td>
										<td>'.date('Y-m-d H:i',$log['createdate']).'</td></tr>';
						}
					?>
					<tr><td colspan="10"><?=$pages;?></td></tr>
				</tbody>
			</table>			
		</div>
	</div>
</div>
<script>
	$('#createdate').Zebra_DatePicker();
</script>