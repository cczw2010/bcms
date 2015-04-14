<div class="tabbox mailmain">
	<ul class="boxs">
		<li class="tablabel active">发送邮件</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<form action="/manage/mail/send/">
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
		initTinymce("#mailbody",'/static/dist/css/common.min.css,/static/dist/css/main.min.css',true);
		addUpload('#addattach',{
			objtype:'mail',
			fileexts:'*.*',
			showoname:true,
			replacepic : '/static/dist/img/attach.jpg',
			uploadurl:"/manage/widget/upload"
			},function(dom,json){
				if (json.code>0) {
					$(dom).find('.uploadify_showname').append('<br>('+json.data.osize+'M)')
				}else{
					alert(json.msg);
				}
			});
	});
</script>