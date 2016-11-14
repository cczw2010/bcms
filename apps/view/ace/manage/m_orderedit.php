<form action="/manage/order/edit/">
<table class="table table-striped table-bordered table-hover dataTable">
	<thead>
		<tr>
			<th colspan="2">编辑订单</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="80">订单状态：</td>
			<td>
				<select disabled="disabled"><option value="-1">-无-</option><?=$options;?></select>
				<input type="hidden" name="status" value="<?php echo $oitem['status']; ?>" >
			</td>
		</tr>
		<tr>
			<td>订单号：</td>
			<td><input type="text" disabled="disabled" name="orderno" value="<?php echo $oitem['orderno']; ?>"></td>
		</tr>
		<tr>
			<td>订单金额：</td>
			<td>
				<input type="text" disabled="disabled" value="<?php echo $oitem['totalfee']; ?>">
				<input type="hidden" name="totalfee" value="<?php echo $oitem['totalfee']; ?>">
			</td>
		</tr>
		<tr>
			<td>支付方式：</td>
			<td>
				<?php echo $oitem['payment'];?>
				<input type="hidden" name="paymentid" value="<?php echo $oitem['paymentid']; ?>">
			</td>
		</tr>
		<tr>
			<td>配送方式：</td>
			<td>
				<?php echo $oitem['shipping'];?>
				<input type="hidden" name="shippingid" value="<?php echo $oitem['shippingid']; ?>">
			</td>
		</tr>
		<tr>
			<td>配送地址：</td>
			<td>
				<?php echo $oitem['address'];?>
				<input type="hidden" name="addressid" value="<?php echo $oitem['addressid']; ?>">
			</td>
		</tr>
		<tr>
			<td>快递单号：</td>
			<td>
				<div>(增加快递单号，订单状态自动更新为已发货)</div>
				<input name="shippingno" value="<?php echo $oitem['shippingno'];?>" >
				<input type="hidden" name="oldshippingno" value="<?php echo $oitem['shippingno'];?>">
			</td>
		</tr>
		<tr>
			<td>备注：</td>
			<td><?php echo $oitem['tips'];?></td>
		</tr>
		<tr>
			<td>附加信息：</td>
			<td>
				<div>（该信息是管理员关于该订单增加的附加信息，可在前端展示也可不显示）</div>
				<textarea style="width:600px;height:300px;" id="ordercontent" name="descr" ><?php echo $oitem['descr']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?php echo $oitem['id']; ?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>
<script>
	$(function(){
		initTinymce("#ordercontent",'/static/dist/css/common.min.css,/static/dist/css/main.min.css',true);
	});
	
</script>