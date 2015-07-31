<?php
// 用户模块
class User{
	public $loginuser;
	// 初始化的时候就判断登陆
	function __construct(){
		$this->loginuser = Module_User::getLoginUser(); 
	}

	// 用户首页
	public function index(){
		if (!$this->loginuser) {
			Uri::redirect('/user/login');
		}
		$datas['user'] = $this->loginuser;
		$datas['user']['avatar']= Module_User::getUserAvatar($this->loginuser['id']);
		$this->view->load('user_index',$datas);
	} 
	// 个人信息修改
	public function edit(){
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
		$this->view->load('user_edit',$datas);
	}
	// 用户登陆
	public function login(){
		if ($this->loginuser) {
			Uri::redirect(Uri::getPrevPage());
		}
		$datas = array('error'=>'');
		if (isset($_POST['subbtn'])) {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$captcha = trim($_POST['captcha']);
			if (!empty($username) && !empty($password) && !empty($captcha)) {
				// 检查验证码
		 		if (Captcha::check($captcha)) {
		 			// 登陆
					$ret = Module_User::login($username,$password);
					if ($ret['code']>0) {
						Uri::redirect(Uri::getPrevPage());
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
		$datas['thirdapp'] = Module_ThirdLogin::getThirdLoginHTML();
		// dump($GLOBALS['db']->getLastSql());
		$this->view->load('user_login',$datas);
	}

	// 用户注册
	public function register(){
		// if ($this->loginuser) {
		// 	Uri::redirect('/user');
		// }
		$datas = array('error'=>'');
		if (isset($_POST['subbtn'])) {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$email = trim($_POST['email']);
			
			$check = FormVerify::rule(
				array(FormVerify::userName($username,4,30),'用户名必须再4~30个字符之间'),
				array(Module_Sword::banned($username),'用户名请不要使用敏感词汇'),
				array(FormVerify::password($password,6,16),'密码必须在6~16个字符之间,允许的符号(|-_字母数字)'),
				array(FormVerify::email($email),'邮箱格式不正确')
				);
			if ($check===true) {
	 			// 注册
				$ret = Module_User::register($username,$password,array(
					'email'=>$email,
					'types'=>Module_User::TYPE_USER,
				));
				if ($ret['code']>0) {
					// 登陆跳转
					$ret = Module_User::login($username,$password);
					Uri::redirect('/');
				}
				$datas['error'] = $ret['msg'];
			}else{
				$datas['error'] = $check;
			}
			
		}
		$this->view->load('user_register',$datas);
	}

	// 退出登录
	public function logout(){
		Module_User::logout();
		Uri::redirect('/');
	}
	/*********************************************
 	* 以下第三方登录部分
 	*********************************************/
	
	//第三方登陆页面 
	public function loginapp(){
		if ($this->loginuser) {
			Uri::redirect(Uri::getPrevPage());
		}
		$params = Uri::getParams();
		$params = $params['params'];
		if (count($params)>0) {
			Module_ThirdLogin::gotoAppLogin($params[0]);
		}
	}

	// 授权成功回调页面
	public function appcallback(){
		$info = Module_ThirdLogin::appCallback();
		if ($info) {
			// 测试一下登陆
			$ret = Module_ThirdLogin::applogin($info['openid'],$info['appname']);
			if ($ret['code']>0) {
				Uri::redirect('/');
			}
			$this->view->load('user_applogin',$info);
		}else{
			showMessage('授权登录失败，请重试！','/user/login');
		}
	}
	// 用户移除授权时的回调页面
	public function appdelnotify(){
		
	}
	// 关联已有用户
	public function appbind(){
		if ($this->loginuser) {
			Uri::redirect(Uri::getPrevPage());
		}
		$datas = array();
		// 如果是提交
		if (isset($_POST['captcha'])) {
			$username = Uri::post('username');
			$password = Uri::post('password');
			$captcha = Uri::post('captcha');
			// 检查验证码
	 		if (Captcha::check($captcha)) {
	 			// 登陆
				$ret = Module_ThirdLogin::loginbind($username,$password);
				if ($ret['code']>0) {
					Uri::redirect(Uri::getPrevPage());
					// Uri::redirect('/');
				}
				$datas['error'] = $ret['msg'];
	 		}else{
	 			$datas['error'] = '验证码错误，请重新输入';
	 		}
		}
		$this->view->load('user_appbind',$datas);
	}

	// 完善资料生成新用户
	public function appnew(){
		if ($this->loginuser) {
			Uri::redirect(Uri::getPrevPage());
		}
		$datas = array();
		if (isset($_POST['username'])) {
			$username = Uri::post('username');
			$password = Uri::post('password');
			$email = Uri::post('email');
			// 注册绑定
			$ret = Module_ThirdLogin::registerAppUser($username,$password,$email);
			if ($ret['code']>0) {
				Uri::redirect(Uri::getPrevPage());
				// Uri::redirect('/');
			}else{
				$datas['error'] = $ret['msg'];
	 		}
		}
			// 检查验证码
		$this->view->load('user_appnew',$datas);
	}

	// 自动创建用户关联，以后完善
	public function appauto(){
		if ($this->loginuser) {
			Uri::redirect(Uri::getPrevPage());
		}
		$ret = Module_ThirdLogin::registerAppUser();
		if ($ret['code']>0) {
			Uri::redirect(Uri::getPrevPage());
			// Uri::redirect('/');
		}else{
			showMessage($ret['msg']);
		}
	}
}