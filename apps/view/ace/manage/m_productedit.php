<style>
	.isspecs #mulispec{
		display:block;
	}
	.isspecs #singlespec{
		display:none;
	}
	#mulispec li{
		float:left;
		margin-left:5px;
		padding:6px;
	    border:1px solid #ccc;
	    border-radius: 5px;
	    line-height: 30px;
	}
	li#addspec{
		padding:50px 20px;
	}
</style>
<div class="alert alert-info">
	tips:商品应尽量挂在最底层分类上, 逻辑未写完,啥时候用到再说
</div>
<form action="/manage/product/edit/">
<table class="table table-striped table-bordered table-hover dataTable" border="0" cellpadding="10" cellspacing="1" width="1000" >
	<thead>
		<tr>
			<th colspan="2"><?php echo isset($oitem)?'内容编辑':'新增内容'; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>所属分类*：</td>
			<td><select name="cateid"><option value="0">-无-</option><?=$options;?></select></td>
		</tr>
		<tr>
			<td>所属品牌*：</td>
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
			<td>库存：</td>
			<td id="skuboxwrap" class="<?=(isset($oitem) && $oitem['isskus']==1)?'isskus':'';?> ">
			<div>
				是否多库存:<input id="checkskus" class="mr20" <?=(isset($oitem) && $oitem['isskus']==1)?'checked="checked"':'';?> type="checkbox"  name="isskus" value="1" >
			</div>
			<hr>
			<div id="singlespec">
				<div>
					市价*：<input required="true" type="text" size="10" name="oprice" value="<?=oprice?>">
				</div>
				<div>
					售价*：<input required="true" type="text" size="10" name="price" value="<?=price?>">
				</div>
				<div>
					库存*：<input required="true" type="text" size="10" name="quantity" value="<?=quantity?>">
				</div>
			</div>
			<ul id="mulispec" class="clearfix hidden">
				<li id="addspec" class="ccenter">
					<input type="button" value="+新增" />
				</li>
			</ul>
			</td>
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
				<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>
<script type="text/html" id="specitem_tmpl">
	<li>
		<div>
			名称*：<input class="specname" type="text" size="10" name="specname[]" value="<%=specname%>">
		</div>
		<div>
			市价*：<input required="true" type="text" size="10" name="oprice[]" value="<%=oprice%>">
		</div>
		<div>
			售价*：<input required="true" type="text" size="10" name="price[]" value="<%=price%>">
		</div>
		<div>
			库存*：<input required="true" type="text" size="10" name="quantity[]" value="<%=quantity%>">
		</div>
		<div>
			上架*:<input class="mr20" type="checkbox" name="status[]" value="1" <%=((1==status)?checked="checked":"")%>>
			<input type="button" name="" value="delete" class="delspec ml20 <%=id>0?'hidden':''%>">
		</div>
		<input class="specid" type="hidden" size="10" name="ids[]" value="<%=id%>">
	</li>
</script>
<script>
	$(function(){
		initTinymce("#productcontent",false,true);
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
		// 空数据模板
		var datatpl = {specname:'',id:'',oprice:'',price:'',quantity:'',status:1};
		//事件绑定
		$('#checkskus').on('click',function(){
			if (this.checked) {
				$('#skuboxwrap').addClass('isspecs');
				$('#singlespec').find('input').attr('disabled',true);
				$('#mulispec').find('input').attr('disabled',false);
			}else{
				$('#skuboxwrap').removeClass('isspecs');
				$('#singlespec').find('input').attr('disabled',false);
				$('#mulispec').find('input').attr('disabled',true);
			}
		});
		// 添加新规格项
		$('#addspec').on('click',function(){
			var html = tmpl('specitem_tmpl',datatpl);
			$(html).insertBefore(this);
		});
		// 删除规则项
		$('#mulispec').on('click','.delspec',function(){
			var specitem = $(this).parents('li'),
				specid = specitem.find('.specid').val();
			if (specid.length>0) {
				alert('该规格是已有库存，删除可能会影响库存统计。请使用下架操作。');
				return;
			}
			specitem.remove();
		});
	});
</script>