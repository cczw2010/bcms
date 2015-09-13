'<form action="/manage/user/medit/">
	<table class="col-12 table table-bordered table-striped">
	<thead>
		<tr>
			<th colspan="2">编辑管理员</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>用户名：</td>
			<td><input type="text" <?php if (isset($user)): ?>disabled="disabled"<?php endif ?> name="username" value="<?php echo isset($user)?$user['username']:''; ?>"></td>
		</tr>
		<tr>
			<td>密码：</td>
			<td><input type="text" name="password" value="">（留空代表不修改密码）</td>
		</tr>
		<tr>
			<td>用户组：</td>
			<td><select name="group" >
				<?php
					foreach ($groups as $group) {
						echo '<option value="'.$group['id'].'" '.(isset($user) && $user['group']==$group['id']?'selected=true':'').' >'.$group['name'].'</option>';
					}
				?>
			</select></td>
		</tr>
		<tr>
			<td colspan="2" class="text-center">
				<input type="hidden" name="id" value="<?php echo isset($user)?$user['id']:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>