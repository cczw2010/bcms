<?php
/**
 * 用户管理处理类，
 */
final class Model_User extends Model_Common{
	// public TNAME = 't_user';
	private $TLOG = 't_user_log';
	private $TADRESS = 't_user_address';
	private $TFRIEND = 't_follow';
	private $PASSPERFIX = 'bcms_'; // 密码加密前缀
	// 分组类型 0 管理员分组， 1 普通用户分组
	private $TYPE_USER = 1;
	private $TYPE_MANAGER = 0;

	static public $statuss = array(0=>'锁定',1=>'正常',2=>'未完善');

 	public function __construct($tname){
 		parent::__construct($tname);
 	}

 	/**
 	 * 对外开放初始化方法，不做成单例，因为本类可能被多个同类数据表调用
 	 * @param  [type] $tname [description]
 	 * @return [type]        [description]
 	 */
 	static public function init($tname){
 		return new self($tname);
 	}

 	/**
	 * 登录,并更新登录信息
	 * @param  string $username 用户名
	 * @param  string $password 密码
	 * @param  boolean $ismanager 是否以管理员身份登陆
	 * @return array
	 */
	public function login($username,$password,$ismanager=false){
		$ret=array('code'=>-1,'msg'=>'');
		if (!empty($username) && !empty($password)) {
			// 过滤禁止注册的用户名包含的字符串,后期必须加
			$password = md5($password);
			$users = $GLOBALS['db']->select($this->TNAME,array(
																											'username'=>$username,
																											'password'=>$password
																											));
			if ($users['pcnt']>0) {
				$user = $users['list'][0];
				if ($user['status']==0) {
					$ret['code'] = -2;
					$ret['msg'] = '用户被锁定.';
				}else{
					// 判断是否管理员,缓存到session中
					if ($ismanager) {
						if ($user['types'] == Module_Group::TYPE_MANAGER) {
							Helper::setSession('_s_manager',$user);
						}else{
							$ret['msg'] = '您不是管理员，请不要随便尝试.';
							return $ret;
						}
					}else{
						Helper::setSession('_s_user',$user);
					}
					// 更新最新登陆信息
					$cip = Helper::getClientIp();
					$param = array(
							'lastip'=> $cip,
							'logincount = logincount+1',
							'lasttime'=>$_SERVER['REQUEST_TIME'],
						);
					$GLOBALS['db']->update($this->TNAME,$param,array('id'=>$user['id']));
					// 更新用户日志
					$ua = new Useragent();
					$paramlog = array(
						'userid' => $user['id'],
						'username' => $user['username'],
						'addtime' => $_SERVER['REQUEST_TIME'],
						'ip' => $cip,
						'befrom' => $ua->is_mobile()?$ua->mobile():'www',
						'useragent' => $_SERVER['HTTP_USER_AGENT'],
						);
					$GLOBALS['db']->insert($this->TLOG,$paramlog);
					// dump($GLOBALS['db']->getLastSql());die();
					$ret['code'] = 1;
					$ret['data'] = $user;
				}
			}else{
				$ret['msg'] = '用户名或者密码不正确';
			}
		}else{
			$ret['msg'] = '用户名或者密码不能为空';
		}
		return $ret;
	}
	/**
	 * 将某用户id设置为登陆用户
	 */
	public function loginById($userid){
		$ret=array('code'=>-1,'msg'=>'');

		$user=self::getUser($userid);
		if ($user['code']>0) {
			$user = $user['data'];
			if ($user['status']==0) {
				$ret['msg'] = '用户被锁定.';
			}else{
				// 判断是否管理员,缓存到session中
				if ($user['types'] = Module_Group::TYPE_MANAGER) {
					Helper::setSession('_s_manager',$user);
				}else{
					Helper::setSession('_s_user',$user);
				}
				// 更新最新登陆信息
				$param = array(
						'lastip'=>Helper::getClientIp(),
						'logincount = logincount+1',
						'lasttime'=>$_SERVER['REQUEST_TIME']
					);
				$GLOBALS['db']->update(self::TNAME,$param,array('id'=>$user['id']));

				$ret['code'] = 1;
				$ret['data'] = $user;
			}
		}else{
			$ret['msg'] = $user['msg'];
		}
		return $ret;
	}
}