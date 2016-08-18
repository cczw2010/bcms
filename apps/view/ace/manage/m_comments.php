<div class="row tabbable">
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
			<form action="/manage/comment/lists/?appid=<?php echo $appid;?>">
			<label for="">状态：</label>
			<select name="status" id="">
				<option value="-1" >全部</option>
				<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>>正常</option>
				<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>>锁定</option>
			</select>
			<label for="">评论对象id：</label>
			<input type="text" name="objid" size="10" value="<?php echo isset($filter['objid'])?$filter['objid']:'' ?>">
			<label for="">作者：</label>
			<input type="text" name="username" size="10" value="<?php echo isset($filter['username'])?$filter['username']:'' ?>">
			<label for="">内容：</label>
			<input type="text" name="message" size="10" value="<?php echo isset($filter['message'])?$filter['message']:'' ?>">
			<input type="hidden" name="filterform" value="1">
			<input class="submitbtn btn btn-info btn-sm" type="button" value="提 交">
			</form>
		</div>
		<div class="widget-body">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th width="50">id</th>
						<th>作者</th>
						<th>评分</th>
						<th>内容</th>
						<th>创建时间</th>
						<th>状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($items)) {
						foreach ($items['list'] as $id => $item) {
							echo '<tr><td>'.$id.'</td>
									<td>'.$item['username'].'</td>
									<td>'.$item['score'].'</td>
									<td>'.mb_substr($item['message'],0,100).'...</td>
									<td>'.date('Y-m-d H:i:s',$item['createdate']).'</td>
									<td>'.Module_Comment::$statuss[$item['status']].'</td>
									<td>
									<a href="/manage/comment/edit/'.$item['id'].'/'.$appid.'"  class="ajaxbtn">编辑</a>
									<a href="/manage/comment/del/'.$item['id'].'/'.$appid.'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</a>
									</td>';
						}
					}
					?>
					<tr><td colspan="7" align="center"><?=(isset($pages)?$pages:'');?></td></tr>
				</tbody>
			</table>	
		</div>
	</div>
</div>