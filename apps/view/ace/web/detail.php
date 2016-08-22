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
			<h3><i>+</i>如何使用滴滴设计？</h3>
			<div class="designerDetail company">
				<p>1、首先您需要按照要求如实填写相关信息注册滴滴设计</p>
				<p>2、登陆后进入主页发布您项目</p>
				<p>3、项目审核通过后，我们会为您筛选多个最为匹配和优质的设计师，并将您的需求推送给他们</p>
				<p>4、项目发布成功后，您就可以等着设计师申请</p>
				<p>5、您可以从申请的设计师中，查看其相关背景资料以及设计等级，再进行综合比选，选择雇佣最合适的设计师为您服务</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>可以发布哪些项目？</h3>
			<div class="designerDetail company">
				<p>目前已开通建筑方案、建筑施工图、规划设计、景观方案、景观施工图、结构设计、给排水设计、电气设计、暖通设计、效果图设计、手绘设计、动画设计</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>怎样发布项目？</h3>
			<div class="designerDetail company">
				<p>您可以点击登录滴滴设计（公司版）后的主页“发布需求”选项，按照要求选定发布项目的类型，选择备选接单设计师所在地，填写具体的项目情况和要求，同时您也可以上传项目相关图片，然后填写为此项目发布的设计金额，点击确定提交需求，最后需要您点击“去付款”完成支付本次任务发布的金额</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>发布项目需要收费吗？</h3>
			<div class="designerDetail company">
				<p>滴滴设计对于目前在平台发布项目的公司采取免费策略，一般您通过在平台发布项目审核通过后24小时内，将会收到感兴趣设计师的申请。如果你未与设计师确认合作且收到的申请少于3人，请与滴滴设计客服联系，我们会尽快安排退款</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我可以要求退款吗？</h3>
			<div class="designerDetail company">
				<p>如果您的项目属于以下三种情况中的一种，您可以联系滴滴设计客服申请退款</p>
				<p>1、项目还在审核中的</p>
				<p>2、项目审核未通过的</p>
				<p>3、项目发布超过7天无设计师申请的</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>项目审核需要多久？</h3>
			<div class="designerDetail company">
				<p>由于提交的项目较多，我们尽量会在一个工作日内审核完成，请耐心等待</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>为什么我的项目审核未通过？</h3>
			<div class="designerDetail company">
				<p>您可以通过站内信查看审核未通过理由，项目将会自动撤销，请您按照要求重新发布项目</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我需要上传什么样的项目描述？</h3>
			<div class="designerDetail company">
				<p>您可以上传你喜欢的图片、参考案例、项目背景资料等，帮助设计师更好地理解您的倾向。我们不建议您上传产品核心内容、涉及到商业机密的资料，在收到设计师的申请后，您可以通过APP端在线聊天工具与设计师进一步沟通细节</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我可以追加修改项目内容描述吗？</h3>
			<div class="designerDetail company">
				<p>如果您的项目审核通过，处于“新订单”和“未雇佣”状态，都可以添加补充要求</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>如何选择设计师？</h3>
			<div class="designerDetail company">
				<p>收到设计师申请项目后，您就可以开始挑选设计师了；从项目发布开始，您有7天的时间选择设计师。您可以查看到设计师的所在地、滴滴设计等级、毕业院校、工作经历、过往作品案例等，以及已完成项目数和项目收入；在此期间，我们鼓励您与设计师沟通，您可以通过滴滴设计的在线聊天功能与设计沟通或留言。当您确定与某位设计合作后，请点击“雇佣”</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>如何联系设计师？</h3>
			<div class="designerDetail company">
				<p>确定雇佣后，您可以点击“聊天”，进行项目的进一步详细沟通（不建议您通过除滴滴设计平台以外的其他即时通讯软件进行沟通，交易完成后会自动生成聊天记录，有利于您作为项目出现争议时申诉提交的过程证明），或是通过设计师留下的联系方式进行直接沟通</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>如何查看设计师的成果？</h3>
			<div class="designerDetail company">
				<p>点击APP软件最底部“进行中”，然后打开项目详情页，底部会提示“查看提交”同时会显示具体提交时间；点开“查看提交”会显示提交成果的云盘链接地址和密码</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>如果对设计成果不满意怎么办？</h3>
			<div class="designerDetail company">
				<p>首先请您有耐心的继续与设计师保持良好的沟通，如果您还不满意，请点击“设置”中“客服与帮助”页面在线反馈相关问题或者拨打客服电话，我们从中协调帮您解决问题</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>项目完成后怎么付款？</h3>
			<div class="designerDetail company">
				<p>如果您与设计师沟通无误后，设计师会申请结算，平台会自动给您发送申请结算提示信息，您点开进行中的项目会收到“确定结算”的请求，点击确定之后，请您给该设计师针对本次设计服务内容给出公正客观的评价。如果您觉得设计师不错，可以点击右上角“收藏”</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>项目结束后怎样开发票？</h3>
			<div class="designerDetail company">
				<p>您点击“确定结算”之后，按照流程给设计师公正客观的评价，之后会显示支付成功，点击“发票开具” ，您可以选择“按订单开票”或“按金额开票”，之后按照要求填写相关开票信息，点击提交就可以了，发票会于每周三统一寄出</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>如何查询发票开具状态？</h3>
			<div class="designerDetail company">
				<p>点击左上角个人信息选项,然后点击“我的钱包”、“发票”选项中的“开票历史”</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>如何给账户充值？</h3>
			<div class="designerDetail company">
				<p>请您点击“余额”，然后点击“充值”选项，具体充值方式目前仅支持微信支付与支付宝支付</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我的评价部分有什么用？</h3>
			<div class="designerDetail company">
				<p>如果您累计三次项目交易评分为三星以下，将会面临账号自动冻结，所以请您慎重认真的操作每一个项目交易的过程；如您遭遇到恶意差评，请您及时联系在线客服进行申诉，或者在我的评价里点击申诉选项，并如实填写申诉的原因</p>
			</div>
		</li>
	</ul>
	<ul name="flows" class="iAmDesigner" id="designer" style="display:none">
		<li>
			<h3><i>+</i>怎样加入滴滴设计平台？</h3>
			<div class="designerDetail designer">
				<p>首页最底端扫描二维码进入App Store（苹果应用商店）下载DD设计师版，或者直接进入App Store（苹果应用商店）搜索“DD设计师”，按照步骤依次如实填写完成注册，通过审核后，滴滴设计平台会依据你填写的资料，自动推送相关项目信息</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我怎么才知道是否通过审核？</h3>
			<div class="designerDetail designer">
				<p>如果您按照要求填写完信息之后，一般会在两个工作日内完成审核，具体审核通过时间，以短信通知为准。(填写资料完毕后,审核通过和未通过都会以短信的形式通知用户)</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>加入滴滴设计平台后，我能做些什么？</h3>
			<div class="designerDetail designer">
				<p>你可以查看滴滴设计平台推送给你的项目。你可以在“个人信息”中选择“我的设计类型”，平台会依据此选项偏好推送相关项目。你还可以管理完善你的个人资料信息，如工作经历、作品、证照信息等方面，把你的专业能力、设计经历等优势展现给项目发布方。只有你参与申请的项目，项目发布方才能查看你的个人资料与作品集，其他项目方或其他设计师无法查看</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>滴滴设计根据什么给我推送项目信息？</h3>
			<div class="designerDetail designer">
				<p>滴滴设计会根据项目类型、设计师所在地这两个方面信息进行综合判断，你可以在“个人信息”中设置</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>为什么我没有收到项目推送？</h3>
			<div class="designerDetail designer">
				<p>1、“我的设计类型”’中至少勾选一项</p>
				<p>2、你所在的地方暂时还没有相关类型项目发布</p>
				<p>3、进入主页向下拖动刷新一下</p>
				<p>如果仍没有收到项目推送信息，请联系滴滴设计客服</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>项目推送的有效期有多久？</h3>
			<div class="designerDetail designer">
				<p>一般从项目发布成功之日算起，后延7个工作日（不含节假日），如果过了7个工作日之后，仍没有选中设计师，项目信息自动失效撤销</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我怎么知道自己是否被选中雇佣了？</h3>
			<div class="designerDetail designer">
				<p>一般会有信息提示推送，打开“DD设计师”APP，进入首页“我的消息”中查看或者是点击底部的“进行中”选项</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>自己被项目发布方选中之后怎么沟通呢？</h3>
			<div class="designerDetail designer">
				<p>一般建议您通过APP内聊天功能直接与项目发布方进一步沟通项目细节（会自动保存聊天记录在本地的,卸载后就没有聊天记录，如有纠纷可作为调解依据），或者与项目方直接电话联系</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>怎么提交设计成果？</h3>
			<div class="designerDetail designer">
				<p>项目完成之后，上传到云盘中存储，设置好存储地址和云盘下载密码，然后点击进行中的项目，底端有“提交作品”选项，点击之后会显示云盘存储地址和云盘下载密码对话框，依次填好之前设置好的存储地址和下载密码，然后点击提交</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>设计成果提交之后怎么申请付款呢？</h3>
			<div class="designerDetail designer">
				<p>设计成果提交成功之后，平台系统会自动通知项目发布方查看，同时您也可以通过APP内的聊天功能提醒或者电话提醒，如果经过沟通之后，项目发布方对成果很满意，您可以在“进行中”的栏目中点击“申请结算”</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>怎么才能收到项目发布方的付款呢？</h3>
			<div class="designerDetail designer">
				<p>发出“申请结算”之后，如果项目发布方无异议，点击“确定结算”之后，钱就到你滴滴设计账户上了</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>为什么账户余额跟我做的项目金额少了一些呢？</h3>
			<div class="designerDetail designer">
				<p>系统平台自动扣除国家规定应缴纳税额、个人所得税之后才是你项目后的所得金额报酬</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我怎么从滴滴设计账户提现呢？</h3>
			<div class="designerDetail designer">
				<p>您可以选择通过支付宝平台或银行卡提现，一般本周收入需要延迟到下周才可以提现(每周二就可以提现,比如说周二有一笔款来了,周二就可以提现)</p>
			</div>
		</li>
		<li>
			<h3><i>+</i>我的钱包里“D币”是干什么的呢？</h3>
			<div class="designerDetail designer">
				<p>D币可以参与滴滴商城里的抽奖活动，还可以用来兑换滴滴商城里的物品，您可以推荐其他设计师或其他公司，当们使用您的推荐码成功双方均被奖励D币；同时如果您每完成一笔项目交易获得好评的话，平台系统也会自动奖励您D币的。(您每完成一笔项目交易,就会获得D币,不仅仅是好评获得D币,差评还可能减少D币)</p>
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
