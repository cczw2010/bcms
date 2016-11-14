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
	<div class="widget-box transparent">
		<div class="widget-header header-color-blue3">
			<form action="/manage/article/lists/">
			<label class="" for="">分类：</label>
			<select name="cateid" id="">
				<option value="0" >全部</option>
				<?=$options ;?>
			</select>
			<label class="" for="">状态：</label>
			<select name="status" id="">
				<option value="-1" >全部</option>
				<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>>正常</option>
				<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>>锁定</option>
			</select>
			<label class="" for="">置顶：</label>
			<select name="istop" id="">
				<option value="-1" >全部</option>
				<option value="1" <?php if(isset($filter['istop'])&&($filter['istop']==1)){echo 'selected';} ?>>是</option>
				<option value="0" <?php if(isset($filter['istop'])&&($filter['istop']==0)){echo 'selected';} ?>>否</option>
			</select>
			<label class="" for="">热推：</label>
			<select name="ishot" id="">
				<option value="-1" >全部</option>
				<option value="1" <?php if(isset($filter['ishot'])&&($filter['ishot']==1)){echo 'selected';} ?>>是</option>
				<option value="0" <?php if(isset($filter['ishot'])&&($filter['ishot']==0)){echo 'selected';} ?>>否</option>
			</select>
			<label class=""  for="">作者：</label>
			<input type="text" name="username" size="10" value="<?php echo isset($filter['username'])?$filter['username']:'' ?>">
			<label class=""  for="">标题：</label>
			<input type="text" name="title" size="10" value="<?php echo isset($filter['title'])?$filter['title']:'' ?>">
			<input type="hidden" name="filterform" value="1">
			<input class="submitbtn btn btn-info btn-sm"  type="button" value="提 交">
			</form>
		</div>
		<div class="widget-body">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th width="50">id</th>
						<th width="">标题</th>
						<th width="">作者</th>
						<th width="">浏览</th>
						<th width="">评论</th>
						<th width="">收藏</th>
						<th width="">喜欢</th>
						<th width="">置顶</th>
						<th width="">热门</th>
						<th width="">创建时间</th>
						<th width="">状态</th>
						<th width="">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($items)) {
						foreach ($items['list'] as $id => $item) {
							echo '<tr><td>'.$id.'</td>
									<td>'.$item['title'].'</td>
									<td>'.$item['username'].'</td>
									<td>'.$item['viewnum'].'</td>
									<td>'.$item['comnum'].'</td>
									<td>'.$item['favnum'].'</td>
									<td>'.$item['likenum'].'</td>
									<td>'.$item['istop'].'</td>
									<td>'.$item['ishot'].'</td>
									<td>'.date('Y-m-d H:i',$item['createdate']).'</td>
									<td>'.Module_Article::$statuss[$item['status']].'</td>
									<td>
									<a href="/manage/article/comm/?objid='.$item['id'].'"  class="ajaxbtn">评论</a>
									<a href="/manage/article/edit/'.$item['id'].'"  class="ajaxbtn">编辑</a>
									<a href="/manage/article/del/'.$item['id'].'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</a>
									</td>';
						}
					}
					?>
					<tr><td colspan="12"><?=(isset($pages)?$pages:'');?></td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>