<div class="sidebar" id="sidebar">
	<script type="text/javascript">
		try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
	</script>
	<!-- sidebar-shortcuts -->
	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<button class="btn btn-success">
				<i class="icon-signal"></i>
			</button>

			<button class="btn btn-info">
				<i class="icon-pencil"></i>
			</button>

			<button class="btn btn-warning">
				<i class="icon-group"></i>
			</button>

			<button class="btn btn-danger">
				<i class="icon-cogs"></i>
			</button>
		</div>
		<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
			<span class="btn btn-success"></span>
			<span class="btn btn-info"></span>
			<span class="btn btn-warning"></span>
			<span class="btn btn-danger"></span>
		</div>
	</div><!-- #sidebar-shortcuts -->
	<ul class="nav nav-list">
		<!-- <li class="active">
			<a href="/manage/home/index">
				<i class="icon-dashboard"></i>
				<span class="menu-text"> 仪表盘 </span>
			</a>
		</li> -->
		<!-- 主菜单树 -->
		<?php
			foreach ($menuTree as $mv) {
				echo '<li>'.
					'<a href="#" class="dropdown-toggle">'.
						'<i class="icon-cog"></i>'.
						'<span class="menu-text">'.$mv['name'].'</span>'.
						'<b class="arrow icon-angle-down"></b>'.
					'</a>';
				if (isset($mv['subs'])) {
					echo '<ul class="submenu">';
					foreach ($mv['subs'] as $subk => $subv) {
						echo '<li><a class="ajaxbtn" href="'.Uri::build('manage/'.$mv['con'],$subk).'">'.$subv.'</a></li>';
					}
					echo '</ul>';
				}
				echo '</li>';
			}
		?>
	</ul><!-- /.nav-list -->
	<div class="sidebar-collapse" id="sidebar-collapse">
		<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
	</div>

	<script type="text/javascript">
		try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
	</script>
</div>