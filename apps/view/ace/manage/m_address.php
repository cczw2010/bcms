<div class="row">
	<?php if (!empty($errmsg)): ?>
	<div class="alert alert-info">
		<i class="icon-warning-sign"></i>
		地址信息只能浏览，不能修改,如需修改请联系技术人员
	</div>
	<?php endif ?>

	<div class="col-12 widget-container-span ui-sortable">
		<table class="table table-striped table-bordered table-hover dataTable">
			<thead>
				<tr>
					<th width="50">id</th>
					<th width="100">用户id</th>
					<th width="100">收货人</th>
					<th width="">地址</th>
					<th width="">手机号</th>
					<th width="">固话</th>
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