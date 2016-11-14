<div class="row">
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="alert alert-info">'.$errmsg.'</div>';
	}
	?>
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" data-target="#citylist">区域列表</a></li>

		<li class=""><a class="ajaxbtn btn btn-success" href="/manage/city/edit/?appid=<?php echo $appid; ?>" >添加新顶级区域</a>
	</ul>
	<div class="tab-content">
		<div class="widget-container-span ui-sortable tab-pane active" id="citylist">
			<div class="widget-header header-color-blue3">
				<form action="/manage/city/lists/?appid=<?php echo $appid; ?>">
				<label class="" for="">父项id：</label>
				<input type="text" name="parentId" size="10" value="<?php echo isset($filter['parentId'])?$filter['parentId']:'' ?>">
				<label class=""  for="">id：</label>
				<input type="text" name="id" size="15" value="<?php echo isset($filter['id'])?$filter['id']:'' ?>">
				<label class=""  for="">名称：</label>
				<input type="text" name="name" size="10" value="<?php echo isset($filter['name'])?$filter['name']:'' ?>">
				<label class="" for="">状态：</label>
				<select name="status" id="">
					<option value="-1" >全部</option>
					<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>>正常</option>
					<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>>锁定</option>
				</select>
				<input type="hidden" name="filterform" value="1">
				<input class="submitbtn btn btn-info btn-sm"  type="button" value="提交">
				</form>
			</div>
			<div class="widget-body">
				<table class="table table-striped table-bordered table-hover dataTable" width="80%" border="0" cellpadding="5" cellspacing="1" >
					<thead>
						<tr>
							<th width="50">id</th>
							<th width="">名称</th>
							<th width="100">邮编</th>
							<th width="">描述</th>
							<th width="50">状态</th>
							<th width="150">操作</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php
						$fItem = current($items);
						if (!empty($fItem)) {
							$baseDepth = $fItem['depth']; // 第一个区域的depth为基准depth,默认1
							foreach ($items as $id => $item) {
								echo '<tr><td>'.$item['id'].'</td><td align="left">';
								if ($item['depth']>$baseDepth) {
									$prefix=array_fill(0,($item['depth']-$baseDepth)*3,"&nbsp;");
									$prefix= implode($prefix);
									$prefix.='└';
									echo $prefix;
								}
								echo $item['name'].'</td>
									<td>'.$item['zipcode'].'</td>
									<td>'.$item['desc'].'</td>
									<td>'.Module_Area::$statuss[$item['status']].'</td>
									<td class="">
										<a href="/manage/city/edit/?parentId='.$item['id'].'&appid='.$appid.'" class="ajaxbtn">添加子项</a>
										<a href="/manage/city/edit/'.$item['id'].'" class="ajaxbtn">编辑</a>
										<a href="/manage/city/del/?id='.$item['id'].'&appid='.$appid.'"  class="ajaxbtn" data-confirm="确认删除吗？该操作不可恢复，并且其下所有子项将一并删除！同时如果已有其他资源关联到本项，请慎重！">删除</a>
									</td></tr>';
							}
						}
						?>
						<tr><td colspan="6"><?=(isset($pages)?$pages:'');?></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>