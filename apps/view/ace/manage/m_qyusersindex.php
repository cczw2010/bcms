<div class="row">
	<!-- 左侧部门列表 -->
	<div class="col-md-3">
		<div class="panel panel-default">
		  <div class="panel-heading left">
		  	<input type="text" class="input-sm" placeholder="搜索部门" name="" value="">
			</div>
		  <div class="panel-body">
		    <div id="partmenttree" class="tree tree-unselectable"></div>
		  </div>
		</div>
	</div>
	<!-- 右侧员工列表 -->
	<div class="col-md-9">
		<div class="btn-group">
			<button class="btn btn-gray btn-sm">
				添加员工
				<i class="icon-plus align-top bigger-125 icon-on-right"></i>
			</button>
		</div>
		<div id="ustatus" class="btn-group" data-toggle="buttons">
			<?php foreach ($ustatus as $k => $v): ?>
				<label class="btn btn-sm btn-gray"><?php echo $v; ?>
					<input name="ustatus" type="radio" value="<?php echo $k; ?>">
				</label>
			<?php endforeach ?>
		</div>
		<div class="space-10"></div>
		<div id="partmentusers"></div>
	</div> 

	<div id="parteditlist" class="dropdown" style="position: fixed;">
		<ul class="dropdown-menu">
			<li><a href="#" tabindex="-1">添加子部门</a></li>
			<li class="divider"></li>
			<li><a href="#" tabindex="-1">重命名</a></li>
			<li><a href="#" tabindex="-1">删除</a></li>
		</ul>
	</div>
</div>

<script>
	var partmentid = 1;
	var partustatus = 0;
	// 用于fuelux tree的数据结构
	var DataSourceTree = function(options) {
		this.url = options.url;
	};
	DataSourceTree.prototype.data = function(options, callback) {
		var self = this;
		var $data = null;

		var param = null
		if(!("name" in options) && !("type" in options)){
			param = 0;//load the first level data
		}
		else if("type" in options && options.type == "folder") {
			if("additionalParameters" in options && "children" in options.additionalParameters)
				param = options.additionalParameters["id"]
		}
		
		if(param != null) {
			$.ajax({
				url: this.url,
				data: 'id='+param,
				type: 'get',
				dataType: 'json',
				success : function(response) {
					if(response.code == 0)
						callback({ data: response.data })
				},
				error: function(response) {
					// console.log(response);
				}
			})
		}
	};
	// 部门列表
	$('#partmenttree').ace_tree({
		dataSource: new DataSourceTree({url: '/manage/qyusers/partlists/'}) ,
		loadingHTML:'<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
		'open-icon' : 'icon-folder-open',
		'close-icon' : 'icon-folder-close',
		'selectable' : true,
		'selected-icon' : null,
		'unselected-icon' : null
		});
		
	// 部门树点击
	$('#partmenttree').on('mousedown','.tree-folder-header',function(e){
		e.preventDefault();
		var data = $(this).data();
		partmentid = data.additionalParameters.id;
		switch(e.which){
			case 3://右键
				$('#parteditlist').addClass('open').css({'left':e.clientX,'top':e.clientY});
			break;
			case 1://左键
			showPartUsers();
			break;
		}
	});
	// 自动隐藏下拉菜单
	$(document).on('click',function(){
		$('#parteditlist').removeClass('open');
	}).on('contextmenu',function(){
		return false;
	});
	
	// 用户状态radio
	$('#ustatus').on('click','label',function(){
		partustatus = $(this).find('input').val();
		showPartUsers();
	});
	// 显示部门下的人员
	function showPartUsers(){
		$.get('/manage/qyusers/ulists/?partid='+partmentid+'&status='+partustatus,function(h){
			$('#partmentusers').html(h);
		});
	}
	// 操作员工
	$('#partmentusers').on('click','.puseroper',function(e){
		e.preventDefault();
		var cuser = $('.chosepuser:checked').eq(0);
		if (cuser.length==0) {
			alert('请先选择要操作的员工');
		}else{
			var id = cuser.data('uid');
			console.log(id);
		}
	});
	showPartUsers();
</script>