<div class="row">
	<div class="alert alert-info">
		<i class="icon-warning-sign"></i>
		首先数据库的配置不能是空密码哦，另外导出的sql文件里可能会有一条警告信息，去掉就可以正常使用了
	</div>
	<div class="text-warning orange">
		<span id="ajaxmsg"></span>
	</div>
	<div class="col-12 widget-container-span ui-sortable">
		<div class="widget-box">
			<div class="widget-header header-color-blue3">
				<h5 class="bigger lighter">
					<i class="icon-table"></i>
					数据库备份
				</h5>
			</div>
			<div class="widget-body">
				<div class="widget-main no-padding">
					<form action="<?php echo Uri::build('manage/setting','dbback') ;?>">
					<table class="table table-striped table-bordered table-hover">
						<tbody>
							<tr>
								<td class="">mysqldump路径:</td>
								<td>
									<input type="text" name="dumppath" value="<?php echo isset($dumppath)?$dumppath:'mysqldump'; ?>">
								</td>
								<td class="hidden-480">
									linux基本都能直接调用，windows一般没有配到path里就不能直接调用，需要完整路径。值得注意的是路径中别有空格
								</td>
							</tr>
							<tr>
								<td>备份路径:</td>
								<td>
									<input type="text" name="savepath" value="<?php echo isset($savepath)?$savepath:'backup'; ?>">
								</td>
								<td>不建议修改，默认站点根目录backup目录，请注意目标文件夹的读写权限</td>
							</tr>
							<tr>
								<td>备份内容:</td>
								<td>
									<select class="form-control" name="savetype" id="savetype">
										<option value="all">数据库结构和数据</option>
										<option value="structure">数据库结构</option>
									</select>
								</td>
								<td></td>
							</tr>
							<tr>
								<td colspan="3">
									<input type="button"  name="submitbtn" class="btn btn-yellow submitbtn returnjson" value="备 份">
								</td>
							</tr>
						</tbody>
					</table>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>