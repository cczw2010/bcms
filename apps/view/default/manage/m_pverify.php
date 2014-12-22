<div class="tabbox catemain">
	<div class="info"><span class="xicon mr10">R</span> tips:缓存系统自处理,敏感词分为过滤敏感词汇和禁止敏感词汇。</div>
	<?php
	if (!empty($msg)) {
		echo '<div class="info"><span class="xicon mr10">W</span>'.$msg.'</div>';
	}
	?>
	<ul class="boxs">
		<li class="tablabel active">敏感词配置</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<form action="<?php echo Uri::build('manage','pverify'); ?>">
			<table class="tablebox formtable" width="400" border="0" cellpadding="5" cellspacing="1" >
				<tbody>
					<tr>
						<td>禁止敏感词：</td>
						<td>
						<div>(如果出现以下敏感词将直接返回false,一般用户注册登陆验证,多个请用|分割，可用于表单基础信息检测（用户名，签名，昵称等）)</div>
						<textarea cols="120" rows="10" name="banned"><?php echo isset($oitem)?$oitem['banned']:''; ?></textarea></td>
					</tr>
					<tr>
						<td>过滤敏感词：</td>
						<td>
						<div>(如果出现以下敏感词将用*号来过滤，多个请用|分割。可用于文章，详情等内容类型替换。)</div>
						<textarea cols="120" rows="10" name="filters"><?php echo isset($oitem)?$oitem['filters']:''; ?></textarea></td>
					</tr>
					<tr>
						<td>状态：</td>
						<td>
						<div>(启用的话，敏感词模块自带的验证方法将调用这些敏感词，否则不调用)</div>
						启用<input class="mr20" type="radio" <?=(!isset($oitem) || $oitem['status']==1)?'checked="checked"':'';?> name="status" value="1" >
						不启用<input type="radio" <?=(isset($oitem) && $oitem['status']==0)?'checked="checked"':'';?> name="status" value="0" >
						</td>
					</tr>
					<tr>
						<td class="ccenter" colspan="2">
							<input type="hidden" name="id" value="<?php echo isset($oitem)?$oitem['id']:''; ?>">
							<input type="button"  name="submitbtn" class="submitbtn" value="提 交">
						</td>
					</tr>
				</tbody>
			</table>
			</form>		
		</div>
	</div>
</div>