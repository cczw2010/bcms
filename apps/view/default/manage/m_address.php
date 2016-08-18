<div class="tabbox groupmain">
	<div class="info">
		<span class="xicon mr10">R</span> tips:地址信息只能浏览，不能修改,如需修改请联系技术人员
	</div>
	<ul class="boxs">
		<li class="tablabel active">地址列表</li>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
			<table class="tablebox" width="850" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="50">id</th>
						<th width="50">用户id</th>
						<th width="80">收货人</th>
						<th width="">地址</th>
						<th width="100">手机号</th>
						<th width="100">固话</th>
						<th width="50">默认</th>
						<th width="100">创建时间</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($items['list'] as $id => $item) {
							echo '<tr><td>'.$item['id'].'</td>
										<td>'.$item['userid'].'</td>
										<td>'.$item['name'].'</td>
										<td>'.$item['province'].$item['city'].$item['area'].$item['detail'].'</td>
										<td>'.$item['mobile'].'</td>
										<td>'.$item['phone'].'</td>
										<td>'.($item['default']==1?'是':'否').'</td></tr>
										<td>'.date('Y-m-d H:i:s',$item['createdate']).'</td></tr>';
						}
					?>
				</tbody>
			</table>			
		</div>
	</div>
</div>