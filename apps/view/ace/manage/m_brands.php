<div class="row" id="brandmain">
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="alert alert-info">'.$errmsg.'</div>';
	}
	?>
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" data-target="#branchlist">品牌列表</a></li>
		<li class=""><a data-toggle="tab"  data-target="#newbranch" >新增</a></li>
	</ul>
	<div class="tab-content">
		<div id="branchlist" class="widget-container-span ui-sortable tab-pane active">
			<div class="widget-box transparent">
				<div class="widget-header header-color-blue3">
					<form action="/manage/product/brands/">
					<label class="" for="">状态：</label>
					<select name="status" id="">
						<option value="-1" >全部</option>
						<option value="1" <?php if(isset($filter['status'])&&($filter['status']==1)){echo 'selected';} ?>><?php echo Module_Brand::$statuss[1] ?></option>
						<option value="0" <?php if(isset($filter['status'])&&($filter['status']==0)){echo 'selected';} ?>><?php echo Module_Brand::$statuss[0] ?></option>
					</select>
					<label class=""  for="">品牌名称：</label>
					<input type="text" name="name" size="10" value="<?php echo isset($filter['name'])?$filter['name']:'' ?>">
					<input type="hidden" name="filterform" value="1">
					<input class="submitbtn btn btn-info btn-sm"  type="button" value="提交">
					</form>
				</div>
				<div class="widget-body">
					<table class="table table-striped table-bordered table-hover dataTable" width="95%" border="0" cellpadding="5" cellspacing="1" >
						<thead>
							<tr>
								<th width="20">id</th>
								<th width="120">品牌名称</th>
								<th width="120">品牌英文</th>
								<th width="120">logo</th>
								<th width="120">官网</th>
								<th width="">简介</th>
								<th width="80">状态</th>
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
											<td>'.Module_Brand::$statuss[$item['status']].'</td>
											<td>
											<a href="/manage/product/brandedit/'.$item['id'].'"  class="ajaxbtn">编辑</a href>
											<a href="/manage/product/branddel/'.$item['id'].'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</a href>
											</td>';
								}
							}
							?>
							<tr><td colspan="19"><?=(isset($pages)?$pages:'');?></td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div id="newbranch" class="widget-container-span ui-sortable tab-pane">
			<div class="widget-body">
				<form action="/manage/product/brandedit/">
				<table class="table table-striped table-bordered table-hover dataTable" border="0" width="500" cellpadding="10" cellspacing="1" >
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
								<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
							</td>
						</tr>
					</tbody>
				</table>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	// 上传
	addUpload('#brandlogo',{
				objtype:'brandlogo',
				hidenew:true,
				hidedel:true,
				uploadurl:"/manage/widget/upload"
			});
</script>