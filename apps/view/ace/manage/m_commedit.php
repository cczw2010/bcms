<form action="/manage/comment/edit/">
<table class="table table-striped table-bordered table-hover dataTable">
	<thead>
		<tr>
			<th colspan="2"><?php echo isset($oitem)?'内容编辑':'新增内容'; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>评分：</td>
			<td>
				<input name="score"  value=" <?=isset($oitem)?$oitem['score']:0;?>" >
			</td>
		</tr>
		<tr>
			<td>内容*：</td>
			<td>
			<textarea name="message" id="" cols="30" rows="10"><?php echo isset($oitem)?$oitem['message']:''; ?></textarea>
		</tr>
		<tr>
			<td>状态:</td>
			<td>
				正常<input type="radio" <?=(!isset($oitem) || $oitem['status']==1)?'checked="checked"':'';?> name="status" value="1" >
				屏蔽<input type="radio" <?=(isset($oitem) && $oitem['status']==0)?'checked="checked"':'';?> name="status" value="0" >
			</td>
		</tr>
		<tr>
			<td colspan="2" class="text-center">
				<input type="hidden" name="id" value="<?php echo isset($oitem)?$oitem['id']:''; ?>">
				<input type="hidden" name="appid" value="<?php echo isset($oitem)?$oitem['appid']:''; ?>">
				<input type="hidden" name="userid" value="<?php echo isset($oitem)?$oitem['userid']:''; ?>">
				<input type="hidden" name="objid" value="<?php echo isset($oitem)?$oitem['objid']:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn btn btn-info btn-sm" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>