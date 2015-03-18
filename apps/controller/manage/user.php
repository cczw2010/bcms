<?php
// 用户管理类
class User{
	const ERRNAME = '_x_errmsg';
	function __construct(){
		$this->loginuser = Module_User::getloginUser(true);
		if (empty($this->loginuser)) {
			if ($GLOBALS['cur_method']!='login') {
				Uri::build('manage/user','login',false,true);
			}
		}else{
			if ($GLOBALS['cur_method']=='login') {
				Uri::build('manage/home','index',false,true);
			}
			/////////////////// 切入权限管理模块,根据权限来展示树
			$this->rights = Module_Group::isManager($this->loginuser['group']);
			if ($this->loginuser['group']!= Module_Group::GROUP_SUPER && $this->rights===false) {
					// throw new Exception('对不起，您没有权限进行该操作！请与管理员联系', 1);
					showMessage('对不起，您没有权限进行该操作！请与权限管理员联系');
			}
			$this->view->data(array('user'=>$this->loginuser));
		}
	}
	//登陆
	public function login(){
		$datas = array('error'=>'');
		if (isset($_POST['subbtn'])) {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$captcha = trim($_POST['captcha']);
			if (!empty($username) && !empty($password) && !empty($captcha)) {
				// 检查验证码
		 		if (Captcha::check($captcha)) {
		 			// 登陆
					$ret = Module_User::login($username,$password,true);
					if ($ret['code']>0) {
						Uri::build('manage/home','index',false,true);
						// Uri::redirect('/');
					}
					$datas['error'] = $ret['msg'];
		 		}else{
		 			$datas['error'] = '验证码错误，请重新输入';
		 		}
			}else{
				$datas['error'] = '必填项不能为空';				
			}
		}else{
			// 记录进入当前页的页面.方便登陆成功后跳转, 并判断排除前一页是否也是当前页
			if (isset($_SERVER['HTTP_REFERER'])) {
				if (strripos($_SERVER['HTTP_REFERER'], '/user')>=0) {
					Uri::setPrevPage($_SERVER['HTTP_REFERER']);
				}
			}
		}
		// $datas['thirdapp'] = Module_ThirdLogin::getThirdLoginHTML();
		// dump($GLOBALS['db']->getlastsql());
		$this->view->load('manage/m_ulogin',$datas);
	}
	// 退出登陆
	public function logout(){
		Module_User::logout(true);
		Uri::build('manage/user','login',false,true);
	}
	// 用户列表
	public function users(){
		$datas = array('types'=>Module_User::TYPE_USER);
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);

		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		// 过滤条件
		$conds = array('types'=>Module_User::TYPE_USER);	//检索条件
		$pageParams = array('types'=>Module_User::TYPE_USER);//分页搜索参数
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

		$ret = Module_Group::getGroups(array('types'=>Module_Group::TYPE_USER));
		$datas['groups'] = $ret['list'];
		$datas['pages'] = multiPages($page,$psize,$datas['users']['total'],$pageParams,true);

		$this->view->load('manage/m_users',$datas);
	}
	// 管理员列表
	public function managers(){
		$datas = array('types'=>Module_User::TYPE_MANAGER);
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);

		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		// 过滤条件
		$conds = array('types'=>Module_User::TYPE_MANAGER);	//检索条件
		$pageParams = array('types'=>Module_User::TYPE_MANAGER);//分页搜索参数
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

		$ret = Module_Group::getGroups(array('types'=>Module_Group::TYPE_MANAGER));
		$datas['groups'] = $ret['list'];
		$datas['pages'] = multiPages($page,$psize,$datas['users']['total'],$pageParams,true);

		$this->view->load('manage/m_managers',$datas);
	}
	// 用户编辑
	public function edit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$types = Uri::post('types',Module_User::TYPE_USER);
			$username = Uri::post('username');
			if(mb_strlen($username)>0){
				$ret = Module_User::modifyUser($id,array('username'=>$username,
																			'email'=>Uri::post('email'),
																			'sign'=>Uri::post('sign'),
																			'group'=>Uri::post('group'),
																			'status'=>Uri::post('status')));
				if ($ret) {
					// 添加日志
					Module_log::setItem(array('modulename'=>Module_User::APPNAME,
						'moduleid'=>Module_User::APPID,
						'key'=>'更新',
						'message'=>'操作id '.$id.';操作库:'.Module_User::TNAME
						));
				}
			}else{
				Helper::setSession(self::ERRNAME,'用户名不能为空！');
			}
			if ($types == Module_User::TYPE_USER) {
				Uri::build('manage/user','users',false,true);
			}else{
				Uri::build('manage/user','managers',false,true);
			}
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$types = $params[1]||Module_Group::TYPE_USER;
			$ret = Module_User::getUser($params[0]);
			if ($ret['code']>0) {
				$datas['user'] = $ret['data'];
				$ret = Module_Group::getGroups(array('types'=>$types));
				$datas['groups'] = $ret['list'];
				$this->view->load('manage/m_useredit',$datas);
			}else{
				Helper::setSession(self::ERRNAME,'没有相关用户信息');
				if ($types == Module_User::TYPE_USER) {
					Uri::build('manage/user','users',false,true);
				}else{
					Uri::build('manage/user','managers',false,true);
				}
			}
		}
	}
	// 个人信息修改
	public function editinfo(){
		// 表单提交
		if (isset($_POST['id'])) {
			$ret = array('code'=>-1,'msg'=>'');
			$id = Uri::post('id',0);
			$username = Uri::post('username');
			if(mb_strlen($username)>0){
				$_ret = Module_User::modifyUser($id,array('username'=>$username,
																			'email'=>Uri::post('email'),
																			'sign'=>Uri::post('sign'),
																			'status'=>Uri::post('status')));
				if ($_ret) {
					// 添加日志
					Module_log::setItem(array('modulename'=>Module_User::APPNAME,
						'moduleid'=>Module_User::APPID,
						'key'=>'更新',
						'message'=>'操作id '.$id.';操作库:'.Module_User::TNAME
						));
					$ret['msg'] = '修改成功';
				}else{
					$ret['msg'] = '修改失败，请重试';
				}
			}else{
				$ret['msg'] = '用户名不能为空';
			}
			die(json_encode($ret));
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_User::getUser($params[0]);
			if ($ret['code']>0) {
				$datas['user'] = $ret['data'];
			}else{
				$datas['errmsg'] = '没有相关用户信息';
			}
		}

		$ret = Module_Group::getGroups();
		$datas['groups'] = $ret['list'];
		$this->view->load('manage/m_userinfoedit',$datas);
	}
	// 修改当前用户密码
	public function repass(){
		if (Http::isPost()) {
			$ret = array('code'=>-1,'msg'=>'操作失败，请重试');
			$password = Uri::post('password','');
			$id = Uri::post('id',0);
			$check = FormVerify::rule(
							array(($id>0),'参数错误！'),
							array(FormVerify::password($password,6,16),'密码必须在6~16个字符之间,允许的符号(-_)字母数字)')
							);
			if ($check!==true) {	
				$ret['msg'] = $check;
			}else{
				$result = Module_User::modifyUser($id,array('password'=>md5($password)));
				if ($result!==false) {
					$ret['code'] = 1;
					$ret['msg'] = '更新成功！';
				}
			}
			die(json_encode($ret));
		}
		$datas =array('user'=>Module_User::getloginUser());
		$this->view->load('manage/m_urepass',$datas);
	}
	// 删除用户
	public function del(){
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		$types = $params[1];
		if (count($params)>0) {
			$ret = Module_User::delUser($params[0]);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('modulename'=>Module_User::APPNAME,
						'moduleid'=>Module_User::APPID,
						'key'=>'删除',
						'message'=>'操作id '.$params[0].';操作库:'.Module_User::TNAME
						));
			}
		}
		// 不管删除成功与否直接跳转
		if ($types == Module_User::TYPE_USER) {
			Uri::build('manage/user','users',false,true);
		}else{
			Uri::build('manage/user','managers',false,true);
		}
	}
	// 用户收货地址
	public function address(){
		$uid = Uri::get('id',0);
		$address = Module_User::getAdresss($uid);

		$datas=array('items'=>$address);
		$this->view->load('manage/m_address',$datas);
	}
	// 用户登录日志
	public function ulogs(){
		$datas = array();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);

		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		// 过滤条件
		$conds = array();	//检索条件
		$pageParams = array();//分页搜索参数
		if (!empty($_REQUEST['userid'])) {
			$conds['userid'] =  $_REQUEST['userid'];
			$pageParams['userid'] = $_REQUEST['userid'];
		}
		if (!empty($_REQUEST['username'])) {
				$conds['username'] = 'like "%'.$_REQUEST['username'].'%"';
				$pageParams['username'] = $_REQUEST['username'];
			}
		if (!empty($_REQUEST['addtime'])) {
			$conds[] = 'FROM_UNIXTIME(addtime,"%Y-%m-%d")="'.date('Y-m-d',strtotime($_REQUEST['addtime'])).'"';
			$pageParams['addtime'] = $_REQUEST['addtime'];
		}
		// 检索
		$datas['logs'] = Module_User::getUserLog($conds,$page,$psize);
		$datas['pages'] = multiPages($page,$psize,$datas['logs']['total'],$pageParams,true);		
		$this->view->load('manage/m_ulogs',$datas);
	}
	// 管理员分组组
	public function mgroup(){
		$datas = array();
		$datas['groups'] = Module_Group::getGroups(array('types'=>Module_Group::TYPE_MANAGER));
		$datas['modules'] = $this->getRightList();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['types'] = 0;
		Uri::setPrevPage();
		$this->view->load('manage/m_group',$datas);
	}
	// 用户分组
	public function ugroup(){
		$datas = array();
		$datas['groups'] = Module_Group::getGroups(array('types'=>Module_Group::TYPE_USER));
		$datas['modules'] = $this->getRightList();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['types'] = 1;
		Uri::setPrevPage();
		$this->view->load('manage/m_group',$datas);
	}
	// 编辑用户组
	public function gedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = intval($_POST['id']);
			$types = intval($_POST['types']);
			$name = $_POST['name'];
			$rights = isset($_POST['rights'])?implode(',', $_POST['rights']):'';
			$ret = Module_Group::setGroup(array('name'=>$name,
																			'rights'=>$rights,
																			'types'=>$types,
																			'desc'=>$_POST['desc']),$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('modulename'=>Module_Group::APPNAME,
						'moduleid'=>Module_Group::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Group::TNAME
						));
			}
			$page = $types ==0?'mgroup':'ugroup';
			Uri::build('manage/user',$page,false,true);
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Group::getGroups(array('id'=>$params[0]));
			if ($ret['pcnt']>0) {
				$datas['group'] = current($ret['list']);
			}else{
				Helper::setSession(self::ERRNAME,'没有相关用户组信息'.$GLOBALS['db']->getlastsql());
				Uri::build('manage/user','ugroup',false,true);
			}
		}
		$datas['modules'] = $this->getRightList();
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
				Module_log::setItem(array('modulename'=>Module_Group::APPNAME,
						'moduleid'=>Module_Group::APPID,
						'key'=>'删除',
						'message'=>'操作id '.$params[0].';操作库:'.Module_Group::TNAME
						));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
	//中文说明
	private function getRightList(){
		return array('manage'=>array(
															// 'home-index'=>'首页',
															'setting-info'=>'系统信息',
															'setting-cache'=>'缓存处理',
															'setting-logs'=>'log日志',
															'setting-dbback'=>'数据库备份',
															'user-users'=>'用户列表',
															'user-managers'=>'管理员列表',
															'user-mgroup'=>'管理员分组',
															'user-ugroup'=>'用户分组',
															'user-ulogs'=>'登录日志',
															'article-lists'=>'文章列表',
															'article-cate'=>'文章分类',
															'article-comm'=>'文章评论管理',
															'product-brands'=>'品牌列表',
															'product-lists'=>'商品列表',
															'product-cate'=>'商品分类',
															'product-comm'=>'商品评论管理',
															'order-lists'=>'订单列表',
															'setting-citys'=>'区域管理',
															'setting-verify'=>'敏感词汇',
															'setting-thirdlogincfg'=>'第三方登陆配置',
															'setting-payment'=>'支付配置',
															'mail-cfg'=>'SMTP邮件配置',
															'mail-send'=>'发送邮件',
															)
			);
	}
}