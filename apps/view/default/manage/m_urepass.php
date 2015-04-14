<form action="/manage/user/repass/">
<table class="tablebox formtable" width="300" border="0" cellpadding="10" cellspacing="1" >
	<thead>
		<tr>
			<th colspan="2">重置密码</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="150">密码：</td>
			<td><input type="text" name="password" value=""></td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?php echo $user['id']; ?>">
				<input type="button" name="submitbtn" class="submitbtn" data-callback=""  value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>