<?php
// 用户管理类
class Manager{
	const ERRNAME = '_x_errmsg';
	const MODULEID = 'manager';

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
			if ($GLOBALS['cur_method']!='logout') {
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
	}
	//登陆
	public function login(){
		$datas = array('error'=>'');
		if (Http::isPost()) {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$captcha = trim($_POST['captcha']);
			if (!empty($username) && !empty($password) && !empty($captcha)) {
				// 检查验证码
		 		if (Captcha::check($captcha)) {
		 			// 登陆
		 			if ($username == $GLOBALS['config']['supermanager']['username'] && $password == $GLOBALS['config']['supermanager']['password']) {
		 				Helper::setSession(Module_Manager::SESSION_KEY,array('username'=>$username,'group'=>'-1','id'=>0));
		 				Uri::build('manage/home','index',false,true);
		 			}else{
		 				$ret = Module_Manager::login($username,$password);
						if ($ret['code']>0) {
							Uri::build('manage/home','index',false,true);
						}
						$datas['error'] = $ret['msg'];
		 			}
		 		}else{
		 			$datas['error'] = '验证码错误，请重新输入';
		 		}
			}else{
				$datas['error'] = '必填项不能为空';
			}
		}
		$this->view->load('manage/m_ulogin',$datas);
	}
	// 退出登陆
	public function logout(){
		Module_Manager::logout();
		Uri::build('manage/manager','login',false,true);
	}
	// 管理员列表
	public function managers(){
		$datas = array();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);

		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;
		// 过滤条件
		$conds = array();	//检索条件
		$pageParams = array();//分页搜索参数
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
		$datas['users'] = Module_Manager::getUsers($conds,'order by id desc',$page,$psize);

		$ret = Module_Group::getGroups(array('moduleid'=>self::MODULEID));
		$datas['groups'] = $ret['list'];
		$datas['pages'] = multiPages4Ace($page,$psize,$datas['users']['total'],$pageParams,true);

		$this->view->load('manage/m_managers',$datas);
	}
	// 增加|修改管理员
	public function medit(){
		if (isset($_POST['id'])) {
			$ret = array('code'=>-1,'msg'=>'');

			$id = Uri::post('id',0);
			$group = Uri::post('group',0);
			$username = Uri::post('username','');
			$password = Uri::post('password','');

			if ($id>0) {
				$arrs = array('group'=>$group);
				if (!empty($password)) {
					if (FormVerify::password($password,6,16)) {
						$arrs['password'] = md5($password);
					}else{
						$ret['msg'] = '密码必须在6~16个字符之间,允许的符号(-_)字母数字)'; 
					}
				}
				if (empty($ret['msg'])) {
					if(Module_Manager::modifyUser($id,$arrs)){
						// $ret['code'] = 1;
						// $ret['msg'] = '操作成功';
						Helper::setSession(self::ERRNAME,'操作成功');
						Uri::build('manage/manager','managers',false,true);
					}else{
						$ret['msg'] = '编辑失败';
					}
				}
			}else{
				if (!FormVerify::userName($username,4,30) || $username==$GLOBALS['config']['supermanager']['username'] || !FormVerify::password($password,6,16)) {
					$ret['msg'] = '用户名必须再4~30个字符之间；密码必须在6~16个字符之间,允许的符号(-_)字母数字,且不能使用admin等敏感字样)';
				}else{
					$ret1 = Module_Manager::register($username,$password,array(
						'group'=>$group,
					));
					if ($ret1['code']>0) {
						// $ret['code'] = 1;
						// $ret['msg'] = '编辑成功';
						Helper::setSession(self::ERRNAME,'编辑成功');
						Uri::build('manage/manager','managers',false,true);
					}else{
						$ret['msg'] = '编辑失败';
					}
				}
			}
			die(json_encode($ret));
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Manager::getUser($params[0]);
			if ($ret['code']>0) {
				$datas['user'] = $ret['data'];
				$ret = Module_Group::getGroups(array('moduleid'=>self::MODULEID));
				$datas['groups'] = $ret['list'];
				$this->view->load('manage/m_medit',$datas);
			}else{
				Helper::setSession(self::ERRNAME,'没有相关用户信息');
				Uri::build('manage/manager','managers',false,true);
			}
		}
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
				$result = Module_Manager::modifyUser($id,array('password'=>md5($password)));
				if ($result!==false) {
					$ret['code'] = 1;
					$ret['msg'] = '更新成功！';
				}
			}
			die(json_encode($ret));
		}
		$datas =array('user'=>$this->loginuser);
		$this->view->load('manage/m_urepass',$datas);
	}
	// 删除用户
	public function del(){
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		$types = $params[1];
		if (count($params)>0) {
			$ret = Module_Manager::delUser($params[0]);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('message'=>'操作id '.$params[0].';操作库:'.Module_Manager::TNAME));
			}
		}
		Uri::build('manage/manager','managers',false,true);
	}
	// 管理员分组组
	public function mgroup(){
		$datas = array();
		$datas['groups'] = Module_Group::getGroups(array('moduleid'=>self::MODULEID));
		$datas['modules'] = $this->getRightList();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		Uri::setPrevPage();
		$this->view->load('manage/m_mgroup',$datas);
	}
	// 编辑用户组
	public function gedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = intval($_POST['id']);
			$name = $_POST['name'];
			$rights = isset($_POST['rights'])?implode(',', $_POST['rights']):'';
			$ret = Module_Group::setGroup(array('name'=>$name,
												'rights'=>$rights,
												'moduleid'=>self::MODULEID,
												'desc'=>$_POST['desc']),$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Group::TNAME));
			}
			Uri::build('manage/manager','mgroup',false,true);
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
				Uri::build('manage/manager','ugroup',false,true);
			}
		}
		$datas['modules'] = $this->getRightList();
		$this->view->load('manage/m_mgroupedit',$datas);
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
	//中文说明
	private function getRightList(){
		return array('manage'=>array(
						// 'home-index'=>'首页',
						'setting-info'=>'系统信息',
						'setting-cache'=>'缓存处理',
						'setting-logs'=>'log日志',
						'setting-dbback'=>'数据库备份',
						'user-users'=>'用户列表',
						'user-edit'=>'编辑用户',
						'user-ugroup'=>'用户分组',
						'user-gedit'=>'编辑用户分组',
						'manager-managers'=>'管理员列表',
						'manager-medit'=>'编辑管理员',
						'manager-mgroup'=>'管理员分组',
						'manager-gedit'=>'编辑管理员分组',
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