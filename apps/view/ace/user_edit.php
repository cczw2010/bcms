<form action="/manage/user/editinfo/">
<table class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" >
	<thead>
		<tr>
			<th colspan="2">编辑用户</th>
		</tr>
	</thead>
	<tbody>
		<?php if (isset($user)): ?>
		<tr>
			<td>用户名：</td>
			<td>
			<?php echo isset($user)?$user['username']:''; ?>
			<input type="hidden" name="username" value="<?php echo isset($user)?$user['username']:''; ?>">
			</td>
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
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?php echo isset($user)?$user['id']:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn" data-callback="dialogclose" value="提 交">
			</td>
		</tr>
		<?php else: ?>
			<tr>
				<td colspan="2" class="ccenter">
					<?php echo $errmsg; ?>
				</td>
			</tr>
		<?php endif ?>
		
	</tbody>
</table>
</form>