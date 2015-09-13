		<!-- basic scripts -->
		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='/datas/ace1.2/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="/datas/ace1.2/js/bootstrap.min.js"></script>
		<script src="/datas/ace1.2/js/typeahead-bs2.min.js"></script>

		<!-- page specific plugin scripts -->
		<!--[if lte IE 8]>
		  <script src="/datas/ace1.2/js/excanvas.min.js"></script>
		<![endif]-->
		<script src="/datas/ace1.2/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/datas/ace1.2/js/jquery.ui.touch-punch.min.js"></script>
		<script src="/datas/ace1.2/js/jquery.slimscroll.min.js"></script>
		<script src="/datas/ace1.2/js/jquery.sparkline.min.js"></script>
		<script src="/datas/ace1.2/js/flot/jquery.flot.min.js"></script>
		<script src="/datas/ace1.2/js/flot/jquery.flot.pie.min.js"></script>
		<script src="/datas/ace1.2/js/flot/jquery.flot.resize.min.js"></script>
		<script src="/datas/ace1.2/js/date-time/bootstrap-datepicker.min.js"></script>
		<script src="/datas/ace1.2/js/date-time/bootstrap-timepicker.min.js"></script>
		
		<script src="/datas/ace1.2/js/fuelux/fuelux.tree.min.js"></script>
		<!-- ace scripts -->
		<script src="/datas/ace1.2/js/ace-elements.min.js"></script>
		<script src="/datas/ace1.2/js/ace.min.js"></script>
		<script>
		 (function($){
		 	  //导航
		 		$('body').on('click','#sidebar li',function(e){
        	var o = $(this);
        	if (o.find('.submenu').length==0) {
        		$('#sidebar li').removeClass('active');
          	o.addClass('active');
          	$('#currentcrumb').html(o.text());
        	}
		 		});
		 		// ajax按钮
		 		$('body').on('click','.ajaxbtn',function(e){
					e.preventDefault();
					var that = this;
					var url = $(that).attr('href');
					var confirmtxt = that.dataset.confirm;
					if (!confirmtxt || confirm(confirmtxt)) {
						$.get(url).done(function(h){
								ajaxCallBack(h,that);
							});
					}
					return false;
        });
        // ajax表单
        $('body').on('click','.submitbtn',function(e){
					e.preventDefault();
					e.stopPropagation();
					var that = this;
					var form = that.form;
					var data = $(form).serialize();
					var confirmtxt = that.dataset.confirm;
					if (!confirmtxt || confirm(confirmtxt)) {
						$.post(form.action,data).done(function(h){
							ajaxCallBack(h,that);
						});
					}
					return false;
        });
        // 自定义分页 ace-page
        $('body').on('click','.ace-page [data-page]',function(){
        	var that = this;
        	var pageObj = $(that).parents('.ace-page');
        	var pageno =that.dataset.page; //用$.data 与dataset兼容性并不好
        	var maxpage = pageObj.data('maxpage');
        	var path = pageObj.data('url');
        	var query = pageObj.data('query');
        	var isajax = pageObj.data('isajax');
        	var url = path+pageno+'?'+query;
        	if (pageno<=maxpage && pageno>=1) {
        		if (isajax) {
		 					$.get(url)
		 						.done(function(h){
			 						ajaxCallBack(h,that);
			 					});;
	        	}else{
	        		location.href= url;
	        	}
        	}
        });
		 })(jQuery);

		 /**
		  * ajax回调处理
		  * @param h ajax返回的字符串
		  * @param o 当前事件来源dom
		  */
		 function ajaxCallBack(h,o){
		 	var json;
		 	try{
				json = JSON.parse(h);
		 	}catch(e){}
		 	if (json) {
	 			var msg = json.code==1?json.msg||'操作成功':json.msg||'操作失败';
	 			// 显示消息 先寻找页面上是否有 #ajaxmsg 如果有就显示,没有就alert
	 			var o = $('#ajaxmsg');
			 	if (o.length>0) {
			 		o.html(msg).show();
			 	}else{
			 		alert(msg);
			 	}
	 		}else{
	 			// 如果没有data-flusharea 则刷新主区域
	 			var flusharea = o.dataset.flusharea||'#pageArea';
	 			clearJsObj(flusharea);
	 			$(flusharea).html(h);
	 		}	
		 }
		 // function showDialog(){
		 // 	$('#exampleModal').on('show.bs.modal', function (event) {
			//   var button = $(event.relatedTarget) // Button that triggered the modal
			//   var recipient = button.data('whatever') // Extract info from data-* attributes
			//   // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
			//   // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
			//   var modal = $(this)
			//   modal.find('.modal-title').text('New message to ' + recipient)
			//   modal.find('.modal-body input').val(recipient)
			// });
		 // }
		 // 清除页面上的编辑器和日历
			function clearJsObj(wraper){
				//如果页面上有tinymce 在ajax切换页面时，很可能造成编辑器数据缓存，所以要先干掉避免内存溢出
				//而干掉时会还原缓存的数据到目的textarea上，将覆盖当前页面上textarea的值（如果id和编辑器id相同），
				//所以再生成页面代码前就干掉
				if (tinymce && tinymce.activeEditor) {
					tinymce.activeEditor.destroy();
				}
				// 循环遍历
				wraper = wraper||'';
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