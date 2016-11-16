<form action="/manage/city/edit/">
<table class="table table-striped table-bordered table-hover dataTable" border="0" cellpadding="10" cellspacing="1" >
	<thead>
		<tr>
			<th colspan="2">编辑区域信息</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="80">父项：</td>
			<td width="100"><?php echo $pname;?></td>
		</tr>
		<tr>
			<td>名称：</td>
			<td><input type="text" name="name" value="<?php echo isset($item)?$item['name']:''; ?>"></td>
		</tr>
		<tr>
			<td>邮编：</td>
			<td><input type="text" name="zipcode" value="<?php echo isset($item)?$item['zipcode']:''; ?>"></td>
		</tr>
		<tr>
			<td>描述：</td>
			<td><textarea name="desc" cols="60" rows="8"><?php echo isset($item)?$item['desc']:''; ?></textarea></td>
		</tr>
		<tr>
			<td>状态:</td>
			<td>
				启用<input class="mr20" type="radio" <?=(!isset($item) || $item['status']==1)?'checked="checked"':'';?> name="status" value="1" >
				不启用<input type="radio" <?=(isset($item) && $item['status']==0)?'checked="checked"':'';?> name="status" value="0" >
			</td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?php echo isset($item)?$item['id']:''; ?>">
				<input type="hidden" name="appid" value="<?php echo isset($appid)?$appid:0; ?>">
				<input type="hidden" name="parentId" value="<?php echo isset($parentId)?$parentId:0; ?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>