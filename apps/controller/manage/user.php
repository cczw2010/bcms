<?php
// 用户管理类
class User{
	const ERRNAME = '_x_errmsg';
	const MODULEID = 'user';

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
	// 用户列表
	public function users(){
		$datas = array();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);

		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		// 过滤条件
		$conds = array();	//检索条件
		$pageParams = array();//分页搜索参数
		if (!empty($_REQUEST['email'])) {
			$conds['email'] = 'like "%'.$_REQUEST['email'].'%"';
			$pageParams['email'] = $_REQUEST['email'];
		}
		if (!empty($_REQUEST['username'])) {
			$conds['username'] = 'like "%'.$_REQUEST['username'].'%"';
			$pageParams['username'] = $_REQUEST['username'];
		}
		if (isset($_REQUEST['status']) && $_REQUEST['status']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['status'] = $_REQUEST['status'];
			$pageParams['status'] = $_REQUEST['status'];
		}
		$datas['conds'] = $conds;
		// 检索
		$datas['users'] = Module_User::getUsers($conds,'order by id desc',$page,$psize);

		$ret = Module_Group::getGroups(array('moduleid'=>self::MODULEID));
		$datas['groups'] = $ret['list'];
		$datas['pages'] = multiPages4Ace($page,$psize,$datas['users']['total'],$pageParams,true);

		$this->view->load('manage/m_users',$datas);
	}
	// 用户编辑
	public function edit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$username = Uri::post('username');
			if(mb_strlen($username)>0){
				$attrs = array('email'=>Uri::post('email'),
											'sign'=>Uri::post('sign'),
											'group'=>Uri::post('group'),
											'status'=>Uri::post('status'));
				if ($id==0) {
					$ret = Module_User::register($username,'123456',$attrs);
				}else{
					$attrs['username'] = $username;
					$ret = Module_User::modifyUser($id,$attrs);
				}
				if ($ret===true || $ret['code']==1) {
					// 添加日志
					Module_log::setItem(array('message'=>'操作id '.$id.';操作库:'.Module_User::TNAME));
				}
			}else{
				Helper::setSession(self::ERRNAME,'用户名不能为空！');
			}
			Uri::build('manage/user','users',false,true);
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_User::getUser($params[0]);
			if ($ret['code']>0) {
				$datas['oitem'] = $ret['data'];
			}else{
				Helper::setSession(self::ERRNAME,'没有相关用户信息');
				Uri::build('manage/user','users',false,true);
			}
		}
		$ret = Module_Group::getGroups(array('moduleid'=>self::MODULEID));
		$datas['groups'] = $ret['list'];
		$this->view->load('manage/m_useredit',$datas);
	}
	// 删除用户
	public function del(){
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_User::delUser($params[0]);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('message'=>'操作id '.$params[0].';操作库:'.Module_User::TNAME));
			}
		}
		Uri::build('manage/user','users',false,true);
	}
	// 用户收货地址
	public function address(){
		$uid = Uri::get('id',0);
		$address = Module_User::getAdresss($uid);

		$datas=array('items'=>$address);
		$this->view->load('manage/m_address',$datas);
	}
	// 用户分组
	public function ugroup(){
		$datas = array();
		$datas['groups'] = Module_Group::getGroups(array('moduleid'=>self::MODULEID));
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		Uri::setPrevPage();
		$this->view->load('manage/m_group',$datas);
	}
	// 编辑用户组
	public function gedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = intval($_POST['id']);
			$name = $_POST['name'];
			$rights = isset($_POST['rights'])?implode(',', $_POST['rights']):'';
			$ret = Module_Group::setGroup(array('name'=>$name,
												'moduleid'=>self::MODULEID,
												'rights'=>$rights,
												'desc'=>$_POST['desc']),$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Group::TNAME));
			}
			Uri::build('manage/user','ugroup',false,true);
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Group::getGroup($params[0]);
			if ($ret['code']>0) {
				$datas['group'] = $ret['data'];
			}else{
				// Helper::setSession(self::ERRNAME,'没有相关用户组信息'.$GLOBALS['db']->getLastSql());
				Helper::setSession(self::ERRNAME,'没有相关用户组信息');
				Uri::build('manage/user','ugroup',false,true);
			}
		}
		$this->view->load('manage/m_groupedit',$datas);
	}
	// 删除用户组
	public function gdel(){
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Group::delGroup($params[0]);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('message'=>'操作id '.$params[0].';操作库:'.Module_Group::TNAME));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
}