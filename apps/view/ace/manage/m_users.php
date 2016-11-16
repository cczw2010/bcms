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
				<form action="/manage/user/users/">
				<label class="" for="">状态：</label>
				<select name="status" id="">
					<option value="-1" >全部</option>
					<?=SForm::buildOptionsSimple(Module_User::$statuss,$conds['status']);?>
				</select>
				<label class=""  for="">用户名：</label>
				<input class="input-sm" type="text" name="username" size="10" value="<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'' ?>">
				<label class=""  for="">邮箱：</label>
				<input class="input-sm" type="text" name="email" size="10" value="<?php echo isset($_REQUEST['email'])?$_REQUEST['email']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="btn btn-info btn-sm submitbtn"  type="button" value="提交">
				</form>
			</div>
			<div class="widget-body">
				<div class="widget-main no-padding">
					<table class="table table-striped table-bordered table-hover dataTable">
						<thead>
							<tr>
								<th width="50">id</th>
								<th width="100">用户名</th>
								<th width="">邮 箱</th>
								<th width="">分 组</th>
								<th width="">登陆次数</th>
								<th width="">注册时间</th>
								<th width="">最后登录</th>
								<th width="">状 态</th>
								<th width="">操 作</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($users['list'] as $user) {
									echo '<tr><td class="text-center">'.$user['id'].'</td>
												<td>'.$user['username'].'</td>
												<td>'.$user['email'].'</td>
												<td class="text-center">'.(isset($groups[$user['group']])?$groups[$user['group']]['name']:'无').'</td>
												<td class="text-center">'.$user['logincount'].'</td>
												<td>'.date('Y-m-d H:i',$user['addtime']).'</td>
												<td>'.date('Y-m-d H:i',$user['lasttime']).'</td>
												<td class="text-center">'.(Module_User::$statuss[$user['status']]).'</td>
												<td class="">
													<a href="/manage/user/address/?id='.$user['id'].'" class="ajaxbtn">地址列表</a>
													<a href="/manage/user/edit/'.$user['id'].'" class="ajaxbtn">编辑</a>
													<a href="/manage/user/del/'.$user['id'].'" data-confirm="确认删除吗？该操作不可恢复，并且该用户的所有关联信息将失效，请慎重！" class="ajaxbtn">删除</a>
												</td></tr>';
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