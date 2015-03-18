<?php
class Comment{
	const ERRNAME = '_x_errmsg';
	// 评论列表
	public function lists(){
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
		$this->view->load('manage/m_comments',$datas);
	}
	// 编辑评论
	public function edit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$appid = Uri::post('appid',0);
			$attrs = array(
				'appid'=>$appid,
				'userid'=>Uri::post('userid'),
				'objid'=>Uri::post('objid'),
				'score'=>Uri::post('score',0),
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
		$this->view->load('manage/m_commedit',$datas);
	}
	// 删除评论
	public function del(){
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
}