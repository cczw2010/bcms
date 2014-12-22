<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8" />
		<title>SSMS后台管理系统 v1.0</title>
		<meta name="description" content="ssms 后台管理系统" />
		<meta name="email" content="71752352@qq.com" />
    <meta name="Author" content="awen" />
    <meta name="Version" content="1.0" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!-- 360极速浏览器 -->
    <meta name="renderer" content="webkit" />
		<link type="image/x-icon" rel="shortcut icon" href="/static/dist/img/favicon.ico">
		<!-- basic styles-->
		<link rel="stylesheet" href="/static/dist/css/common.min.css" />
		<link rel="stylesheet" href="/static/dist/css/manager.min.css?t=<?php echo time(); ?>" />
		<link rel="stylesheet" href="/static/dist/css/normalize.min.css" />
		<link rel="stylesheet" href="/datas/uploadify/uploadify.css">
		<link rel="stylesheet" href="/datas/datepicker/css/default.css">
		<link rel="stylesheet" href="/datas/zebra_dialog/css/flat/zebra_dialog.css">
		<!-- basic scripts -->
		<script src='/static/dist/js/jquery-1.8.3.min.js'></script>
		<script src="/static/dist/js/main.min.js"></script>
		<script src="/datas/tinymce/tinymce.min.js"></script>
		<script src="/datas/uploadify/jquery.uploadify.min.js"></script>
		<script src="/datas/datepicker/javascript/zebra_datepicker.min.js"></script>
		<script src="/datas/zebra_dialog/javascript/zebra_dialog.js"></script>
	</head>
	<body>
		<!-- 顶部 -->
		<div class="topbar posrel clearfix p10">
			<div class="shbox-l f20 ">
				 SSMS后台管理系统
			</div>
			<div class="shbox-flex cright">
				<?php 
				if ($user) {
					echo '欢迎您！'.$user['username'].' |
					 <span class="ajaxbtn dialogbtn"  data-url="'.Uri::build('manage','pueditinfo',array($user['id'])).'">修改个人信息</span> | 
					 <span class="ajaxbtn dialogbtn"  data-url="'.Uri::build('manage','purepass').'">修改密码</span> | <a href="'.Uri::build('user','logout').'" title="退出">退出</a>';
				}else{
					echo '未登录！';
				}
				?>
			</div>
		</div>
		<!-- 中部 -->
		<div class="clearfix">
			<div class="menu_tree shbox-l">
				<div class="menutopbar"><span class="xicon mr10 f18">|</span> 菜单导航</div>
				<ul class="f14">
				<!-- 主菜单树 -->
				<?php
					foreach ($menuTree as $mv) {
						echo ' <li class="foldermenu"><span>'.$mv['name'].'</span></li>';
						if (isset($mv['subs'])) {
							echo '<li class="submenubox">';
							foreach ($mv['subs'] as $subk => $subv) {
								echo '<div class="ajaxmenu" data-url="'.Uri::build('manage',$subk).'">'.$subv.'</div>';
							}
							echo '</li>';
						}
					}
				?>
				</ul>
			</div>
			<div class="shbox-flex">
				<!-- 面包屑导航部分 -->
				<div class="navbar clearfix">
					<div class="fleft pl20">
						<span class="xicon mr10">&#x69;</span><a href="<?php echo Uri::build('manage','index');?>">首页</a> > <span  id="curpage"></span>
					</div>
					<div id="loading" class="fleft pl20"><span class="xicon animate-spin mr10">1</span>loading....</div>
				</div>
				<!-- 内容展示区域-->
				<div  id="pageArea">
						<div class="info"><span class="xicon mr10">R</span> tips:为避免不必要的麻烦，请慎重操作。
						</div>
						<?php
							// dump($GLOBALS);
						?>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var dialog,_ajaxcall;

			$("#loading").ajaxStart(function(){
			  $(this).show();
			});
			$("#loading").ajaxComplete(function(){
			  $(this).hide();
			});
			// 一级菜单项点击
			$('.foldermenu').on('click',function(){
				$(this).next('.submenubox').slideToggle('fast');
			});
			// 动态菜单点击
			$('.ajaxmenu').on('click',function(){
				var o = $(this);purl = o.data('url')||'';
				purl = $.trim(purl);
				if (purl.length>0) {
					showPage(purl);
					// 显示面包屑
					$('#curpage').html(o.text());
					$('.menu_tree .ajaxmenu').removeClass('active');
					o.addClass('active');
				}
			});
			// 动态操作按钮点击，默认刷新data-flusharea设定的sel对应的区域,否则刷新pageArea
			$('.ajaxbtn,.jpagecan').live('click',function(){
				var data  = $(this).data(),
					url = data['url'],
					flusharea = data['flusharea'] || '#pageArea',
					isdialog = $(this).hasClass('dialogbtn');
				// 是否有确认框信息
				if (data.confirm) {
					if(!confirm(data.confirm)){
						return false;
					}
				}
				// 提交
				if (!isdialog) {
					$.get(url,function(html){
						clearJsObj(flusharea);
						$(flusharea).empty().html(html);
					});
				}else{
					dialog = new $.Zebra_Dialog('', {
								'type':false,
						    'source':  {'ajax': url},
						    // 'width': ,
						    'title': '',
						    'buttons':  false,
						    'onclose':function(){
						    	dialog = null;
						    }
						});
				}
			});
			//ajax post提交表单
			$('.submitbtn').live('click',function(){
				// 如果存在tinymce编辑器，ajax提交可能出现内容没有同步到textarea,所以手动同步一下
				if (tinymce) {
					tinymce.triggerSave();
				}
				var form = this.form,
					data = $(form).serialize();
					_ajaxcall =$(this).data('callback');
				console.log(_ajaxcall);
				// 如果有dialog
				if (dialog) {
					dialog.message.children().eq(0).html('<div class="ccenter">提交中，请等待...</div>');
				}
				$.post(form.action,data,function(_data){
					// 先判断json错误返回
					try{
						_data = $.parseJSON(_data);
						if (dialog) {
							dialog.message.children().eq(0).html('<div class="ccenter">'+_data.msg+'</div>');
						}else{
							alert(_data.msg);
						}
					}catch(e){
						clearJsObj('#pageArea');
						$('#pageArea').empty().html(_data);
						// 滚动页面到顶
						window.scrollTo(window.scrollX,0);
					}
					window[_ajaxcall]&&window[_ajaxcall].call(_data);
					_ajaxcall=null;
				});
			});
			// group折叠
			$('.foldbar').live('click',function(){
				$(this).parent('.group_title').siblings('.group_list').slideToggle('fast');
			});
			function showPage(purl){
				$.get(purl,function(data){
					clearJsObj('#pageArea');
					$('#pageArea').empty().html(data);
				});
			}
			
			// 通用构建tab切换标签列表
			function buildTab(selbox){
				$(selbox+' .tablabel').on('click',function(){
					var index = $(this).index();
					$(selbox+' .tabbody').eq(index).addClass('active').siblings('.tabbody').removeClass('active');		
					$(this).addClass('active').siblings('.tablabel').removeClass('active');
				});
			}
			// 关闭dialog
			function dialogclose(){
				if (dialog) {
					setTimeout(function(){
							dialog&&dialog.close();
							dialog = null;
						},1000);
				};
			}
			// 清除页面上的编辑器和日历
			function clearJsObj(wraper){
					//如果页面上有tinymce 在ajax切换页面时，很可能造成编辑器数据缓存，所以要先干掉避免内存溢出
					//而干掉时会还原缓存的数据到目的textarea上，将覆盖当前页面上textarea的值（如果id和编辑器id相同），
					//所以再生成页面代码前就干掉
					if (tinymce && tinymce.activeEditor) {
						tinymce.activeEditor.destroy();
					}
					// 循环遍历
					$(wraper+' input').each(function(index, el) {
						var datepicker = $(this).data('Zebra_DatePicker');
						if (datepicker) {
							datepicker.destroy();
						};
					});
			}
		</script>
	</body>
</html>