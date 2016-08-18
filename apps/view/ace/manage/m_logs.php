<div class="row">
	<div class="alert alert-info">
		<i class="icon-warning-sign"></i>
		该处记录了一些后端进行的修改数据库的操作日志
		<button type="button" class="close" data-dismiss="alert">
			<i class="icon-remove"></i>
		</button>
	</div>
	<div class="text-warning orange">
		<span id="ajaxmsg"></span>
	</div>
	<div class="col-12 widget-container-span ui-sortable">
		<div class="widget-box">
			<div class="widget-header header-color-blue3">
				<form action="/manage/setting/logs/">
				<label for="">用户名：</label>
				<input class="input-sm" type="text" name="username" value="<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'' ?>">
				<label for="">模块：</label>
				<input class="input-sm" type="text" name="modulename" value="<?php echo isset($_REQUEST['modulename'])?$_REQUEST['modulename']:'' ?>">
				<label for="">关键字：</label>
				<input class="input-sm" type="text" name="key" value="<?php echo isset($_REQUEST['key'])?$_REQUEST['key']:'' ?>">
				<label for="">时间：</label>
				<input class="date-picker-mlogs input-sm" data-date-format="yyyy-mm-dd" type="text" id="createdate" name="createdate" value="<?php echo isset($_REQUEST['createdate'])?$_REQUEST['createdate']:'' ?>">
				<i class="icon-calendar bigger-110"></i>
				<input type="hidden" name="filterform" value="1">
				<input class="btn btn-info btn-sm submitbtn"  type="button" value="提交">
				</form>
			</div>
			<div class="widget-body">
				<div class="widget-main no-padding">
					<form action="/manage/setting/logs/">
					<table class="table table-striped table-bordered table-hover dataTable">
						<thead>
							<tr>
								<th width="80">用户</th>
								<th width="100">ip</th>
								<th width="100">模块</th>
								<th width="80">关键字</th>
								<th width="">日志</th>
								<th class="sorting" width="150">创建时间</th>
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
						</tbody>
					</table>
					</form>
					<div class="space-10"></div>
					<div>
						<?=$pages;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('.date-picker-mlogs').datepicker({autoclose:true}).next().on(ace.click_event, function(){
		$(this).prev().focus();
	});
</script>
