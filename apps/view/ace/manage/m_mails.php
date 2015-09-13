<form action="/manage/mail/cfg/">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th colspan="2"><h5>SMTP邮件配置(如果不配置则邮件功能无法使用)</h5></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="120">SMTP服务器：</td>
				<td>
				<div>(发件人邮件服务器,记得开启邮件服务器的smtp功能，否则验证会失败)</div>
				<input type="text" name="smtpservice" value="<?php echo isset($data)?$data['smtpservice']:''; ?>">
				</td>
			</tr>
			<tr>
				<td>SMTP端口：</td>
				<td>
				<div>(发件人邮件服务器端口)</div>
				<input type="text" name="port" value="<?php echo isset($data)?$data['port']:''; ?>">
				</td>
			</tr>
			<tr>
				<td>SMTP用户名：</td>
				<td>
				<div>(smtp需要发件人邮件全地址)</div>
				<input type="text" name="username" value="<?php echo isset($data)?$data['username']:''; ?>">
				</td>
			</tr>
			<tr>
				<td>SMTP密码：</td>
				<td>
				<div>(发件人邮件密码)</div>
				<input type="password" name="password" value="<?php echo isset($data)?$data['password']:''; ?>">
				</td>
			</tr>
			<tr>
				<td>发件人名称：</td>
				<td>
					<div>(发件人名称)</div>
					<input type="text" name="nickname" value="<?php echo isset($data)?$data['nickname']:''; ?>">
				</td>
			</tr>
			<tr>
				<td>签名(html)：</td>
				<td><textarea cols="100" rows="4" name="mark"><?php echo isset($data)?$data['mark']:''; ?></textarea></td>
			</tr>
			<tr>
				<td>语言：</td>
				<td>
				<select name="language" >
					<?php 
						$language = isset($data)?$data['language']:'';
						echo SForm::buildOptionsSimple(array('zh'=>'中文','en'=>'英文'),$language) ;
					?>
				</select>
				</td>
			</tr>
			<tr>
				<td class="text-center" colspan="2">
					<input type="hidden" name="id" value="<?php echo isset($data)?$data['id']:''; ?>">
					<input type="button"  name="submitbtn" class="submitbtn btn btn-info" value="提 交">
				</td>
			</tr>
		</tbody>
	</table>
</form>