<?php
/**
 * 用户模块统一管理类,纯静态类
 */
final class Module_Manager{
	const TNAME = 't_manager';
	const TADRESS = 't_user_address';
	const TFRIEND = 't_follow';

	const SESSION_KEY = '_s_manager_';

	static public $statuss = array(0=>'锁定',1=>'正常');
	/**
	 * 登录,并更新登录信息
	 * @param  string $username 用户名
	 * @param  string $password 密码
	 * @return array
	 */
	static public function login($username,$password){
		$ret=array('code'=>-1,'msg'=>'');
		if (!empty($username) && !empty($password)) {
			// 过滤禁止注册的用户名包含的字符串,暂时为实现！！！！！后期必须加
			$password = md5($password);
			$users = $GLOBALS['db']->select(self::TNAME,array('username'=>$username,'password'=>$password));
			if ($users['pcnt']>0) {
				$user = $users['list'][0];
				if ($user['status']==0) {
					$ret['code'] = -2;
					$ret['msg'] = '用户被锁定.';
				}else{
					// 缓存到session中
					Helper::setSession(self::SESSION_KEY,$user);
					// 更新最新登陆信息
					$cip = Helper::getClientIp();
					$param = array(
							'lastip'=> $cip,
							'logincount = logincount+1',
							'lasttime'=>$_SERVER['REQUEST_TIME'],
						);
					$GLOBALS['db']->update(self::TNAME,$param,array('id'=>$user['id']));
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
	static public function loginById($userid){
		$ret=array('code'=>-1,'msg'=>'');

		$user=self::getUser($userid);
		if ($user['code']>0) {
			$user = $user['data'];
			if ($user['status']==0) {
				$ret['msg'] = '用户被锁定.';
			}else{
				Helper::setSession(self::SESSION_KEY,$user);
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
	/**
	 * 注册用户
	 * @param  string $username 必须项目，用户名
	 * @param  string $password 必须项目，密码
	 * @param  array  $data    其他附加项键值对
	 * @return array
	 */
	static public function register($username,$password,$data=array()){
		$ret=array('code'=>-1,'msg'=>'');
		//检查是否注册过用户名和邮箱
		if (self::checkUser('username = "'.$username.'"')>0) {
			$ret['msg'] = '很抱歉！用户名已被注册'; 	
		}else{
			$password = md5($password);
			$cip = Helper::getClientIp();
			$data['username'] = $username;
			$data['password'] = $password;
			$data['addip'] = $cip;
			$data['lastip'] = $cip;
			$data['addtime'] = $_SERVER['REQUEST_TIME'];
			$data['lasttime'] = $_SERVER['REQUEST_TIME'];
			$uid = $GLOBALS['db']->insert(self::TNAME,$data);
			if ($uid>0) {
				$ret['code'] = 1;
				$ret['data'] = $uid;
			}else{
				$ret['msg'] = '注册失败，请重新输入';
			}
		}
		return $ret;
	}
	/**
	 * 获取当前登陆的用户
	 */
	static public function getLoginUser(){
		return isset($_SESSION[self::SESSION_KEY])?$_SESSION[self::SESSION_KEY]:false;
	}
	/**
	 *手动刷新登陆用户缓存,比如修改用户密码，签名等的时候还是很有必要的
	 */
	static public function refreshLoginUser(){
		if (!empty($_SESSION[self::SESSION_KEY])) {
			$ret = Module_Manager::getUser($_SESSION[self::SESSION_KEY]['id']);
			if ($ret['code']>0) {
				$_SESSION[self::SESSION_KEY] = $ret['data'];
			}else{
				Module_Manager::logout();
			}
		}
	}
	/**
	 * 退出登陆
	 */
	static public function logout(){
		unset($_SESSION[self::SESSION_KEY]);
	}

	/**
	 * 检查符合某条件的信息被注册的次数，可用来校验信息是否已经被注册
	 * @return int
	 */
	static public function checkUser($cond=array()){
		$cnt = 0;
		if (count($cond)>0) {
			$vs = $GLOBALS['db']->select(self::TNAME,$cond);
			$cnt = $vs['pcnt'];
		}
		return $cnt;
	}
	/**
 	 * 根据id获取用户
 	 * @param  int $id 内容id
 	 * @return array
 	 */
	static public function getUser($id){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '用户不存在';
		}
		return $ret;
	}
	/**
 	 * 根据用户名获取用户
 	 * @param  string $username 用户名
 	 * @return array
 	 */
	static public function getUserByUsername($username){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where username="'.$username.'"');
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '用户不存在';
		}
		return $ret;
	}
	/**
	 * 获取用户数组
	 * @param  array $cond 根据条件数组获取用户
	 * @return array
	 */
	static public function getUsers($cond=array(),$order='',$page=1,$size=20){
		$cond = empty($cond)?array():$cond;
		return $GLOBALS['db']->select(self::TNAME,$cond,false,$order,$page,$size);
	}
	/**
	 * 修改用户基本表信息，不包括头像，头像使用下面的单独处理静态方法
	 * @param  int $uid  用户id
	 * @param  array $data 修改的数据键值对
	 * @return
	 */
	static public function modifyUser($uid,$data){
		return $GLOBALS['db']->update(self::TNAME,$data,array('id'=>$uid));
	}
	/**
	 * 删除用户（不删除地址，保留数据）
	 * @param  int $uid  用户id
	 * @return
	 */
	static public function delUser($uid){
		$ret = array('code'=>-1,'msg'=>'');
		$user = self::getUser($uid);
		if ($user['code']>0) {
			$GLOBALS['db']->query("delete from ".self::TNAME.' where id='.$uid);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}else{
			$ret['msg']=$user['msg'];
		}
		return $ret;
	}
	// 获取头像path和name
	static function getUserAvatarPath($uid){
	    $uid = sprintf("%09d", $uid);
	    $vs = array();
	    $vs['path'] = 'static/avatar/'.substr($uid, 0, 3).'/'.substr($uid, 3, 2).'/'.substr($uid, 5, 2);
	    $vs['name'] = substr($uid, -2);
	    return $vs;
	}
	// 获取头像url,
	// size: small , middle , big
	static public function getUserAvatar($uid,$size='small'){
		$vs = self::getUserAvatarPath($uid);
		$url = $vs['path'].'/'.$vs['name'].'_'.$size.'.jpg';
		if (!file_exists($url)) {
			$url = 'static/avatar/default.jpg';
		}
    return $url;
	}
	// 保存头像,只在头像目录下生成大中小三种图，不处理原图，
	// 值得注意的是后缀请统一保存为jpg
	// return boolean
	static public function saveAvatar($uid,$img){
		if (!is_file($img)) {
			return false;
		}
		$vs = self::getUserAvatarPath($uid);
		if (!is_dir($vs['path'])) {
			if (!SFile::mkdirs($vs['path'])) {
				return false;
			}
		}
		$small = SImage::csize($img,80);
		$middle = SImage::csize($img,120);
		$big = SImage::csize($img,200);
		// dump($small,$middle,$big);
		SFile::move($small,$vs['path'].'/'.$vs['name'].'_small.jpg');
		SFile::move($middle,$vs['path'].'/'.$vs['name'].'_middle.jpg');
		SFile::move($big,$vs['path'].'/'.$vs['name'].'_big.jpg');
		return true;
	}
	// 获取用户网站上的相册目录名称
	static public function getAlbumBase($uid){
		return empty($uid)?'manager/default':'manager/album_'.$uid;
	}
}