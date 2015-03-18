<form action="/manage/setting/payedit/">
<table class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" width="1000" >
	<thead>
		<tr>
			<th colspan="2">支付配置(自动判断平台调用pc和wap支付接口)</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>标题*：</td>
			<td><input type="text" name="name" value="<?=$oitem['name']; ?>"></td>
		</tr>
		<?php if($oitem['key']=='alipay'){?>
			<tr>
				<td>合作者id*：</td>
				<td>
					<div>(以2088开头的16位纯数字)</div>
					<input required="required" type="text" name="appid" value="<?=$oitem['appid']; ?>">
				</td>
			</tr>
			<tr>
				<td>安全检验码*：</td>
				<td>
					<div>(以数字和字母组成的32位字符)</div>
					<input required="required" type="text" name="appkey" value="<?=$oitem['appkey']; ?>">
				</td>
			</tr>
			<tr>
				<td>商家账号*：</td>
				<td>
					<div>(商家的支付宝账号)</div>
					<input required="required" type="text" name="appaccount" value="<?=$oitem['appaccount']; ?>">
				</td>
			</tr>
			<tr>
				<td>异步回调地址*：</td>
				<td>
					<div>(支付宝异步后台通知支付状态的完整地址:notify_url,默认地址:http://xxx/pay/alipay_notify.php)</div>
					<input required="required" type="text" size="70" name="notifyurl" value="<?=$oitem['notifyurl']; ?>">
				</td>
			</tr>
			<tr>
				<td>同步通知地址*：</td>
				<td>
					<div>(支付成功后的通知完整地址：return_url,默认地址:http://xxx/pay/alipay_return.php)</div>
					<input required="required" type="text" size="70" name="returnurl" value="<?=$oitem['returnurl']; ?>">
				</td>
			</tr>
		<?php }?>
		<tr>
			<td>简介：</td>
			<td><textarea cols="60" rows="6" name="desc" ><?=$oitem['desc']; ?></textarea></td>
		</tr>
		<tr>
			<td>状态:</td>
			<td>
				启用<input class="mr20" type="radio" <?=$oitem['status']==1?'checked="checked"':'';?> name="status" value="1" >
				不启用<input type="radio" <?=$oitem['status']==0?'checked="checked"':'';?> name="status" value="0" >
			</td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?=$oitem['id'];?>">
				<input type="hidden" name="key" value="<?=$oitem['key'];?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>