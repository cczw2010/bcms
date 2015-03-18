<form action="/manage/user/edit/">
<table class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" >
	<thead>
		<tr>
			<th colspan="2">编辑用户</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>用户名：</td>
			<td><input type="text" name="username" value="<?php echo isset($user)?$user['username']:''; ?>"></td>
		</tr>
		<tr>
			<td>邮箱：</td>
			<td><input type="text" name="email" value="<?php echo isset($user)?$user['email']:''; ?>"></td>
		</tr>
		<tr>
			<td>签名：</td>
			<td><input type="text" name="sign" value="<?php echo isset($user)?$user['sign']:''; ?>"></td>
		</tr>
		<tr>
			<td>用户组：</td>
			<td><select name="group" >
				<?php if (isset($user)&&$user['types']==Module_User::TYPE_USER): ?>
					<option value="<?php echo Module_Group::GROUP_GENERAL;?>">普通用户</option>
				<?php endif ?>
				<?php
					$cursid = isset($user)?$user['group']:Module_Group::GROUP_GENERAL; 
					foreach ($groups as $group) {
						echo '<option value="'.$group['id'].'" '.($cursid==$group['id']?'selected=true':'').' >'.$group['name'].'</option>';
					}
				?>
			</select></td>
		</tr>
		<tr>
			<td>状态:</td>
			<td>
				<?php echo SForm::build_checks_simple(Module_User::$statuss,'status',$user['status'],'radio');?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?php echo isset($user)?$user['id']:''; ?>">
				<input type="hidden" name="types" value="<?php echo isset($user)?$user['types']:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>