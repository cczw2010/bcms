<div class="info">
	<span class="xicon mr10">R</span> tips:商品应尽量挂在最底层分类上
</div>
<form action="/manage/product/edit/">
<table class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" width="1000" >
	<thead>
		<tr>
			<th colspan="2"><?php echo isset($oitem)?'内容编辑':'新增内容'; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="80">所属分类*：</td>
			<td><select name="cateid"><option value="0">-无-</option><?=$options;?></select></td>
		</tr>
		<tr>
			<td width="80">所属品牌*：</td>
			<td><select name="brandid"><option value="0">-无-</option>
			<?= $brandoptions;?>
			</select></td>
		</tr>
		<tr>
			<td>标题*：</td>
			<td><input type="text" size="50" name="title" value="<?php echo isset($oitem)?$oitem['title']:''; ?>"></td>
		</tr>
		<tr>
			<td>副标题：</td>
			<td><input type="text" size="50" name="subtitle" value="<?php echo isset($oitem)?$oitem['subtitle']:''; ?>"></td>
		</tr>
		<tr>
			<td>成品图：</td>
			<td id="coverarea" class="clearfix"></td>
		</tr>
		<tr>
			<td>简介：</td>
			<td><textarea cols="60" rows="6" name="summary" ><?php echo isset($oitem)?$oitem['summary']:''; ?></textarea></td>
		</tr>
		<tr>
			<td>市场价*：</td>
			<td><input type="text" name="oprice" value="<?php echo isset($oitem)?$oitem['oprice']:''; ?>"></td>
		</tr>
		<tr>
			<td>售价*：</td>
			<td><input type="text" name="price" value="<?php echo isset($oitem)?$oitem['price']:''; ?>"></td>
		</tr>
		<tr>
			<td>库存*：</td>
			<td><input type="text" name="quantity" value="<?php echo isset($oitem)?$oitem['quantity']:''; ?>"></td>
		</tr>
		<tr>
			<td>单笔上限*：</td>
			<td>
			<div>（0代表不限制）</div>
			<input type="text" name="maxbuy" value="<?php echo isset($oitem)?$oitem['maxbuy']:0; ?>"></td>
		</tr>
		<tr>
			<td>详情*：</td>
			<td><textarea style="width:800px;height:400px;" id="productcontent" name="content" ><?php echo isset($oitem)?$oitem['content']:''; ?></textarea></td>
		</tr>
		<tr>
			<td>标签：</td>
			<td>
			<div>（可用于搜索，seo,多个标签请用英文,隔开）</div>
			<input type="text" size="40" name="tags" value="<?php echo isset($oitem)?$oitem['tags']:''; ?>">
			</td>
		</tr>
		<tr>
			<td>热推:</td>
			<td>
				是<input class="mr20" <?=(isset($oitem) && $oitem['ishot']==1)?'checked="checked"':'';?> type="radio"  name="ishot" value="1" >
				否<input type="radio" <?=(!isset($oitem) || $oitem['ishot']==0)?'checked="checked"':'';?>name="ishot" value="0" >
			</td>
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
				<input type="hidden" name="id" value="<?php echo isset($oitem)?$oitem['id']:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>
<script>
	$(function(){
		initTinymce("#productcontent",'/static/dist/css/common.min.css,/static/dist/css/main.min.css',true);
		// 上传
		var jsons = <?=isset($oitem)?json_encode($oitem['covers']):'[]';?>,
			objtype = "<?=Module_Product::ATTACHTYPE;?>",idx=0;
		for(var k in jsons){
			idx++;
			addUpload('#coverarea',{
				objtype:objtype,
				json:jsons[k],
				uploadurl:"/manage/widget/upload"
			});
		}
		if (idx==0) {
			addUpload('#coverarea',{
				objtype:objtype,
				fileexts:'*.*',
				uploadurl:"/manage/widget/upload"
			});
		}
	});
	
</script>