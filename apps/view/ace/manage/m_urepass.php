<form action="/manage/user/repass/">
<table class="table table-striped table-bordered table-hover">
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
			<td colspan="2" class="text-center">
				<input type="hidden" name="id" value="<?php echo $user['id']; ?>">
				<input type="button" name="submitbtn" class="submitbtn btn btn-info" data-callback=""  value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>