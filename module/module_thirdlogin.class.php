<?php
/**
 * 用户第三方登陆模块统一管理类,纯静态类,有些地方用到了Module_User 模块
 * 新增一个第三方登陆只需要再数据库中增加一行记录(key为唯一标示)，修改相应的后台编辑代码（必填项），
 * 然后在本类的gotoAppLogin和appCallback中增加相应的处理模块就行了，
 * 其他功能自动实现
 */
final class Module_ThirdLogin{
	const APPID = 13;
	const APPNAME = '第三方登陆模块';
	const TNAME = 't_app_user';	//第三方登陆用户关联表
	const TAPPTOKEN = 't_app_token';  //第三方登陆授权信息表
	const TAPPCFG = 't_app_config';	//第三方登陆配置表
	const N_LOGINKEY = 'xm_apploginkey';	//session key  登陆类型key
	const N_LOGINCFG = 'xm_applogincfg';	//session key  登陆配置
	const N_LOGININFO = 'xm_applogininfo';	//session key  登陆返回的信息
	/**************第三方登陆前端业务逻辑部分*************/
	
	// 根据第三方配置登陆app的key去登陆
	static function gotoAppLogin($key){
		// 先清空一下缓存
		self::clearAppSession();

		$ret = self::getAppCfgByKey($key);
		// 不判断配置是否生效，前端自己判断
		if ($ret['code']==1) {
			// 记录下当前登陆的方式
			Helper::setSession(self::N_LOGINKEY,$key);
			$cfg = $ret['data'];
			switch ($cfg['key']) {
				case 'qq':
					// qq登陆重新写了API/Recorder.class.php 中的readInc方法，配置从session中取，不再从文件中取,
					// qqConnectAPI.php 注释掉了sessin_start();
					// 另外errorReport常配置为true
					$cfg['errorReport'] = true;
					Helper::setSession(self::N_LOGINCFG,$cfg);
					include_once(BASEPATH."/datas/qqconnect2.1/API/qqConnectAPI.php");
					$qc = new QC();
					$qc->qq_login();
					break;
				case 'weibo':
					include_once(BASEPATH.'/datas/weibo/saetv2.ex.class.php');
					$o = new SaeTOAuthV2($cfg['appid'] , $cfg['secret'] );
					$code_url = $o->getAuthorizeURL($cfg['callback']);
					header('Location:'.$code_url);
					break;
				case 'douban':
					include_once(BASEPATH.'/datas/douban/doubanOA2.class.php');
					$o = new DoubanOA2($cfg['appid'],$cfg['secret'],$cfg['callback']);
					$code_url = $o->getAuthorizeURL($cfg['scope']);
					header('Location:'.$code_url);
					break;
				case 'renren':
					include_once(BASEPATH.'/datas/renren/rennclient/RennClientBase.php');
					$rennClient = new RennClientBase($cfg['appkey'], $cfg['secret'] );
					$rennClient->setDebug (false);
					// 最后一个参数可以强迫每次重新登陆
					$code_url = $rennClient->getAuthorizeURL ($cfg['callback'], 'code', 'renren',null,true);
					header('Location:'.$code_url);
					break;
			}
			exit;
		}else{
			throw new Exception("第三方登陆信息不存在或者没有生效");
		}
	}
	// 第三方授权回调处理,将各种第三方授权返回信息统一成一种格式缓存
	static function appCallback(){
		$appname = Helper::getSession(self::N_LOGINKEY);
		$info = array('appname'=>$appname);
		switch ($appname) {
			case 'qq':
				include_once(BASEPATH."/datas/qqconnect2.1/API/qqConnectAPI.php");
				$qc = new QC();
				$token = $qc->qq_callback();
				$openid = $qc->get_openid();
				if(!empty($openid)){
					$qc = new QC($token['access_token'],$openid);
					$ret = $qc->get_user_info();
					if ($ret['ret']==0) {
						$info['openid']=$openid;
						$info['token']=$token['access_token'];
						$info['expires_in']=$token['expires_in'];
						$info['token']=$token['access_token'];
						$info['refresh_token']=$token['refresh_token'];
						$info['nickname']=$ret['nickname'];
						$info['gender']=$ret['gender'];
						$info['cover']=$ret['figureurl_qq_2'];
					}
				}
				break;
			case 'weibo':
				if (isset($_REQUEST['code'])) {
					include_once(BASEPATH.'/datas/weibo/saetv2.ex.class.php' );
					$ret = self::getAppCfgByKey($appname);
					$appkey = $ret['data']['appid'];
					$appsecret = $ret['data']['secret'];
					$o = new SaeTOAuthV2($appkey,$appsecret);

					$keys = array();
					$keys['code'] = $_REQUEST['code'];
					$keys['redirect_uri'] = $ret['data']['callback'];
					try {
						$token = $o->getAccessToken( 'code', $keys ) ;
						if ($token) {
							$c = new SaeTClientV2( $appkey , $appsecret , $token['access_token'] );
							$uid_get = $c->get_uid();
							$uid = $uid_get['uid'];
							$umessage = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息

							$info['openid']= $uid;
							$info['token']= $token['access_token'];
							$info['expires_in']= $token['expires_in'];
							$info['nickname']=$umessage['screen_name'];
							$info['gender']=$umessage['gender']=='m'?'男':'女';
							$info['cover']=$umessage['avatar_large'];
							setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
						}
					} catch (OAuthException $e) {}
				}
				break;
			case 'douban':
				if (isset($_GET['code'])) {
					include_once(BASEPATH.'/datas/douban/doubanOA2.class.php');
					$ret = self::getAppCfgByKey($appname);
					$cfg = $ret['data'];
					$o = new DoubanOA2($cfg['appid'],$cfg['secret'],$cfg['callback']);

					$token = $o->getAccessToken($_GET['code']);
					if (!empty($token) && isset($token['access_token'])) {
						$info['token']= $token['access_token'];
						$info['expires_in']=$token['expires_in'];
						$info['refresh_token']=$token['refresh_token'];
						
						$umessage = $o->get('user/~me');
						if ($umessage) {
							$info['openid']= $umessage['uid'];
							$info['nickname']=$umessage['name'];
							$info['cover']=$umessage['avatar'];
						}
					}
				}
				break;
			case 'renren':
				if (isset($_GET['code'])) {
					$ret = self::getAppCfgByKey($appname);
					$cfg = $ret['data'];

					include_once(BASEPATH.'/datas/renren/rennclient/RennClientBase.php');
					include_once(BASEPATH.'/datas/renren/rennclient/service/UserService.php');
					$rennClient = new RennClientBase($cfg['appkey'], $cfg['secret'] );
					// $rennClient->setDebug (true);
					$keys = array ();
					try {
						// 根据code来获得token,下面两个方法修改了一下返回token,以前不返回
						$token = $rennClient->authWithAuthorizationCode ($_GET['code'], $cfg['callback'] );
						// $token = $rennClient->authWithClientCredentials();
						if ($token) {
							// 获得用户接口
							$user_service = new UserService ($rennClient, $token);
							$user = $user_service->getUser();	//不带id获取当前用户的信息
							$info['openid']= $user['id'];
							$info['token']= $token->accessToken;
							$info['refresh_token']= $token->refreshToken;
							$info['nickname']=$user['name'];
							$info['gender']=$user['basicInformation']['sex']=='MALE'?'男':'女';
							$info['cover']=$user['avatar'][1]['url'];
						}
					} catch ( RennException $e ) {
						// var_dump ( $e );
					}
				}
				break;
			default:
				break;
		}
		if (count($info)==1) {
			// 第三方授权失败清空登陆缓存
			self::clearAppSession();
			return false;
		}else{
			// 更新第三方授权信息
			$ret = self::setThirdAccountToken($info);
			$info['appid'] = $ret['data'];
			Helper::setSession(self::N_LOGININFO,$info);
		}
		return $info;
	}
	// 使用openid和appname登陆（如果关联过则登陆成功）
	static function applogin($openid,$appname){
		$ret = array('code'=>-1,'msg'=>'没有关联账号');
		// 检查是否有已经关联的账号
		$users = self::getAppUsers($openid,$appname);
		if ($users && $users['total']>0) {
			// 取第一个关联用户id登陆
			$uitem = $users['list'][0];
			$ret = Module_User::loginById($uitem['userid']);
			if ($ret['code']>0) {
				// 更新第三方登陆信息
				$upattr = array(
					'lasttime'=>$_SERVER['REQUEST_TIME'],
					'lastip'=>Helper::getClientIp());
				$GLOBALS['db']->update(self::TNAME,$upattr,array('id'=>$uitem['id']));
			}
			return $ret;
		}
		return $ret;
	}
	// 注册账户，并与第三方token生成关联（不检查关联是否重复），并登陆，
	// $username,$password为空的情况自动注册临时账号，临时密码：ssms_tmp_123456789，注册的临时用户(status = 2)。后期完善时可以提示修改：邮箱，密码，昵称
	// 不为空的情况生成实际用户,状态默认1
	static function registerAppUser($username='',$password='',$email='',$conds=array()){
		$ret = array('code'=>-1,'msg'=>'');

		$info = Helper::getSession(self::N_LOGININFO);
		if (empty($info)) {
			$ret['msg'] = '登录信息已经过期';
			return $ret;
		}
		$openid = $info['openid'];
		$appname = $info['appname'];
		$t =$_SERVER['REQUEST_TIME'];
		$ip = Helper::getClientIp();
		// 判断自注册
		if (empty($username)) {
			$conds['status'] =2;
		}
		$username = empty($username)?$appname.'_'.$t:$username;
		$password = empty($password)?'tmp_123456789':$password;
		$email = empty($email)?$username.'@anonymous.com':$email;
		$ruser = Module_User::register($username,$password,$email,$conds);
		if ($ruser['code']>0) {
			// 生成关联信息
			$uid = $ruser['data'];
			//根据openid和appname检索是否存在该授权信息
			$tokens = $GLOBALS['db']->select(self::TAPPTOKEN,array('openid'=>$openid,'appname'=>$appname));
			if ($tokens['total']>0) {
				$token = $tokens['list'][0];
				if ($ritemid = self::bindAppUser($uid,$username,$token['id'],$appname)) {
					// 自动登陆
					Module_User::loginById($uid);

					$ret['code'] = 1;
					$ret['data'] = $uid;
				}else{
					$ret['msg'] = '关联新用户失败';
				}
			}else{
				$ret['msg'] = '没有找到第三方token信息';
			}
		}else{
			$ret['msg'] = $ruser['msg'];
		}
		return $ret;
	}
	// 关联现有账号登陆
	static function loginbind($username,$password){
		$ret = array('code'=>-1,'msg'=>'');

		$loginuser = Module_User::login($username,$password);
		if ($loginuser['code']>0) {
			$user = $loginuser['data'];
			$info = Helper::getSession(self::N_LOGININFO);
			// dump($info);die();
			$ritemid = self::bindAppUser($user['id'],$user['username'],$info['appid'],$info['appname']);
			if ($ritemid>0) {
				$ret['code'] = 1;
				$ret['data'] = $user['id'];
			}else{
				$ret['msg'] = '关联失败';
			}
		}else{
			$ret['msg'] = $loginuser['msg'];
		}
		return $ret;
	}

	// 获取开启的第三方登陆列表html
	static function getThirdLoginHTML(){
		$html = '';
		$items = $GLOBALS['db']->select(self::TAPPCFG,array('status'=>1),false,'order by idx');
		if ($items['total']>0) {
			foreach ($items['list'] as $item) {
				$html.='<a href="'.Uri::build('user','loginapp',array($item['key'])).'" ><img src="/static/dist/img/'.$item['key'].'24X24.png" alt="'.$item['name'].'"  title="'.$item['name'].'"/></a>';
			}
		}
		return $html;
	}
	/************************第三方登陆后端配置部分*************/
	// 获取第三方登陆配置列表
	static function getAppCfgs(){
		return $GLOBALS['db']->select(self::TAPPCFG);
	}
	// 设置第三方登陆配置，(**自动刷新缓存)
	static function setAppCfg($arrs,$id){
		
		$ret = array('code'=>-1,'msg'=>'');
		if ($arrs['status']==1) {
			$id = intval($id);
			// 必填项，判断
			$check = FormVerify::rule(
				array(FormVerify::must($arrs['name']),'名称不能为空'),
				array(FormVerify::must($arrs['appid']),'必填信息不能为空'),
				array(FormVerify::must($arrs['appkey']) || FormVerify::must($arrs['secret']),'必填信息不能为空'),
				array(FormVerify::must($arrs['callback']),'回调地址不能为空')
				);
			if ($check!==true) {
				$ret['msg'] = $check;
			}	
		}
		if (empty($ret['msg'])) {
			$GLOBALS['db']->update(self::TAPPCFG,$arrs,array('id'=>$id));
			$ret['code'] = 1;
			$ret['msg'] = '编辑成功';
			// 更新缓存
			$cachekey = 'xm_appcfg_'.$arrs['key'];
			$cachgroup = $GLOBALS['config']['db']['group'];
			$arrs['id'] = $id;
			$GLOBALS['cache_file']->delete($cachgroup,$cachekey);
		}
		return $ret;
	}
	// 根据key获取开启的第三方登陆配置,(**带缓存)
	static function getAppCfgByKey($key){
		$cachekey = 'xm_appcfg_'.$key;
		$cachgroup = $GLOBALS['config']['db']['group'];
		// 检查缓存
		if(!($ret = $GLOBALS['cache_file']->get($cachgroup,$cachekey))){
			$ret = array('code'=>-1,'msg'=>'');
			$query = $GLOBALS['db']->query('select * from '.self::TAPPCFG.' where status>0 and `key`="'.$key.'"');
			if ($item = $GLOBALS['db']->fetch_array($query)) {
				$ret['code'] = 1;
				$ret['data'] = $item;
			}else{
				$ret['msg'] = '配置信息不存在';
			}
			$GLOBALS['cache_file']->set($cachgroup,$cachekey,$ret);
		}
		return $ret;
	}
	/*******************************************第三方登陆用户信息部分*************/
	/**
	 * 编辑第三方用户授权信息，有则更新，无则新增，与本站用户无关
	 * @param array $attrs 更新的参数列表
	 * @return 第三方授权信息表id
	 */
	static function setThirdAccountToken($attrs){
    $ret = array('code' => -1, 'msg' => '');
		$t = $_SERVER['REQUEST_TIME'];
		$ip = Helper::getClientIp();

		//根据openid和appname检索是否存在该授权信息
		$tokens = $GLOBALS['db']->select(self::TAPPTOKEN,array('openid'=>$attrs['openid'],'appname'=>$attrs['appname']));
		$id = $tokens['total']>0?$tokens['list'][0]['id']:0;

		$attrs['lasttime'] = $t;
		$attrs['lastip'] = $ip;
		if (!empty($id)) {
			if($result = $GLOBALS['db']->update(self::TAPPTOKEN,$attrs,array('id'=>$id))){
				$ret['code'] = 1;
				$ret['data'] = $id;
			}else{
				$ret['msg'] = '更新信息失败';
			}
		}else{
			$attrs['addtime'] = $t;
			$attrs['addip'] = $ip;
			if($newid = $GLOBALS['db']->insert(self::TAPPTOKEN,$attrs)){
				$ret['code'] = 1;
				$ret['data'] = $newid;
			}else{
				$ret['msg'] = '新增信息失败';
			}
		}
		return $ret;
	}
	/**
	 * 获取当前第三方用户授权信息关联的用户列表
	 * @param string $openid  qq的openid,weibo的id
	 * @param string $appname  第三方授权标志  qq|weibo...
	 */
	static function getAppUsers($openid,$appname){
		$users = array();
		// 先检查该第三方授权信息是都在token表中的id
		$appid = $GLOBALS['db']->result('select id from `'.self::TAPPTOKEN.'` where openid="'.$openid.'" and appname="'.$appname.'"');
		if ($appid>0) {
			return $GLOBALS['db']->select(self::TNAME,array('appid'=>$appid,'appname'=>$appname));
		}
		return false;
	}
	/**
	 * 关联token和网站用户
	 * @param int $uid 关联的用户id
	 * @param string $uname 关联的用户名
	 * @param int $appid 第三方token表的id
	 * @param string $appname 关联的第三方类型qq|weibo...
	 * @return  int 新增关联表的id
	 */
	static function bindAppUser($uid,$uname,$appid,$appname){
		$t = $_SERVER['REQUEST_TIME'];
		$ip = Helper::getClientIp();
		$id = $GLOBALS['db']->insert(self::TNAME,array('userid'=>$uid,
																					'username'=>$uname,
																					'appid'=>$appid,
																					'appname'=>$appname,
																					'addtime'=>$t,
																					'addip'=>$ip,
																					'lasttime'=>$t,
																					'lastip'=>$ip,
																					));
		return $id;
	}
	
	/*************************内部方法****************************/
	// 清空第三方登陆过程中生成的所有缓存
	private static function clearAppSession(){
		unset($_SESSION[self::N_LOGINKEY]);
		unset($_SESSION[self::N_LOGINCFG]);
		unset($_SESSION[self::N_LOGININFO]);
	}
}