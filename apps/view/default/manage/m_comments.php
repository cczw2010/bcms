<div class="tabbox">
	<div class="info"><span class="xicon mr10">R</span> tips:评论模块的所在模块的appid是<?=$appid;?></div>
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel ajaxbtn active" data-url="<?php echo Uri::build('manage','pcomms').'?appid='.$appid;?>">评论列表</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<div class="filter">
				<form action="<?php echo Uri::build('manage','pcomms').'?appid='.$appid ;?>">
				<span class="xicon mr10">!</span>筛选：
				<label class="ml20" for="">状态：</label>
				<select name="status" id="">
					<option value="-1" >全部</option>
					<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>>正常</option>
					<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>>锁定</option>
				</select>
				<label class="ml20"  for="">评论对象id：</label>
				<input type="text" name="objid" size="10" value="<?php echo isset($filter['objid'])?$filter['objid']:'' ?>">
				<label class="ml20"  for="">作者：</label>
				<input type="text" name="username" size="10" value="<?php echo isset($filter['username'])?$filter['username']:'' ?>">
				<label class="ml20"  for="">内容：</label>
				<input type="text" name="message" size="10" value="<?php echo isset($filter['message'])?$filter['message']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="ml20 submitbtn"  type="button" value="提交">
				</form>
			</div>
			<hr>
			<table class="tablebox" width="90%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="20">id</th>
						<th width="100">作者</th>
						<th width="50">评分</th>
						<th>内容</th>
						<th width="65">创建时间</th>
						<th width="60">状态</th>
						<th width="100">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($items)) {
						foreach ($items['list'] as $id => $item) {
							echo '<tr><td>'.$id.'</td>
									<td>'.$item['username'].'</td>
									<td>'.$item['scroe'].'</td>
									<td>'.mb_substr($item['message'],0,100).'...</td>
									<td>'.date('Y-m-d H:i:s',$item['createdate']).'</td>
									<td>'.Module_Comment::$statuss[$item['status']].'</td>
									<td>
									<span data-url="'.Uri::build('manage','pcommedit').'?id='.$item['id'].'&appid='.$appid.'"  class="ajaxbtn">编辑</span>
									<span data-url="'.Uri::build('manage','pcommdel').'?id='.$item['id'].'&appid='.$appid.'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</span>
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