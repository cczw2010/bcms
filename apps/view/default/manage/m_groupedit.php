<form action="/manage/user/gedit/">
	<table width="100%" class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" >
		<thead>
			<tr>
				<th colspan="2">编辑用户组</th>
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
			<?php if ($group['types'] == 0) { ?>
			<tr>
				<td>权限：</td>
				<td>
					<?php
					foreach ($modules as $module => $methods) {
					?>
					<div class="rightbox">
						<div class="right_title">
							<input class="right_c ml10" type="checkbox" value="<?=$module; ?>"><?=$module; ?>
						</div>
						<ul class="clearfix">
						<?php
							foreach ($methods as $method=>$mname) {
									$val = $module.'-'.$method;
									if (isset($group) && !empty($group['rights'])) {
										$checked = preg_match('/'.$val.'(,|$)/',$group['rights']);
										// dump('/'.$val.'(,|$)/',$val,$checked);
									}else{
										$checked = false;
									}
									echo '<li><input '.($checked?'checked="true"':'').' class="right_m ml10" type="checkbox" name="rights[]" value="'.$val.'">'.$mname.'</li>';					
							}
						?>
						</ul>
					</div>
					<?php	}?>
				</td>
			</tr>
			<?php }?>
			<tr>
				<td colspan="2" class="ccenter">
					<input type="hidden" name="types" value="<?php echo isset($group)?$group['types']:0; ?>">
					<input type="hidden" name="id" value="<?php echo isset($group)?$group['id']:''; ?>">
					<input type="button" name="submitbtn" class="submitbtn" value="提 交">
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