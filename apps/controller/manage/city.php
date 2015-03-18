<?php
// 评论和分类和属性通用管理部分
class City{
	const ERRNAME = '_x_errmsg';
	// 区域列表
	public function lists(){
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
		$this->view->load('manage/m_citys',$datas);
	}
	// 编辑区域
	public function edit(){
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
		$this->view->load('manage/m_cityedit',$datas);
	}
	// 删除区域
	public function del(){
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
	
}