<div class="row">
	<?php
	if (!empty($errmsg)) {
		echo '<div class="alert alert-info">'.$errmsg.'</div>';
	}
	?>
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" data-target="#productlist">商品列表</a></li>
		<li class="">
		<a class="ajaxbtn btn btn-success" href="/manage/product/edit" >新增</a>
		</li>
	</ul>
	<div class="tab-content">
		<div id="productlist" class="widget-container-span ui-sortable tab-pane active">
			<div class="widget-box transparent">
				<div class="widget-header header-color-blue3">
					<form action="/manage/product/lists/">
					<span class="xicon mr10">!</span>筛选：
					<label class="" for="">分类：</label>
					<select name="cateid" id="">
						<option value="0" >全部</option>
						<?=$options ;?>
					</select>
					<label class="" for="">品牌：</label>
					<select name="brandid" id="">
						<option value="0" >全部</option>
						<?= $brandoptions;?>
					</select>
					<label class="" for="">状态：</label>
					<select name="status" id="">
						<option value="-1" >全部</option>
						<?php echo SForm::buildOptionsSimple(Module_Product::$statuss,isset($filter['status'])?$filter['status']:'') ?>
					</select>
					<label class="" for="">推荐：</label>
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
					<input class="submitbtn btn btn-info btn-sm"  type="button" value="提交">
					</form>
				</div>
				<div class="widget-body">
					<table class="table table-striped table-bordered table-hover dataTable" width="100%" border="0" cellpadding="5" cellspacing="1" >
						<thead>
							<tr>
								<th width="50">id</th>
								<th width="">商品名称</th>
								<th width="40">浏览</th>
								<th width="40">评论</th>
								<th width="40">收藏</th>
								<th width="40">喜欢</th>
								<th width="30">热门</th>
								<th width="110">创建时间</th>
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
											<td>'.$item['viewnum'].'</td>
											<td>'.$item['comnum'].'</td>
											<td>'.$item['favnum'].'</td>
											<td>'.$item['likenum'].'</td>
											<td>'.$item['ishot'].'</td>
											<td>'.date('Y-m-d H:i',$item['createdate']).'</td>
											<td>'.Module_Product::$statuss[$item['status']].'</td>
											<td>
											<a href="/manage/product/comm/?objid='.$item['id'].'"  class="ajaxbtn">评论</a>
											<a href="/manage/product/edit/'.$item['id'].'"  class="ajaxbtn">编辑</a>
											<a href="/manage/product/del/'.$item['id'].'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</a>
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
	</div>
</div>