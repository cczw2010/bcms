<form action="/manage/product/propedit/">
<table class="tablebox formtable" border="0"  width="80%"  cellpadding="10" cellspacing="1" >
	<thead>
		<tr>
			<th colspan="2">编辑属性</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>名称：</td>
			<td><input type="text" name="name" value="<?php echo isset($prop)?$prop['name']:''; ?>"></td>
		</tr>
		<tr>
			<td>初始值：</td>
			<td><textarea name="vals" cols="30" rows="10"><?php echo isset($prop)?$prop['vals']:''; ?></textarea></td>
		</tr>
		<tr>
			<td>描述：</td>
			<td><textarea name="desc" cols="30" rows="10"><?php echo isset($prop)?$prop['desc']:''; ?></textarea></td>
		</tr>
		<tr>
			<td>状态:</td>
			<td>
				启用<input class="mr20" type="radio" <?=(!isset($prop) || $prop['status']==1)?'checked="checked"':'';?> name="status" value="1" >
				不启用<input type="radio" <?=(isset($prop) && $prop['status']==0)?'checked="checked"':'';?> name="status" value="0" >
			</td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?php echo isset($prop)?$prop['id']:''; ?>">
				<input type="hidden" name="appid" value="<?php echo isset($prop)?$prop['appid']:0; ?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>