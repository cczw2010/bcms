<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active">
			<a data-toggle="tab" href="#ugrouplist"><?php echo Module_Group::$types[$types];?>列表</a>
		</li>
		<li class="">
			<a data-toggle="tab" href="#ugroupadd">添加新<?php echo Module_Group::$types[$types];?></a>
		</li>
	</ul>
	<div class="tab-content">
		<div id="ugrouplist" class="widget-container-span ui-sortable tab-pane active">
			<div class="widget-box transparent">
				<div class="widget-header header-color-blue3" style="min-height: 0px;overflow: hidden;"></div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<table class="table table-striped table-bordered table-hover dataTable">
							<thead>
								<tr>
									<th width="50">id</th>
									<th width="100">名称</th>
									<th width="200">描述</th>
									<th width="100">操作</th>
								</tr>
							</thead>	
							<tbody>
								<?php
									foreach ($groups['list'] as $id => $group) {
										echo '<tr><td>'.$group['id'].'</td>
													<td>'.$group['name'].'</td>
													<td>'.$group['desc'].'</td>
													<td class="">'.($id>0?
															'<a class="green ajaxbtn" href="/manage/user/gedit/'.$group['id'].'">'.
																'<i class="icon-pencil bigger-130"></i>'.
															'</a> <a class="red ajaxbtn" href="/manage/user/gdel/'.$group['id'].'"  data-confirm="确认删除吗？如果该组下已经有了用户，其权限信息将丢失，请慎重！">'.
																'<i class="icon-trash bigger-130"></i>'.
															'</a>':'').
													'</td></tr>';
									}
								?>
							</tbody>
						</table>
					</div><!-- /widget-main -->
				</div><!-- /widget-body -->
			</div>
		</div>
		<div id="ugroupadd" class="widget-container-span ui-sortable tab-pane ">
			<div class="widget-box transparent">
				<div class="widget-body">
					<div class="widget-main no-padding">
						<form action="/manage/user/gedit/">
						<table class="table table-bordered table-striped">
							<tbody>
								<tr>
									<td width="60">名称:</td>
									<td><input class="input-sm" type="text" name="name" value=""></td>
								</tr>
								<tr>
									<td>描述:</td>
									<td>
										<textarea name="desc" id="" cols="30" rows="5"  value=""></textarea>
									</td>
								</tr>
								<?php if ($types == 0) { ?>
								<tr>
									<td>权限:</td>
									<td>
										<?php
										foreach ($modules as $module => $methods) {
										?>
										<div class="rightbox">
											<div class="right_title">
												<input class="right_c ml10" type="checkbox" value="<?=$module; ?>"><?=$module; ?>
											</div>
											<ul class="clearfix">
											<?php
												foreach ($methods as $method=>$mname) {
														$val = $module.'-'.$method;
														echo '<li><input class="right_m ml10" type="checkbox" name="rights[]" value="'.$val.'">'.$mname.'</li>';					
													}
											?>
											</ul>
										</div>
										<?php	}?>
									</td>
								</tr>	
								<?php }?>
								<tr>
									<td colspan="2" class="text-center">
										<input type="hidden" name="types" value="<?php echo isset($types)?$types:0; ?>">
										<input type="hidden" name="id" value="">
										<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
									</td>
								</tr>
							</tbody>
						</table>
						</form>
					</div><!-- /widget-main -->
				</div><!-- /widget-body -->
			</div>
		</div>
	</div>



