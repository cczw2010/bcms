<div class="row">
	<div class="alert alert-info">tips:此处处理<?=$appname?>模块的所有附加属性! 未完成!!!!!!!</div>
	<?php
	if (!empty($errmsg)) {
		echo '<div class="error error-info">'.$errmsg.'</div>';
	}
	?>
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" data-target="#proplist">属性列表</a></li>
		<li class=""><a data-toggle="tab" data-target="#newprop">添加新属性</a></li>
	</ul>
	<div class="tab-content">
		<div id="proplist" class="widget-container-span tab-pane active">
			<table class="table table-striped table-bordered table-hover dataTable" width="80%" border="0" cellpadding="5" cellspacing="1" >
				<thead>
					<tr>
						<th width="50">id</th>
						<th width="100">名称</th>
						<th width="">值</th>
						<th width="">描述</th>
						<th width="50">状态</th>
						<th width="100">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($props as $id => $prop) {
							echo '<tr><td>'.$prop['id'].'</td>
										<td>'.$prop['name'].'</td>
										<td>'.$prop['vals'].'</td>
										<td>'.$prop['desc'].'</td>
										<td>'.Module_Prop::$statuss[$prop['status']].'</td>
										<td class="">
											<a href="/manage/product/propedit/'.$prop['id'].'" class="ajaxbtn">编辑</a>
											<a href="/manage/product/propdel/'.$prop['id'].'" data-confirm="确认删除吗？该操作不可恢复，并且其下所有子类敬意并删除！同时如果已有其他资源关联到本类，请慎重！" class="ajaxbtn">删除</a>
										</td></tr>';
						}
					?>
				</tbody>
			</table>
		</div>
		<div id="newprop" class="widget-container-span tab-pane">
			<form action="/manage/product/propedit/">
			<table class="table table-striped table-bordered table-hover dataTable" border="0" width="80%" cellpadding="10" cellspacing="1" >
				<thead>
					<tr>
						<th colspan="2">新增属性</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>名称：</td>
						<td><input type="text" name="name" value=""></td>
					</tr>
					<tr>
						<td>初始值：</td>
						<td>
							<div>(多个初始值用|分隔开)</div>
							<textarea name="vals" cols="30" rows="10"></textarea>
						</td>
					</tr>
					<tr>
						<td>描述：</td>
						<td><textarea name="desc" cols="30" rows="10"></textarea></td>
					</tr>
					<tr>
						<td>状态:</td>
						<td>
							启用<input class="mr20" type="radio" checked="true"  name="status" value="1" >
							不启用<input type="radio" name="status" value="0" >
						</td>
					</tr>
					<tr>
						<td colspan="2" class="ccenter">
							<input type="hidden" name="id" value="">
							<input type="hidden" name="appid" value="<?=$appid;?>">
							<input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交">
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
	</div>
</div>