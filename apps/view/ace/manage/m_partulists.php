<div class="row">
	<div class="text-warning orange">
		<span id="ajaxmsg">
			<?php if ($errcode!=0){
				echo $errmsg;
			}?>
		</span>
	</div>
	<div class="col-12">
		<div>
			<a class="puseroper" data-op="m" href="/manage/qyusers/uedit/"><i class="icon-key"></i>设置为管理</a> | 
			<a class="puseroper" data-op="edit" href="/manage/qyusers/uedit/"><i class="icon-pencil"></i>编辑</a> | 
			<a class="puseroper" data-op="lock" href="/manage/qyusers/lock/"><i class="icon-lock"></i>禁用</a> | 
			<a class="puseroper" data-op="unlock" href="/manage/qyusers/unlock/"><i class="icon-lock"></i>启用</a> | 
			<a class="puseroper" data-op="del" href="/manage/qyusers/udel/"><i class="icon-trash"></i>删除</a>
		</div>
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th></th>
					<?php
					if (isset($extattrs)) {
						foreach ($extattrs as $v) {
							echo '<th>'.$v.'</th>';
						}
					}
					?>
					<th>姓名</th>
					<th class="hidden-480">职位</th>
					<th class="hidden-480">微信号(非微信名)</th>
					<th>手机</th>
					<th class="hidden-480">邮箱</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($userlist as $user) {
						echo '<td><input type="radio" data-uid="'.$user['userid'].'" class="chosepuser" name="choseuser"></td>';
						if (isset($extattrs)) {
							foreach ($extattrs as $v) {
								echo '<td>'.getUserExtAttrVal($v,$user).'</td>';
							}
						}
						echo '<td>'.$user['name'].'</td>
									<td>'.(!empty($user['position'])?$user['position']:'').'</td>
									<td>'.$user['weixinid'].'</td>
									<td>'.$user['mobile'].'</td>
									<td>'.$user['email'].'</td></tr>';
					}
				?>
			</tbody>
		</table>
	</div>
</div>
