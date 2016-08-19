<form action="/manage/article/number/">
<table class="table table-striped table-bordered table-hover dataTable">
	<thead>
		<tr>
			<th colspan="3">编辑首页数字</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>当前显示：</td>
			<td colspan="2">需求方数字：<?php echo isset($numbers)?$numbers['need_user_num']:''; ?>；设计师数字：<?php echo isset($numbers)?$numbers['designer_num']:''; ?>；修改时间：<?php echo isset($numbers)?date('Y-m-d H:i:s',$numbers['update_time']):''; ?></td>
		</tr>
		<tr>
			<td>需求方数字：</td>
			<td><input type="text" name="need" value="<?php echo isset($numbers)?$numbers['need_user_num']:''; ?>"></td>
			<td>请按展示格式填写，例如000099</td>
		</tr>
		<tr>
			<td>设计师数字：</td>
			<td><input type="text" name="design" value="<?php echo isset($numbers)?$numbers['designer_num']:''; ?>"></td>
			<td>请按展示格式填写，例如000099</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
				<input type="hidden" name="id" value="<?php echo isset($id)?$id:''; ?>">
				<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>
