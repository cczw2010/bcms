<div class="row">
	<?php if (!empty($errmsg)): ?>
	<div class="alert alert-info">
		<i class="icon-warning-sign"></i>
		<?php echo $errmsg; ?>
		<button type="button" class="close" data-dismiss="alert">
			<i class="icon-remove"></i>
		</button>
	</div>
	<?php endif ?>
	<ul class="nav nav-tabs">
		<li class="active">
			<a data-toggle="tab" data-target="#catelist">分类列表</a>
		</li>
		<li>
			 <a class="ajaxbtn btn btn-success" href="/manage/category/edit/?<?php echo 'moduleid='.$moduleid; ?>" >添加新顶级分类</a>
		</li>
	</ul>
	<div class="tab-content">
		<div id="catelist" class="widget-container-span ui-sortable tab-pane active">
			<div class="widget-box transparent">
				<div class="widget-header header-color-blue3" style="">
					<form action="/manage/category/lists/?moduleid=<?php echo $moduleid;?>">
					<label for="">父项id：</label>
					<input class="input-sm" type="text" name="parentId" size="10" value="<?php echo isset($filter['parentId'])?$filter['parentId']:'' ?>">
					<label for="">id：</label>
					<input class="input-sm" type="text" name="id" size="10" value="<?php echo isset($filter['id'])?$filter['id']:'' ?>">
					<label for="">名称：</label>
					<input class="input-sm" type="text" name="name" size="10" value="<?php echo isset($filter['name'])?$filter['name']:'' ?>">
					<label for="">状态：</label>
					<select class="input-sm"  name="status" id="">
						<option value="-1" >全部</option>
						<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>>正常</option>
						<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>>锁定</option>
					</select>
					<input type="hidden" name="filterform" value="1">
					<input class="submitbtn btn btn-info btn-sm"  type="button" value="提 交">
					</form>
				</div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<table class="table table-striped table-bordered table-hover dataTable">
							<thead>
								<tr>
									<th width="50">id</th>
									<th width="">名称</th>
									<th width="">描述</th>
									<th width="">状态</th>
									<th width="">操作</th>
								</tr>
							</thead>
							<tbody align="center">
								<?php
								$fItem = current($items);
								if (!empty($fItem)) {
									$baseDepth = $fItem['depth']; // 第一个分类的depth为基准depth,默认1
									foreach ($items as $id => $item) {
										echo '<tr><td>'.$item['id'].'</td><td align="left">';
										if ($item['depth']>$baseDepth) {
											$prefix=array_fill(0,($item['depth']-$baseDepth)*3,"&nbsp;");
											$prefix= implode($prefix);
											$prefix.='└';
											echo $prefix;
										}
										echo $item['name'].'</td>
											<td>'.$item['desc'].'</td>
											<td>'.Module_Category::$statuss[$item['status']].'</td>
											<td class="">
												<a href="/manage/category/edit/?parentId='.$item['id'].'&moduleid='.$moduleid.'" class="ajaxbtn">添加子项</a>
												<a href="/manage/category/edit/'.$item['id'].'" class="ajaxbtn">编辑</a>
												<a href="/manage/category/del/?id='.$item['id'].'&moduleid='.$moduleid.'"  class="ajaxbtn" data-confirm="确认删除吗？该操作不可恢复，并且其下所有子项将一并删除！同时如果已有其他资源关联到本项，请慎重！">删除</a>
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
	</div>
</div>