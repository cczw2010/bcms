<div class="tabbox">
	<?php
	if (!empty($errmsg)) {
		echo	'<div class="errmsg"><span class="xicon mr10">a</span>'.$errmsg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active">订单列表</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<div class="filter">
				<form action="/manage/order/lists/">
				<span class="xicon mr10">!</span>筛选：
				<label class="ml20" for="">状态：</label>
				<select name="status" id="">
					<option value="-1" >全部</option>
					<?php echo $options;?>
				</select>
				<label class="ml20"  for="">用户id：</label>
				<input type="text" name="userid" size="10" value="<?php echo isset($filter['userid'])?$filter['userid']:'' ?>">
				<label class="ml20"  for="">订单号：</label>
				<input type="text" name="orderno" size="10" value="<?php echo isset($filter['orderno'])?$filter['orderno']:'' ?>">
				<label class="ml20"  for="">创建时间：</label>
				<input type="text" id="createdate" name="createdate" size="10" value="<?php echo isset($_REQUEST['createdate'])?$_REQUEST['createdate']:'' ?>">
				<input type="hidden" name="filterform" value="1">
				<input class="ml20 submitbtn"  type="button" value="提交">
				</form>
			</div>
			<hr>
			<table class="tablebox" width="99%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="20">id</th>
						<th width="100">订单号</th>
						<th width="50">总价</th>
						<th width="100">支付方式</th>
						<th width="100">配送方式</th>
						<th width="">配送地址</th>
						<th width="60">订单状态</th>
						<th width="150">创建时间</th>
						<th width="80">操作</th>
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
									<span data-url="/manage/order/edit/'.$item['id'].'"  class="ajaxbtn">编辑</span>
									<span data-url="/manage/order/del/'.$item['id'].'" data-confirm="确认删除吗？该操作不可恢复!" class="ajaxbtn">删除</span>
									</td>';
						}
					}
					?>
					<tr><td colspan="9"><?=(isset($pages)?$pages:'');?></td></tr>
				</tbody>
			</table>			
		</div>
	</div>
</div>
<script>
	$('#createdate').Zebra_DatePicker();
</script>