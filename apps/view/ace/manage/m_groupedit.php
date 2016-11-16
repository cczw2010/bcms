<form action="/manage/user/gedit/">
	<table class="col-12 table table-bordered table-striped">
		<thead>
			<tr>
				<th colspan="2">编辑分组</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="70">名称：</td>
				<td><input type="text" name="name" value="<?php echo isset($group)?$group['name']:''; ?>"></td>
			</tr>
			<tr>
				<td>描述:</td>
				<td>
					<textarea name="desc" id="" cols="30" rows="5"><?php echo isset($group)?$group['desc']:''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="text-center">
					<input type="hidden" name="id" value="<?php echo isset($group)?$group['id']:''; ?>">
					<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
				</td>
			</tr>
		</tbody>
	</table>
</form>
<script>
	// 权限组选取
	$('.right_c').on('click',function(){
		var o = $(this),
			check = o.is(':checked');
		o.parents('.rightbox').find('.right_m').attr('checked',check);
	});
	$('.right_m').on('click',function(){
		var o = $(this),
			p = o.parents('.rightbox'),
			allcheck = p.find('.right_m:not(:checked)').length==0;
		p.find('.right_c').attr('checked',allcheck);
	});
</script>