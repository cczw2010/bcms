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
				启用<input class="" type="radio" <?=(!isset($oitem) || $oitem['status']==1)?'checked="checked"':'';?> name="status" value="1" >
				不启用<input type="radio" <?=(isset($oitem) && $oitem['status']==0)?'checked="checked"':'';?> name="status" value="0" >
			</td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?php echo isset($oitem)?$oitem['id']:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>
<script>
	// 上传
	addUpload('#brandlogo',{
				objtype:'brandlogo',
				hidenew:true,
				hidedel:true,
				uploadurl:"/manage/widget/upload"
			});
</script>