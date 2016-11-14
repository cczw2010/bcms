<div class="row">
	<ul class="nav nav-tabs">
		<?php foreach ($items as $item) {
			echo '<li ><a data-toggle="tab" data-target=".tab-pane.active" class="ajaxbtn" href="/manage/setting/thirdloginedit/?key='.$item['key'].'" data-flusharea=".tab-pane.active">'.$item['name'].'</a></li>';
		}?>
	</ul>
	<div class="tab-content">
		<div class="widget-container-span ui-sortable tab-pane active">
			<div class="alert alert-info">选择标签查看或编辑对应的设置</div>
		</div>
	</div>
</div>
