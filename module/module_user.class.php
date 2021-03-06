<?php
/**
 * 用户模块统一管理类,纯静态类
 */
final class Module_User{
	const TNAME = 't_user';
	const TADRESS = 't_user_address';
	const TFRIEND = 't_follow';

	const SESSION_KEY = '_s_user_';

	static public $statuss = array(0=>'锁定',1=>'正常',2=>'未完善');
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
			$ret = Module_User::getUser($_SESSION[self::SESSION_KEY]['id']);
			if ($ret['code']>0) {
				$_SESSION[self::SESSION_KEY] = $ret['data'];
			}else{
				Module_User::logout();
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
	/**
	 * 获取用户的地址列表
	 * @param  int $userid 用户id
	 * @return array
	 */
	static public function getAdresss($userid){
		return $GLOBALS['db']->select(self::TADRESS,array('userid'=>$userid),false,'order by isdefault desc,id desc',-1);
	}
	/**
	 * 删除地址
	 * @param  int $id  地址id
	 * @return null
	 */
	static public function delAdress($id){
		$GLOBALS['db']->delete(self::TADRESS,array('id'=>$id));
	}
	/**
	 * 编辑地址
	 * @param array $arrs 编辑的数据键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 返回$id
	 */
	static public function setAdress($arrs,$id){
		$ret = array('code'=> -1,'msg'=>'');
		// 要判断必填
		$check = FormVerify::rule(
			array(FormVerify::len($arrs['name'],2),'收货人不能少于2个字符'),
			array(FormVerify::must($arrs['province']),'省份不能为空'),
			array(FormVerify::must($arrs['city']),'城市不能为空'),
			array(FormVerify::must($arrs['area']),'地区不能为空'),
			array(FormVerify::len($arrs['detail'],4),'请认真填写详细地址'),
			array(FormVerify::must($arrs['mobile'])||FormVerify::must($arrs['phone']),'手机号码或者固定电话最少填写一个')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		if ($id>0) {
			// 判断是否存在
			$cnt = $GLOBALS['db']->result('select count(*) from '.self::TADRESS.' where id='.$id);
			if ($cnt==0) {
				$ret['msg'] = '要编辑的内容不存在';
			}else{
				//更新数据
				$result = $GLOBALS['db']->update(self::TADRESS,$arrs,array('id'=>$id));
				if (!$result) {
					$ret['msg'] = '更新失败';
				}
			}
		}else{
			$id = $GLOBALS['db']->insert(self::TADRESS,$arrs);
			if (empty($id)) {
				$ret['msg'] = '新增失败';
			}
		}
		if ($id>0 && empty($ret['msg'])) {
			$ret['code'] = 1;
			$ret['data'] = $id;
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
		return empty($uid)?'default':'album_'.$uid;
	}
	////////////////////////////////////////////关注部分
	//判断是否关注
	static public function isFollow($ouid,$fuid){
		$cnt = $GLOBALS['db']->result('select count(*) from '.self::TFRIEND.' where uid='.$ouid.' and fuid='.$fuid);
		return $cnt>0;
	}
	/**
	 * 关注,逻辑会判断是否关注过，用户是否存在。
	 * 该方法同步更新用户基本表关注数被关注数
	 * @param $ouid  用户id
	 * @param $fuid  目标用户id
	 * @param string $nickname 给目标用户起的昵称，可为空
	 */
	static public function addFollow($ouid,$fuid,$nickname=''){
		$ret = array('code'=>-1,'msg'=>'');
		//参数检验
		$check = FormVerify::rule(
			array($ouid!=$fuid,'不能关注自己'),
			array(FormVerify::must($ouid),'用户id不能为空'),
			array(FormVerify::must($fuid),'目标用户不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		//判断是否关注过
		if (self::isFollow($ouid,$fuid)) {
			$ret['msg']= '您已经关注过';
			return $ret;
		}
		//判断目标用户是否存在
		$fuser = Module_User::getUser($fuid);
		if ($fuser['code']<0) {
			$ret['msg']= '目标用户不存在';
			return $ret;
		}
		$fuser = $fuser['data'];
		$params = array(
				'uid'=>$ouid,
				'fuid'=>$fuid,
				'funame'=>empty($fuser['nickname'])?$fuser['username']:$fuser['nickname'],
				'fnickname'=>$nickname,
				'createdate'=>$_SERVER['REQUEST_TIME'],
				'ip'=>Helper::getClientIp()
			);
		$id = $GLOBALS['db']->insert(self::TFRIEND,$params);
		if ($id>0) {
			//更新用户基本表关注数
			Module_User::modifyUser($ouid,array('followingnum=followingnum+1'));
			//更新用户基本表被关注数
			Module_User::modifyUser($fuid,array('followednum=followednum+1'));
			$ret['code'] = 1;
			$ret['data'] = $id;
		}else{
			$ret['msg']='操作失败';
		}
		return $ret;
	}
	/**
	 * 取消关注,逻辑会判断是否关注过
	 * 该方法同步更新用户基本表关注数被关注数
	 * @param $ouid  用户id
	 * @param $fuid  目标用户id
	 */
	static public function delFollow($ouid,$fuid){
		$ret = array('code'=>-1,'msg'=>'');
		if (empty($ouid)||empty($fuid)) {
			$ret['msg'] = '参数错误';
			return $ret;
		}
		//判断是否关注过
		if (!self::isFollow($ouid,$fuid)) {
			$ret['msg']= '您并未关注过该用户';
			return $ret;
		}
		$result = $GLOBALS['db']->delete(self::TFRIEND,array('uid'=>$ouid,'fuid'=>$fuid));
		if ($result) {
			//更新用户基本表关注数
			Module_User::modifyUser($ouid,array('followingnum=followingnum-1'));
			//更新用户基本表被关注数
			Module_User::modifyUser($fuid,array('followednum=followednum-1'));
			$ret['code'] = 1;
		}else{
			$ret['msg']='操作失败';
		}
		return $ret;
	}
	/**
	 * 批量删除(取消)关注，该方法不会处理用户基本表中的关注被关注数
	 * @param $conds 删除条件数组,不能为空,防止清空
	 * @return array
	 */
	static public function delFollows($conds){
		$ret = array('code'=>-1,'msg'=>'');
		if (empty($conds)) {
			$ret['msg'] = '条件不能为空';
		}else{
			$GLOBALS['db']->delete(self::TFRIEND,$conds);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}
		return $ret;
	}
	/**
	 * 获取关注(被关注)列表
	 * @param  array	$cond 查询条件数组或者条件字符串（不带where）
	 * @param  string $orderby 排序条件字符串
	 * @param  int 		$page 页码（-1代表全部）
	 * @param  int 		$psize 每页数量
	 * @return array
	 */
	static public function getFollows($cond=array(),$orderby='',$page=1,$psize=10){
		return $GLOBALS['db']->select(self::TFRIEND,$cond,'id',$orderby,$page,$psize);
	}
	/**
	 * 获取关注(被关注)数量
	 * @param  array	$cond 查询条件数组或者条件字符串（不带where）
	 * @return 数量
	 */
	static public function getFollowsCnt($cond=array()){
		$where = $GLOBALS['db']->buildWhere($cond);
		$cnt = $GLOBALS['db']->result('select count(*) from '.self::TFRIEND.$where);
		return $cnt==null?0:$cnt;
	}
}