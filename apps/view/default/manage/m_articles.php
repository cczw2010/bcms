<div class="tabbox">
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active">文章列表</li>
		<li class="tablabel"><span class="xicon">g</span><span data-url="/manage/article/edit/" class="ajaxbtn">新增</span></li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<div class="filter">
				<form action="/manage/article/lists/">
				<span class="xicon mr10">!</span>筛选：
				<label class="ml20" for="">分类：</label>
				<select name="cateid" id="">
					<option value="0" >全部</option>
					<?=$options ;?>
				</select>
				<label class="ml20" for="">状态：</label>
				<select name="status" id="">
					<option value="-1" >全部</option>
					<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>>正常</option>
					<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>>锁定</option>
				</select>
				<label class="ml20" for="">置顶：</label>
				<select name="istop" id="">
					<option value="-1" >全部</option>
					<option value="1" <?php if(isset($filter['istop'])&&($filter['istop']==1)){echo 'selected';} ?>>是</option>
					<option value="0" <?php if(isset($filter['istop'])&&($filter['istop']==0)){echo 'selected';} ?>>否</option>
				</select>
				<label class="ml20" for="">热推：</label>
				<select name="ishot" id="">
					<option value="-1" >全部</option>
					<option value="1" <?php if(isset($filter['ishot'])&&($filter['ishot']==1)){echo 'selected';} ?>>是</option>
					<option value="0" <?php if(isset($filter['ishot'])&&($filter['ishot']==0)){echo 'selected';} ?>>否</option>
				</select>
				<label class="ml20"  for="">作者：</label>
				<input type="text" name="username" size="10" value="<?php echo isset($filter['username'])?$filter['username']:'' ?>">
				<label class="ml20"  for="">标题：</label>
				<input type="text" name="title" size="10" value="<?php echo isset($filter['title'])?$filter['title']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="ml20 submitbtn"  type="button" value="提交">
				</form>
			</div>
			<hr>
			<table class="tablebox" width="100%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="20">id</th>
						<th width="">标题</th>
						<th width="60">作者</th>
						<th width="30">浏览</th>
						<th width="30">评论</th>
						<th width="30">收藏</th>
						<th width="30">喜欢</th>
						<th width="30">置顶</th>
						<th width="30">热门</th>
						<th width="100">创建时间</th>
						<th width="40">状态</th>
						<th width="120">操作</th>
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
									<span data-url="/manage/article/comm/?objid='.$item['id'].'"  class="ajaxbtn">评论</span>
									<span data-url="/manage/article/edit/'.$item['id'].'"  class="ajaxbtn">编辑</span>
									<span data-url="/manage/article/del/'.$item['id'].'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</span>
									</td>';
						}
					}
					?>
					<tr><td colspan="17"><?=(isset($pages)?$pages:'');?></td></tr>
				</tbody>
			</table>			
		</div>
	</div>
</div>