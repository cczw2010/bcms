<form action="/manage/user/edit/">
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th colspan="2"><?php echo isset($oitem)?'编辑用户':'新增用户(新增用户密码初始为:123456)'; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>用户名：</td>
			<td><input type="text" name="username" value="<?php echo isset($oitem)?$oitem['username']:''; ?>"></td>
		</tr>
		<tr>
			<td>邮箱：</td>
			<td><input type="text" name="email" value="<?php echo isset($oitem)?$oitem['email']:''; ?>"></td>
		</tr>
		<tr>
			<td>签名：</td>
			<td><input type="text" name="sign" value="<?php echo isset($oitem)?$oitem['sign']:''; ?>"></td>
		</tr>
		<tr>
			<td>用户组：</td>
			<td><select name="group" >
				<option value="0">无分组</option>
				<?php
					$cursid = isset($oitem)?$oitem['group']:0;
					foreach ($groups as $group) {
						echo '<option value="'.$group['id'].'" '.($cursid==$group['id']?'selected=true':'').' >'.$group['name'].'</option>';
					}
				?>
			</select></td>
		</tr>
		<tr>
			<td>状态:</td>
			<td>
			<?php 
				$status = isset($oitem)?$oitem['status']:0;
				echo SForm::buildChecksSimple(Module_User::$statuss,'status',$status,'radio');
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="text-center">
				<input type="hidden" name="id" value="<?php echo isset($oitem)?$oitem['id']:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>