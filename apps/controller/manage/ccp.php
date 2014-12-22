<?php
// 评论和分类和属性通用管理部分
class MCCP{
	const ERRNAME = '_x_errmsg';
	// 分类列表
	public function cates(){
		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		$appid = Uri::get('appid',0);
		$appname = Uri::get('appname','');
		$datas = array('appid'=>$appid);

		// 过滤条件
		$conds = array('appid'=>$appid);	//检索条件
		$pageParams = array('appid'=>$appid);//分页搜索参数
		if (!empty($_REQUEST['name'])) {
			$fname = trim($_REQUEST['name']);
			$conds['name'] = 'like "%'.$fname.'%"';
			$pageParams['name'] = $fname;
		}
		if (isset($_REQUEST['parentId'])) {
			$fparentid = trim($_REQUEST['parentId']);
			if (strlen($fparentid)>0) {
				$conds['parentId'] = $fparentid;
				$pageParams['parentId'] = $fparentid;
			}
		}
		if (!empty($_REQUEST['id'])) {
			$conds['id'] = $_REQUEST['id'];
			$pageParams['id'] = $_REQUEST['id'];
		}
		if (isset($_REQUEST['status']) && $_REQUEST['status']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['status'] = $_REQUEST['status'];
			$pageParams['status'] = $_REQUEST['status'];
		}

		$ret = Module_Category::getChilds(0,0,$conds,$page,$psize);
		$datas['items'] = $ret['data']['items'];
		$datas['pages'] = multiPages($page,$psize,$ret['data']['total'],$pageParams,true);		

		$datas['appname'] = $appname;
		$datas['filter'] = $pageParams;
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$itemid = isset($_REQUEST['itemid'])?$_REQUEST['itemid']:0;
		$datas['options'] = Module_Category::getChildsOptions($datas['items'],$itemid);
		return $datas;
	}
	// 编辑分类
	public function cateedit(){
		$datas = array();
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$appid = Uri::post('appid',0);
			$params = array('name'=>Uri::post('name'),
																		'parentId'=>Uri::post('parentId',0),
																		'status'=>Uri::post('status'),
																		'appid'=>$appid,
																		'desc'=>Uri::post('desc'));
			$ret = Module_Category::setCate($params,$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('modulename'=>Module_Category::APPNAME,
						'moduleid'=>Module_Category::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';分类对应模块APPID '.$appid.';操作库:'.Module_Category::TNAME
						));
			}
			Uri::redirect(Uri::getPrevPage());
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		// 修改
		if (count($params)>0) {
			$ret = Module_Category::getCate($params[0]);
			if ($ret['code']>0) {
				$datas['item'] = $ret['data'];
				$datas['appid'] = $datas['item']['appid'];
				$datas['parentId'] =$datas['item']['parentId'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg']);
				Uri::redirect(Uri::getPrevPage());
			}
		}elseif (isset($_GET['appid'])) {
			// 新增子项
			$datas['appid'] = Uri::get('appid',0);
			$datas['parentId'] = Uri::get('parentId',0);
		}
		// select
		if ($datas['parentId']==0) {
			$datas['pname'] = '顶级';
		}else{
			$ret = Module_Category::getCate($datas['parentId']);
			$datas['pname'] = $ret['data']['name'];
		}
		return $datas;
	}
	// 删除分类
	public function catedel(){
		$id = Uri::get('id',0);
		if ($id>0) {
			$ret = Module_Category::delCate($id);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('modulename'=>Module_Category::APPNAME,
						'moduleid'=>Module_Category::APPID,
						'key'=>'删除',
						'message'=>'操作id '.$id.';操作库:'.Module_Category::TNAME
						));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
	// 评论列表
	public function comms(){
		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;

		$appid = Uri::get('appid',0);
		$datas = array('appid'=>$appid);
		// 过滤条件
		$conds = array('appid'=>$appid);	//检索条件
		$pageParams = array('appid'=>$appid);//分页搜索参数
		if (!empty($_REQUEST['objid'])) {
			$conds['objid'] = '= '.$_REQUEST['objid'].'';
			$pageParams['objid'] = $_REQUEST['objid'];
		}
		if (!empty($_REQUEST['username'])) {
			$conds['username'] = 'like "%'.$_REQUEST['username'].'%"';
			$pageParams['username'] = $_REQUEST['username'];
		}
		if (!empty($_REQUEST['message'])) {
			$conds['message'] = 'like "%'.$_REQUEST['message'].'%"';
			$pageParams['message'] = $_REQUEST['message'];
		}
		
		if (isset($_REQUEST['status']) && $_REQUEST['status']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['status'] = $_REQUEST['status'];
			$pageParams['status'] = $_REQUEST['status'];
		}
		$datas['items'] = Module_Comment::getItems($conds,'order by id desc',$page,$psize);
		$datas['pages'] = multiPages($page,$psize,$datas['items']['total'],$pageParams,true);		
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['filter'] = $pageParams;
		return $datas;
	}
	// 编辑评论
	public function commedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$appid = Uri::post('appid',0);
			$attrs = array(
				'appid'=>$appid,
				'userid'=>Uri::post('userid'),
				'objid'=>Uri::post('objid'),
				'scroe'=>Uri::post('scroe',0),
				'message'=>Uri::post('message'),
				'status'=>Uri::post('status',0),
			);
			// 因为评论不在后台新增，所以暂时不考虑其他字段的修改
			$ret = Module_Comment::setItem($attrs,$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('modulename'=>Module_Comment::APPNAME,
						'moduleid'=>Module_Comment::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';评论对应模块APPID '.$appid.';操作库:'.Module_Comment::TNAME
						));
			}
			Uri::redirect(Uri::getPrevPage());
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Comment::getItem($params[0]);
			if ($ret['code']>0) {
				$datas['oitem'] = $ret['data'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg']);
				Uri::redirect(Uri::getPrevPage());
			}
		}
		return $datas;
	}
	// 删除评论
	public function commdel(){
		$params = Uri::getParams();
		$params = $params['params'];
		if (count($params)>0) {
			$ret = Module_Comment::delItem($params[0]);
		}
		// 添加日志
		if ($ret['code']>0) {
			Module_log::setItem(array('modulename'=>Module_Comment::APPNAME,
					'moduleid'=>Module_Comment::APPID,
					'key'=>'删除',
					'message'=>'操作id '.$params[0].$appid.';操作库:'.Module_Comment::TNAME
					));
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
	// 区域列表
	public function citys(){
		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		$appid = Uri::get('appid',0);
		$appname = Uri::get('appname','');
		$datas = array('appid'=>$appid);

		// 过滤条件
		$conds = array('appid'=>$appid);	//检索条件
		$pageParams = array('appid'=>$appid);//分页搜索参数
		if (!empty($_REQUEST['name'])) {
			$fname = trim($_REQUEST['name']);
			$conds['name'] = 'like "%'.$fname.'%"';
			$pageParams['name'] = $fname;
		}
		if (isset($_REQUEST['parentId'])) {
			$fparentid = trim($_REQUEST['parentId']);
			if (strlen($fparentid)>0) {
				$conds['parentId'] = $fparentid;
				$pageParams['parentId'] = $fparentid;
			}
		}
		if (!empty($_REQUEST['id'])) {
			$conds['id'] = $_REQUEST['id'];
			$pageParams['id'] = $_REQUEST['id'];
		}
		if (isset($_REQUEST['status']) && $_REQUEST['status']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['status'] = $_REQUEST['status'];
			$pageParams['status'] = $_REQUEST['status'];
		}

		$ret = Module_Area::getChilds(0,0,$conds,$page,$psize);
		$datas['items'] = $ret['data']['items'];
		$datas['pages'] = multiPages($page,$psize,$ret['data']['total'],$pageParams,true);		

		$datas['appname'] = $appname;
		$datas['filter'] = $pageParams;
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$itemid = isset($_REQUEST['itemid'])?$_REQUEST['itemid']:0;
		$datas['options'] = Module_Area::getChildsOptions($datas['items'],$itemid);
		return $datas;
	}
	// 编辑区域
	public function cityedit(){
		$datas = array();
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$appid = Uri::post('appid',0);
			$ret = Module_Area::setCate(array('name'=>Uri::post('name'),
																		'parentId'=>Uri::post('parentId'),
																		'status'=>Uri::post('status'),
																		'appid'=>$appid,
																		'zipcode'=>Uri::post('zipcode'),
																		'desc'=>Uri::post('desc'),),$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('modulename'=>Module_Area::APPNAME,
						'moduleid'=>Module_Area::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';区域对应模块APPID '.$appid.';操作库:'.Module_Area::TNAME
						));
			}
			Uri::redirect(Uri::getPrevPage());
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		// 修改
		if (count($params)>0) {
			$ret = Module_Area::getCate($params[0]);
			if ($ret['code']>0) {
				$datas['item'] = $ret['data'];
				$datas['appid'] = $datas['item']['appid'];
				$datas['parentId'] =$datas['item']['parentId'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg']);
				Uri::redirect(Uri::getPrevPage());
			}
		}elseif (isset($_GET['appid'])) {
			// 新增子项
			$datas['appid'] = Uri::get('appid',0);
			$datas['parentId'] = Uri::get('parentId',0);
		}
		// select
		if ($datas['parentId']==0) {
			$datas['pname'] = '顶级';
		}else{
			$ret = Module_Area::getCate($datas['parentId']);
			$datas['pname'] = $ret['data']['name'];
		}
		return $datas;
	}
	// 删除区域
	public function citydel(){
		$id = Uri::get('id',0);
		if ($id>0) {
			$ret = Module_Area::delCate($id);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('modulename'=>Module_Area::APPNAME,
						'moduleid'=>Module_Area::APPID,
						'key'=>'删除',
						'message'=>'操作id '.$id.';操作库:'.Module_Area::TNAME
						));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
	// 编辑属性
	public function propedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$appid = Uri::post('appid',0);
			$ret = Module_Prop::setItem(array('name'=>Uri::post('name'),
																		'vals'=>Uri::post('vals'),
																		'status'=>Uri::post('status'),
																		'appid'=>$appid,
																		'desc'=>Uri::post('desc'),),$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('modulename'=>Module_Prop::APPNAME,
						'moduleid'=>Module_Prop::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';分类对应模块APPID '.$appid.';操作库:'.Module_Prop::TNAME
						));
			}
			Uri::redirect(Uri::getPrevPage());
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Prop::getItem($params[0]);
			if ($ret['code']>0) {
				$datas['prop'] = $ret['data'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg']);
				Uri::redirect(Uri::getPrevPage());
			}
		}
		return $datas;
	}
	// 删除属性
	public function propdel(){
		$params = Uri::getParams();
		$params = $params['params'];
		if (count($params)>0) {
			$cnt = $GLOBALS['db']->result('select count(*) as cnt from '.Module_Prop::TPITEM.' where propid ='.$params[0]);
			if ($cnt>0) {
				Helper::setSession(self::ERRNAME,'该属性被其他模块使用着，不能直接删除。');
			}else{
				$ret = Module_Prop::delItem($params[0]);
				// 添加日志
				if ($ret['code']>0) {
					Module_log::setItem(array('modulename'=>Module_Prop::APPNAME,
							'moduleid'=>Module_Prop::APPID,
							'key'=>'删除',
							'message'=>'操作id '.$params[0].$appid.';操作库:'.Module_Prop::TNAME
							));
				}
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
}