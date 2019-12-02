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
		// 上传
		var setting = {
				queueID: 'addattach',
				fileObjName: 'file',
				// uploadLimit:10,
				queueSizeLimit: 0,
				formData: {
					'objtype': 'mail',
				},
				uploadScript: '/manage/widget/upload/'
			},
			params={
				callback:function (error, file, data) {
					if (error) {
						alert(error)
						return false;
					}
					console.log(file, data);
				},
				// btnWraper:'#btnwrap', 
				canSort:true,
			};
		// console.log(jsons);
		var supload = new SUplodiFive(setting,params);
	});
</script>