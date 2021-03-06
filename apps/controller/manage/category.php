<?php
// 评论和分类和属性通用管理部分
class Category{
	const ERRNAME = '_x_errmsg';
	function __construct(){
		$this->loginuser = Module_Manager::getloginUser();
		if (empty($this->loginuser)) {
			if ($GLOBALS['cur_method']!='login') {
				$this->view->load('manage/m_redirect',array('url'=>'/manage/manager/login'));
				die();
			}
		}else{
			if ($GLOBALS['cur_method']=='login') {
				Uri::build('manage/home','index',false,true);
			}
			/////////////////// 切入权限管理模块,根据权限来展示树
			if ($this->loginuser['username']!=$GLOBALS['config']['supermanager']['username']) {
				$group = Module_Group::getGroup($this->loginuser['group']);
				if ($group['code']==1) {
						if(empty($group['data']['rights'])){
							showMessage('对不起，您没有权限进行该操作！请与权限管理员联系');
						}
						// throw new Exception('对不起，您没有权限进行该操作！请与管理员联系', 1);
				}else{
					showMessage('管理员组信息错误!');
				}
			}
			$this->view->data(array('user'=>$this->loginuser));
		}
	}
	// 分类列表
	public function lists(){
		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		$moduleid = Uri::get('moduleid',0);
		$appname = Uri::get('appname','');
		$datas = array('moduleid'=>$moduleid);
		// 过滤条件
		$conds = array('moduleid'=>$moduleid);	//检索条件
		$pageParams = array('moduleid'=>$moduleid);//分页搜索参数
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
		$datas['pages'] = multiPages4Ace($page,$psize,$ret['data']['total'],$pageParams,true);

		$datas['appname'] = $appname;
		$datas['filter'] = $pageParams;
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$itemid = isset($_REQUEST['itemid'])?$_REQUEST['itemid']:0;
		$datas['options'] = Module_Category::getChildsOptions($datas['items'],$itemid);

		$this->view->load('manage/m_category',$datas);
	}
	// 编辑分类
	public function edit(){
		$datas = array();
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$moduleid = Uri::post('moduleid',0);
			$params = array('name'=>Uri::post('name'),
							'parentId'=>Uri::post('parentId',0),
							'status'=>Uri::post('status'),
							'moduleid'=>$moduleid,
							'desc'=>Uri::post('desc'));
			$ret = Module_Category::setCate($params,$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('message'=>'操作id '.($id==0?$ret['data']:$id).';分类对应模块moduleid '.$moduleid.';操作库:'.Module_Category::TNAME));
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
				$datas['moduleid'] = $datas['item']['moduleid'];
				$datas['parentId'] =$datas['item']['parentId'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg']);
				Uri::redirect(Uri::getPrevPage());
			}
		}elseif (isset($_GET['moduleid'])) {
			// 新增子项
			$datas['moduleid'] = Uri::get('moduleid',0);
			$datas['parentId'] = Uri::get('parentId',0);
		}
		// select
		if ($datas['parentId']==0) {
			$datas['pname'] = '顶级';
		}else{
			$ret = Module_Category::getCate($datas['parentId']);
			$datas['pname'] = $ret['data']['name'];
		}
		$this->view->load('manage/m_cateedit',$datas);
	}
	// 删除分类
	public function del(){
		$id = Uri::get('id',0);
		if ($id>0) {
			$ret = Module_Category::delCate($id);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('message'=>'操作id '.$id.';操作库:'.Module_Category::TNAME));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
}