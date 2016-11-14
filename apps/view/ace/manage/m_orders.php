<div class="row">
	<div class="alert alert-info">

	<?php if (!empty($errmsg)): ?>
		<i class="icon-warning-sign"></i>
		<?php echo $errmsg; ?>
		<button type="button" class="close" data-dismiss="alert">
			<i class="icon-remove"></i>
		</button>
	<?php endif ?>
	</div>
	<div class="widget-box transparent">
		<div class="widget-header header-color-blue3">
			<form action="/manage/order/lists/">
			<label class="ml20" for="">状态：</label>
			<select name="status" id="">
				<option value="-1" >全部</option>
				<?php echo $options;?>
			</select>
			<label class=""  for="">用户id：</label>
			<input type="text" name="userid" size="10" value="<?php echo isset($filter['userid'])?$filter['userid']:'' ?>">
			<label class=""  for="">订单号：</label>
			<input type="text" name="orderno" size="10" value="<?php echo isset($filter['orderno'])?$filter['orderno']:'' ?>">
			<label class="ml20"  for="">创建时间：</label>
			<input type="text" id="createdate" name="createdate" size="10" value="<?php echo isset($filter['createdate'])?$filter['createdate']:'' ?>">
			<input type="hidden" name="filterform" value="1">
			<input class="submitbtn btn btn-info btn-sm"  type="button" value="提 交">
			</form>
		</div>
		<div class="widget-body">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th width="50">id</th>
						<th width="">订单号</th>
						<th width="">总价</th>
						<th width="">支付方式</th>
						<th width="">配送方式</th>
						<th width="">配送地址</th>
						<th width="">订单状态</th>
						<th width="">创建时间</th>
						<th width="">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($items)) {
						foreach ($items['list'] as $id => $item) {
							echo '<tr><td>'.$id.'</td>
									<td>'.$item['orderno'].'</td>
									<td>'.$item['totalfee'].'</td>
									<td>'.$item['payment'].'</td>
									<td>'.$item['shipping'].'</td>
									<td>'.$item['address'].'</td>
									<td>'.Module_Order::$statuss[$item['status']].'</td>
									<td>'.date('Y-m-d H:i:s',$item['createdate']).'</td>
									<td>
									<a href="/manage/order/edit/'.$item['id'].'"  class="ajaxbtn">编辑</a>
									</td>';
						}
					}
					?>
					<tr><td colspan="12"><?=(isset($pages)?$pages:'');?></td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>