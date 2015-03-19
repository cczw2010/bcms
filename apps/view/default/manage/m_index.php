<?php
	include('m_header.php');
?>
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
								echo '<div class="ajaxmenu" data-url="'.Uri::build('manage/'.$mv['con'],$subk).'">'.$subv.'</div>';
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
						<span class="xicon mr10">&#x69;</span><a href="<?php echo Uri::build('manage/home','index');?>">首页</a> > <span  id="curpage"></span>
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
					dialog&&dialog.close();
					dialog = null;
				}
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