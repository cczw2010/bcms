<?php
// 用户管理类
class MUser{
	const ERRNAME = '_x_errmsg';
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

		$ret = Module_Group::getGroups();
		$datas['groups'] = $ret['list'];
		$datas['pages'] = multiPages($page,$psize,$datas['users']['total'],$pageParams,true);

		return $datas;
	}
	// 用户编辑
	public function uedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
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
			Uri::build('manage','pusers',false,true);
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
				Helper::setSession(self::ERRNAME,'没有相关用户信息');
				Uri::build('manage','pusers',false,true);
			}
		}

		$ret = Module_Group::getGroups();
		$datas['groups'] = $ret['list'];
		return $datas;
	}
	// 个人信息修改
	public function ueditinfo(){
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
				Helper::setSession(self::ERRNAME,'没有相关用户信息');
				Uri::build('manage','pusers',false,true);
			}
		}

		$ret = Module_Group::getGroups();
		$datas['groups'] = $ret['list'];
		return $datas;
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
		return $datas;
	}
	// 删除用户
	public function udel(){
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
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
		Uri::build('manage','pusers',false,true);
	}
	// 用户收货地址
	public function uaddress(){
		$uid = Uri::get('id',0);
		$address = Module_User::getAdresss($uid);

		$datas=array('items'=>$address);
		return $datas;
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
		return $datas;
	}
	// 用户组
	// $type 分组类型
	public function groups($type=0){
		$datas = array();
		$datas['groups'] = Module_Group::getGroups(array('types'=>$type));
		$datas['modules'] = $this->getModules1();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		return $datas;
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
			$page = $types ==0?'pugroup':'pusergroup';
			Uri::build('manage',$page,false,true);
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
				Uri::build('manage','pugroup',false,true);
			}
		}
		$datas['modules'] = $this->getModules1();
		return $datas;
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
	/**
	 * 自动获取当前所有的模块和模块下的页面
	 * modify by awen @2014-7-3  改为只返回管理员页面的权限
	 * @param boolean $m 是否包含方法一起返回 
	 */
	private function getModules($m=false){
		// $cpath = $GLOBALS['path_app'].'/'.$GLOBALS['config']['folder_c'];
		// $files = SFile::getPathFiles($cpath,'php');
		$modules = array();
		// foreach ($files as $file) {
		// 	$file = basename($file,'.php');
		// 	$clsname = ucfirst($file);
		// 	if ($m) {
				// include_once($cpath.'/'.$file.'.php');
				$refclass = new ReflectionClass('Manage');
				$methods = $refclass->getMethods(ReflectionMethod::IS_PUBLIC);
				$modules['Manage'] = $methods;
			// }else{
			// 	$modules[] = $clsname;
			// }
		// }
		return $modules;
	}
	//中文说明
	private function getModules1(){
		return array('manage'=>array(
															'index'=>'首页',
															'pinfo'=>'系统信息',
															'pcache'=>'缓存处理',
															'plogs'=>'log日志',
															'pusers'=>'用户列表',
															'pugroup'=>'管理员分组',
															'pusergroup'=>'用户分组',
															'puserlog'=>'登录日志',
															'particles'=>'文章列表',
															'particlecate'=>'文章分类',
															'particlecomm'=>'评论管理',
															'pbrands'=>'品牌列表',
															'pproducts'=>'商品列表',
															'pproductcate'=>'商品分类',
															'pproductcomm'=>'评论管理',
															'porders'=>'订单列表',
															'pcitys'=>'区域管理',
															'pverify'=>'敏感词汇',
															'pappcfgs'=>'第三方登陆',
															'ppayments'=>'支付管理',
															'pmails'=>'SMTP邮件',)
			);
	}
}