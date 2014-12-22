<div class="tabbox" id="brandmain">
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active">品牌列表</li>
		<li class="tablabel"><span class="xicon">g</span><span>新增</span></li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<div class="filter">
				<form action="<?php echo Uri::build('manage','pbrands'); ?>">
				<span class="xicon mr10">!</span>筛选：
				<label class="ml20" for="">状态：</label>
				<select name="status" id="">
					<option value="-1" >全部</option>
					<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>>发布</option>
					<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>>未发布</option>
				</select>
				<label class="ml20"  for="">品牌名称：</label>
				<input type="text" name="name" size="10" value="<?php echo isset($filter['name'])?$filter['name']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="ml20 submitbtn"  type="button" value="提交">
				</form>
			</div>
			<hr>
			<table class="tablebox" width="95%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="20">id</th>
						<th width="120">品牌名称</th>
						<th width="120">品牌英文</th>
						<th width="120">logo</th>
						<th width="120">官网</th>
						<th width="">简介</th>
						<th width="40">状态</th>
						<th width="100">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($items)) {
						foreach ($items['list'] as $id => $item) {
							echo '<tr><td>'.$id.'</td>
									<td>'.$item['name'].'</td>
									<td>'.$item['ename'].'</td>
									<td>'.(!empty($item['logo'])?'<img src="'.$item['logo'].'" width="80" >':'').'</td>
									<td>'.$item['site'].'</td>
									<td>'.mb_substr($item['desc'],0,50).'...</td>
									<td>'.Module_Article::$statuss[$item['status']].'</td>
									<td>
									<span data-url="'.Uri::build('manage','pbrandedit',array($item['id'])).'"  class="ajaxbtn">编辑</span>
									<span data-url="'.Uri::build('manage','pbranddel',array($item['id'])).'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</span>
									</td>';
						}
					}
					?>
					<tr><td colspan="19"><?=(isset($pages)?$pages:'');?></td></tr>
				</tbody>
			</table>			
		</div>
		<div class="tabbody">
			<form action="<?php echo Uri::build('manage','pbrandedit'); ?>">
			<table class="tablebox formtable" border="0" width="500" cellpadding="10" cellspacing="1" >
				<thead>
					<tr>
						<th colspan="2">编辑品牌</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>品牌名：</td>
						<td><input type="text" name="name" value="<?php echo isset($oitem)?$oitem['name']:''; ?>"></td>
					</tr>
						<tr>
						<td>英文名：</td>
						<td><input type="text" name="ename" value="<?php echo isset($oitem)?$oitem['ename']:''; ?>"></td>
					</tr>
					<tr>
						<td>品牌logo：</td>
						<td>
							<?php if (isset($oitem) && !empty($oitem['logo'])): ?>
								<img src="<?=$oitem['logo'];?>" alt="">
							<?php endif ?>
							<div id="brandlogo" class="clearfix"></div>
						</td>
					</tr>
					<tr>
						<td>官网地址：</td>
						<td><input type="text" name="site" value="<?php echo isset($oitem)?$oitem['site']:''; ?>"></td>
					</tr>
					<tr>
						<td>描述：</td>
						<td><input type="text" name="desc" value="<?php echo isset($oitem)?$oitem['desc']:''; ?>"></td>
					</tr>
					<tr>
						<td>状态:</td>
						<td>
							启用<input class="mr20" type="radio" <?=(!isset($oitem) || $oitem['status']==1)?'checked="checked"':'';?> name="status" value="1" >
							不启用<input type="radio" <?=(isset($oitem) && $oitem['status']==0)?'checked="checked"':'';?> name="status" value="0" >
						</td>
					</tr>
					<tr>
						<td colspan="2" class="ccenter">
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
	buildTab('#brandmain');
	// 上传
	addUpload('#brandlogo',{
				objtype:'brandlogo',
				hidenew:true,
				hidedel:true,
				uploadurl:"<?php echo Uri::build('widget','upload')?>"
			});
</script>