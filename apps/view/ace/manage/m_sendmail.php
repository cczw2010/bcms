<form action="/manage/mail/send/">
<table class="table table-striped table-bordered table-hover">
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
			<td colspan="2" class="text-center">
				<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>
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