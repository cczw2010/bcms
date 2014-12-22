<div class="tabbox mailmain">
	<ul class="boxs">
		<li class="tablabel active">SMTP邮件配置</li>
		<li class="tablabel">发送邮件</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<form action="<?php echo Uri::build('manage','pmails') ;?>">
			<table class="tablebox formtable"  border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th colspan="2">邮件配置(如果不配置则邮件功能无法使用)</th>
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
								echo SForm::build_options_simple(array('zh'=>'中文','en'=>'英文'),$language) ;
							?>
						</select>
						</td>
					</tr>
					<tr>
						<td class="ccenter" colspan="2">
							<input type="hidden" name="id" value="<?php echo isset($data)?$data['id']:''; ?>">
							<input type="button"  name="submitbtn" class="submitbtn" value="提 交">
						</td>
					</tr>
				</tbody>
			</table>
			</form>		
		</div>
		<div class="tabbody">
			<form action="<?php echo Uri::build('manage','psendmail'); ?>">
			<table class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" width="1000" >
				<thead>
					<tr>
						<th colspan="2">发送邮件</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="80">收件人*</td>
						<td>
							<div>(收件人列表，多个使用;号隔开)</div>
							<textarea cols="100" rows="1" name="receiver"></textarea>
						</td>
					</tr>
					<tr>
						<td>标题*</td>
						<td><textarea cols="100" rows="1" name="subject"></textarea></td>
					</tr>
					<tr>
						<td>正文*：</td>
						<td>
							<div>(这里是可编辑的邮件正文)</div>
							<textarea style="width:600px;height:300px;" id="mailbody" name="body" ></textarea>
						</td>
					</tr>
					<tr>
						<td>附件:</td>
						<td id="addattach">
							
						</td>
					</tr>
					<tr>
						<td colspan="2" class="ccenter">
							<input type="button" name="submitbtn" class="submitbtn" value="提 交">
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
	</div>
</div>
<script>
	$(function(){
		buildTab('.mailmain');
		initTinymce("#mailbody",'/static/dist/css/common.min.css,/static/dist/css/main.min.css');
		addUpload('#addattach',{
			objtype:'mail',
			fileexts:'*.*',
			showoname:true,
			replacepic : '/static/dist/img/attach.jpg',
			uploadurl:"<?php echo Uri::build('widget','upload')?>"
			},function(dom,json){
				if (json.code>0) {
					$(dom).find('.uploadify_showname').append('<br>('+json.data.osize+'M)')
				}else{
					alert(json.msg);
				}
			});
	});
</script>