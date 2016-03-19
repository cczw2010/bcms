<?php 
	include('m_header.php');
?>
	<div class="main-container" id="main-container">
		<script type="text/javascript">
			try{ace.settings.check('main-container' , 'fixed')}catch(e){}
		</script>
		<div class="main-container-inner">
			<a class="menu-toggler" id="menu-toggler" href="#">
				<span class="menu-text"></span>
			</a>
			<?php include('m_sliderbar.php');?>
			<div class="main-content">
				<div class="breadcrumbs" id="breadcrumbs">
					<script type="text/javascript">
						try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
					</script>
					<ul class="breadcrumb">
						<li>
							<i class="icon-home home-icon"></i>
							<a href="/manage/home/index">首页</a>
						</li>
						<li><span class="active" id="currentcrumb"></span></li>
					</ul><!-- .breadcrumb -->
				</div>
				<!-- main area -->
				<div class="page-content">
					<div class="row">
						<div class="col-sm-12" id="pageArea">
							<!-- 实际内容展示区 -->
							<h5 class="text-center">欢迎使用</h5>
						</div>
					</div>
				</div>
			</div>
		</div>
		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="icon-double-angle-up icon-only bigger-110"></i>
		</a>
	</div>
<?php
	include('m_footer.php');
?>