<div class="row">
	<?php if (!empty($errmsg)): ?>
	<div class="alert alert-info">
		<i class="icon-warning-sign"></i>
		<?php echo $errmsg; ?>
		<button type="button" class="close" data-dismiss="alert">
			<i class="icon-remove"></i>
		</button>
	</div>
	<?php endif ?>
	<div class="col-12 widget-container-span ui-sortable">
		<div class="widget-box">
			<div class="widget-header header-color-blue3">
				<form action="/manage/user/ulogs/">
				<label class=""  for="">用户id：</label>
				<input class="input-sm" type="text" name="userid" size="10" value="<?php echo isset($_REQUEST['userid'])?$_REQUEST['userid']:'' ?>">
				<label class=""  for="">用户名：</label>
				<input class="input-sm" type="text" name="username" size="10" value="<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'' ?>">
				<label class=""  for="">时间：</label>
				<input class="date-picker-mlogs input-sm" data-date-format="yyyy-mm-dd" type="date" id="addtime" name="addtime" value="<?php echo isset($_REQUEST['addtime'])?$_REQUEST['addtime']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="btn btn-success btn-sm submitbtn"  type="button" value="提交">
				</form>
			</div>
			<div class="widget-body">
				<div class="widget-main no-padding">
					<table class="table table-striped table-bordered table-hover dataTable">
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
	</div>
</div>
<script>
	// $('#addtime').Zebra_DatePicker();
</script>