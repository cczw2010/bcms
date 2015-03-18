<div class="tabbox groupmain">
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active">管理员列表</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
		<div class="filter">
				<form action="/manage/user/managers/">
				<span class="xicon mr10">!</span>筛选：
				<label class="ml20" for="">状态：</label>
				<select name="status" id="">
					<option value="-1" >全部</option>
					<?=SForm::build_options_simple(Module_User::$statuss,$conds['status']);?>
				</select>
				<label class="ml20"  for="">用户名：</label>
				<input type="text" name="username" size="10" value="<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'' ?>">
				<label class="ml20"  for="">邮箱：</label>
				<input type="text" name="email" size="10" value="<?php echo isset($_REQUEST['email'])?$_REQUEST['email']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="ml20 submitbtn"  type="button" value="提交">
				</form>
			</div>
			<hr>
			<table class="tablebox" width="100%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="20">id</th>
						<th width="80">用户名</th>
						<th width="80">分 组</th>
						<th width="80">登陆次数</th>
						<th width="100">注册时间</th>
						<th width="100">最后登录</th>
						<th width="40">状 态</th>
						<th width="150">操 作</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($users['list'] as $user) {
							echo '<tr><td class="ccenter">'.$user['id'].'</td>
										<td>'.$user['username'].'</td>
										<td class="ccenter">'.(isset($groups[$user['group']])?$groups[$user['group']]['name']:'无').'</td>
										<td class="ccenter">'.$user['logincount'].'</td>
										<td>'.date('Y-m-d H:i',$user['addtime']).'</td>
										<td>'.date('Y-m-d H:i',$user['lasttime']).'</td>
										<td class="ccenter">'.(Module_User::$statuss[$user['status']]).'</td>
										<td class="">
											<span data-url="/manage/user/edit/'.$user['id'].'/'.Module_User::TYPE_MANAGER.'"  data-flusharea=".tabbody.active" class="ajaxbtn">编辑</span>
											<span data-url="/manage/user/del/'.$user['id'].'/'.Module_User::TYPE_MANAGER.'" data-confirm="确认删除吗？该操作不可恢复，并且该用户的所有关联信息将失效，请慎重！" class="ajaxbtn">删除</span>
										</td></tr>';
						}
					?>
					<tr><td colspan="10"><?=$pages;?></td></tr>
				</tbody>
			</table>			
		</div>
	</div>
</div>
<script>
	// buildTab('.groupmain');
</script>