<div class="tabbox groupmain">
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active">用户登录日志列表</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<div class="filter">
				<form action="<?php echo Uri::build('manage','puserlog'); ?>">
				<span class="xicon mr10">!</span>筛选：
				<label class="ml20"  for="">用户id：</label>
				<input type="text" name="userid" size="10" value="<?php echo isset($_REQUEST['userid'])?$_REQUEST['userid']:'' ?>">
				<label class="ml20"  for="">用户名：</label>
				<input type="text" name="username" size="10" value="<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'' ?>">
				<label class="ml20"  for="">时间：</label>
				<input type="text" id="addtime" name="addtime" size="10" value="<?php echo isset($_REQUEST['addtime'])?$_REQUEST['addtime']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="ml20 submitbtn"  type="button" value="提交">
				</form>
			</div>
			<hr>
			<table class="tablebox" width="100%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="50">用户id</th>
						<th width="100">用户名</th>
						<th width="100">来源</th>
						<th width="100">登录ip</th>
						<th width="120">登录时间</th>
						<th width="">useragent</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($logs['list'] as $log) {
							echo '<td>'.$log['userid'].'</td>
										<td>'.$log['username'].'</td>
										<td>'.$log['befrom'].'</td>
										<td>'.$log['ip'].'</td>
										<td>'.date('Y-m-d H:i',$log['addtime']).'</td>
										<td>'.$log['useragent'].'</td></tr>';
						}
					?>
					<tr><td colspan="10"><?=$pages;?></td></tr>
				</tbody>
			</table>			
		</div>
	</div>
</div>
<script>
	$('#addtime').Zebra_DatePicker();
</script>