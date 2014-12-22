<div class="tabbox groupmain">
	<div class="info">
		<span class="xicon mr10">R</span> tips:管理员组中的权限信息是根据页面的地址来组合的。每个方法对应一个页面。用户的分组目前只做用户界别界定，具体逻辑前端实现
	</div>
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active"><?php echo Module_Group::$types[$types];?>列表</li>
		<li class="tablabel ">添加新<?php echo Module_Group::$types[$types];?></li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<table class="tablebox" width="100%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="50">id</th>
						<th width="100">名称</th>
						<th width="200">描述</th>
						<th width="100">操作</th>
					</tr>
				</thead>
				<tbody  align="center">
					<?php
						foreach ($groups['list'] as $id => $group) {
							echo '<tr><td>'.$group['id'].'</td>
										<td>'.$group['name'].'</td>
										<td>'.$group['desc'].'</td>';
							echo	'<td class="">'.($id>0?
											'<span data-url="'.Uri::build('manage','pgroupedit',array($group['id'])).'" data-flusharea=".tabbody.active"  class="ajaxbtn">编辑</span>
											<span data-url="'.Uri::build('manage','pgroupdel',array($group['id'])).'" data-confirm="确认删除吗？如果该组下已经有了用户，其权限信息将丢失，请慎重！" class="ajaxbtn">删除</span>':'').
										'</td></tr>';
						}
					?>
				</tbody>
			</table>			
		</div>
		<div class="tabbody">
			<form action="<?php echo Uri::build('manage','pgroupedit') ;?>">
				<table class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" >
					<thead>
						<tr>
							<th colspan="2">新建<?php echo Module_Group::$types[$types];?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="60">名称:</td>
							<td><input type="text" name="name" value=""></td>
						</tr>
						<tr>
							<td>描述:</td>
							<td>
								<textarea name="desc" id="" cols="30" rows="5"  value=""></textarea>
							</td>
						</tr>
						<?php if ($types == 0) { ?>
						<tr>
							<td>权限:</td>
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
												echo '<li><input class="right_m ml10" type="checkbox" name="rights[]" value="'.$val.'">'.$mname.'</li>';					
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
								<input type="hidden" name="types" value="<?php echo isset($types)?$types:0; ?>">
								<input type="hidden" name="id" value="">
								<input type="button" name="submitbtn" class="submitbtn" value="提 交">
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<script>
	buildTab('.groupmain');
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