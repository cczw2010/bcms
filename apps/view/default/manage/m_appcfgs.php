<div class="tabbox groupmain">
	<ul class="boxs">
		<?php foreach ($items as $item) {
			echo '<li class="tablabel active"><span class="ajaxbtn" data-url="'.Uri::build('manage','pappcfgedit').'/?key='.$item['key'].'" data-flusharea=".tabbody">'.$item['name'].'</span></li>';
		}?>
		<li class="flex"></li>
	</ul>
	<div class="flex">
		<div class="tabbody active">
		</div>
	</div>
</div>
<script>
$(function(){
	$('.groupmain .ajaxbtn').eq(0).trigger('click');
});
</script>