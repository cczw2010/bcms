<?php
	include('header.php');
?>
<div class="dynamicState">
	<ul class="stateNav clears">
		<li name="process" class="active">使用流程</li>
		<li name="company">我是公司</li>
		<li name="designer">我是设计师</li>
	</ul>
	<div name="flows" class="flows" id="process">
		<ul class="useFlow clears">
			<li class="item1">
				<img src="../../static/web/image/flow1.png" alt="">
				<h4>发布项目</h4>
				<p>填写设计项目的内容，提交审核</p>
			</li>
			<li class="item2">
				<img src="../../static/web/image/flow2.png" alt="">
				<h4>挑选设计师</h4>
				<p>审核通过后，项目会被推送给合适的设计师，感兴趣的设计师会 进行报价</p>
			</li>
			<li class="item3">
				<img src="../../static/web/image/flow3.png" alt="">
				<h4>确定合作</h4>
				<p>经过沟通后挑选一位设计师确定合作</p>
			</li>
		</ul>
		<ul class="useFlow clears">
			<li class="item4">
				<img src="../../static/web/image/flow4.png" alt="">
				<h4>协商并签订合同</h4>
				<p>滴滴设计提供规范化的双方合同模板</p>
			</li>
			<li class="item5">
				<img src="../../static/web/image/flow5.png" alt="">
				<h4>托管设计费</h4>
				<p>签订合同后，项目的全部的款项需托管至滴滴设计，设计师开始工作</p>
			</li>
			<li class="item6">
				<img src="../../static/web/image/flow6.png" alt="">
				<h4>按阶段确认作品</h4>
				<p>设计师按阶段提交作品，并与客户沟通确认，项目完成后，设计师提交源文件</p>
			</li>
			<li class="item7">
				<img src="../../static/web/image/flow7.png" alt="">
				<h4>项目完成！</h4>
				<p>大功告成！<br/>客户与设计师进行互评！</p>
			</li>
		</ul>
	</div>
	<ul name="flows" class="iAmDesigner" id="company" style="display:none">
		<li>
			<h3><i>+</i>怎样加入滴滴设计平台？</h3>
			<div class="designerDetail company">
				<p>首页最底端扫描二维码进入App Store（苹果应用商店）下载DD设计师版，或者直接进入App Store（苹果应用商店）搜索</p>
			</div>
		</li>
	</ul>
	<ul name="flows" class="iAmDesigner" id="designer" style="display:none">
		<li>
			<h3><i>+</i>怎样加入滴滴设计平台？</h3>
			<div class="designerDetail designer">
				<p>首页最底端扫描二维码进入App Store（苹果应用商店）下载DD设计师版，或者直接进入App Store（苹果应用商店）搜索</p>
			</div>
		</li>
	</ul>
</div>
<script type="text/javascript">
window.onload = function(){
	var type = 'process';
	$('.stateNav li').click(function(){
		type = $(this).attr('name');
		$('.stateNav li').removeClass('active');
		$(this).addClass('active');
		$('[name="flows"]').hide();
		$('#'+type).show();

		var clickFather = document.getElementById(type);
		var clickTitle = clickFather.getElementsByTagName('h3');
		var clickTag = clickFather.getElementsByTagName('i');
		var designerDetail = document.querySelectorAll('.'+type);
		for(var i=0;i<clickTitle.length;i++){
			clickTitle[i].index = i;
			clickTitle[i].onclick = function(){
				if(this.className == ''){
					this.className = 'active';
					clickTag[this.index].innerHTML = '-';
					designerDetail[this.index].style.display = 'block';
				}else{
					this.className = '';
					clickTag[this.index].innerHTML = '+';
					designerDetail[this.index].style.display = 'none';
				}
			}
		}
	});
}
</script>
<?php
	include('footer.php');
?>