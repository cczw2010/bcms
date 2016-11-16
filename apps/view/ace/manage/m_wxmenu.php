<style>
.wxsubmenu>td:first-child{
 text-indent: 20px;
}
</style>
<div class="row">
	<div class="alert alert-danger">
		<i class="icon-warning-sign"></i>请先确认config.php中已经增加微信配置.<br>
		微信菜单有缓存,设置完之后,用户可能要重新进入公众号,或者重新关注才会生效.另外为防止对线上造成影响,非专业人员请勿轻易操作.
	</div>
	<div class="alert alert-danger">
		1、自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。<br>
		2、一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。<br>
		3、创建自定义菜单后，菜单的刷新策略是，在用户进入公众号会话页或公众号profile页时，如果发现上一次拉取菜单的请求在5分钟以前，就会拉取一下菜单，如果菜单有更新，就会刷新客户端的菜单。测试时可以尝试取消关注公众账号后再次关注，则可以看到创建后的效果。<br>
		4、如果一级菜单下有二级菜单,则一级菜单的[类型]和[值]均无效!如果一级菜单下无二级菜单,则一级菜单的[类型]和[值]均不能为空!!
		5、更多详情请查阅<a target="_blank" href="http://mp.weixin.qq.com/wiki/10/0234e39a2025342c17a7d23595c6b40a.html">微信开发者文档</a>
	</div>
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" data-target="#wxmenulist">菜单列表</a></li>
		<li><a class="addtopmenu hide">新增一级菜单</a></li>
	</ul>
	<div class="tab-content">
		<div class="widget-container-span tab-pane active" id="wxmenulist">
			<form action="/manage/weixin/medit/">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th width="100">名称</th>
						<th width="50">类型</th>
						<th width="100">值</th>
						<th width="150">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($trs)){
						echo $trs;
					}?>
				</tbody>
			</table>
			<div>
				<input type="button" id="showedit" class="btn btn-info" value="编 辑">
				<input type="button" id="wxmenusubmit" name="submitbtn" class="submitbtn btn btn-info hide" value="提 交">
			</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('#showedit').on('click',function(){
		if (confirm('该操作将直接修改线上菜单!请慎重操作!')) {
			$('#wxmenulist tbody input:disabled').removeAttr('disabled');
			$(this).remove();
			$('#wxmenusubmit').removeClass('hide');
			$('.delwxmitem,.addwxsubmenu,.addtopmenu').removeClass('hide');
		}
	});
	$('#wxmenulist').on('click','.delwxmitem',function(e){
		e.preventDefault();
		var tr = $(this).parents('tr'),
			istopmenu = tr.hasClass('wxtopmenu'),
			msg = istopmenu?"您确定要删除一级菜单吗? 如果删除请同时删除其下的二级菜单!否则将报错!":"确定删除吗?删除以后再点击保存才会修改菜单";

		if(confirm(msg)){
			if(istopmenu){
				tr.nextUntil('.wxtopmenu').remove();
			}
			tr.remove();
		}
	});
	$('.addtopmenu').on('click',function(e){
		e.preventDefault();
		if($('#wxmenulist .wxtopmenu').length>=3){
			alert('一级菜单最多三个!');
			return false;
		}
		var html = '<tr class="wxtopmenu">'+
				'<td><input name="mname[]" value=""></td>'+
				'<td><input name="mtype[]" value=""></td>'+
				'<td><input name="mval[]" value=""></td>'+
				'<td><a class="delwxmitem">删除</a> <a class="addwxsubmenu">添加下级菜单</a><input class="hide" name="mdepth[]" value="0"></td></tr>';
		$('#wxmenulist tbody').append(html);
	});
	$('#wxmenulist').on('click','.addwxsubmenu',function(e){
		e.preventDefault();
		var html = '<tr class="wxsubmenu">'+
				'<td><input name="mname[]" value=""></td>'+
				'<td><input name="mtype[]" value=""></td>'+
				'<td><input name="mval[]" value=""></td>'+
				'<td><a class="delwxmitem">删除</a><input class="hide" name="mdepth[]" value="1"></td></tr>',
			obj = $(this).parents('tr'),
			siblings = obj.nextUntil('.wxtopmenu');
		if(siblings.length>=5){
			alert("请注意,二级菜单最多五个.负责会报错");
			return false;
		}
		var prevTr = siblings.last();
		if(prevTr.length==0){
			prevTr = obj;
		}
		prevTr.after(html);
	});
</script>