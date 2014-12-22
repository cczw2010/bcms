<?php
// controller
// 微信公众平台
define("TOKEN", "cczw_token");
define("TIMEOUT", 600);  //流程过期时间秒
Class Weixin{
	private $wxobj=null;
	function __construct(){
		
	}
	public function index(){
		include_once BASEPATH."/datas/weixin/Wechat.class.php";
		$options = array(
			'token'=>TOKEN, //填写你设定的key
			'encodingaeskey'=>'iHcSfRW50PKSvPG0DG8yVcEOEa88iMrXNfYGXNZU6IU', //填写加密用的EncodingAESKey
			'appid'=>'', //填写高级调用功能的app id, 请在微信开发模式后台查询
			'appsecret'=>'', //填写高级调用功能的密钥
			'partnerid'=>'', //财付通商户身份标识，支付权限专用，没有可不填
			'partnerkey'=>'', //财付通商户权限密钥Key，支付权限专用
			'paysignkey'=>'' //商户签名密钥Key，支付权限专用
			);
		$wxobj = new Wechat($options); //创建实例对象
		if($_GET["echostr"]){
			$wxobj->valid();
			die();
		}
		$type = $wxobj->getRev()->getRevType();
		$data = $wxobj->getRevData();
		//logResult(json_encode($data));
		$openid = $wxobj->getRevFrom();		//user openid
		// $ctime = $wxobj->getRevCtime();
		// $id = $wxobj->getRevID();
		$renzheng = new Renzheng($openid);
		$status = $renzheng->getStatus();
		logResult('>>>>>>>>>>>>>>>>>>>>当前流程状态：status='.$status);
		//如果认证流程已经开始,这里模拟初始化，后期用url申请初始化
		if ($type == Wechat::MSGTYPE_TEXT && strcmp($wxobj->getRevContent(),"实名")==0) {
				logResult('初始化流程开始');
				$renzheng->init();
				$wxobj->text(Renzheng::$langs['needphone'])->reply();
				die();
		}
		//如果已经走入认证流程
		if ($status>Renzheng::STATUS_NONE) {
			logResult('可进入认证流程:'.$type);
			if ($type == Wechat::MSGTYPE_TEXT && strcmp($wxobj->getRevContent(),"0")==0) {
				logResult('收到退出命令，退出流程');
				$renzheng->finish();
				$wxobj->text(Renzheng::$langs['exit'])->reply();
			}else{
				switch($status){
					case Renzheng::STATUS_PHONE:
						logResult('验证手机号流程>>');
						if ($type == Wechat::MSGTYPE_TEXT) {
							$content = $wxobj->getRevContent();
							$ret = $renzheng->verifyPhone($content);
							logResult('验证手机号结果:'.json_encode($ret));
							$wxobj->text($ret['msg'])->reply();
						}else{
							$wxobj->text(Renzheng::$langs['needphone'])->reply();
						}
					break;
					case Renzheng::STATUS_RIDCARD:
						logResult('上传身份证流程>>');
						if ($type == Wechat::MSGTYPE_IMAGE) {
							$mediaid = $data['MediaId'];
							$picurl = $data['PicUrl'];
							// $pic = $wxobj->getMedia($data['MediaId']);
							// if($pic==false) {
							// 	logResult('get media false');
							// 	return false;
							// 	//下载成功
							// }else{
							// }
							$ret = $renzheng->recognizeIDCard($picurl);
							logResult('解析身份证结果:'.json_encode($ret));
							$wxobj->text($ret['msg'])->reply();
						}else{
							$wxobj->text(Renzheng::$langs['needidcard'])->reply();
						}
					break;
					case Renzheng::STATUS_MIDCARD:
						logResult('确认身份证解析结果流程>>');
						if ($type == Wechat::MSGTYPE_TEXT) {
							$addr = $wxobj->getRevContent();
							if (strcmp($addr, "1")==0) {//确认
								logResult('确认身份信息，并进行认证');
								$ret = $renzheng->confirmidcard();
								$wxobj->text($ret['msg'])->reply();
							}else{
								$ret = $renzheng->modifyidcard($addr);
								logResult('修改地址流程:'.json_encode($ret));
								$wxobj->text($ret['msg'])->reply();
							}
						}else{
							$wxobj->text(Renzheng::$langs['confirmidcard'])->reply();
						}
					break;
					case Renzheng::STATUS_REGISTER:

					break;
					case Renzheng::STATUS_DONE;
					break;
				}
			}
		}else{
			//非认证流程处理逻辑
			$wxobj->text('hello 你想干嘛？想实名认证的话请输入：实名')->reply();
		}
		exit;
	}
}
//////////////////////////////////实名认证逻辑类
Class Renzheng{
	const STATUS_NONE = 0;			//流程未开始
	const STATUS_PHONE = 1;			//手机号环节
	const STATUS_RIDCARD = 2;		//识别身份信息环节
	const STATUS_MIDCARD = 3;		//修改身份信息环节
	const STATUS_REGISTER = 4;	//去移动联通认证
	const STATUS_DONE = 5;			//认证成功
	static $langs = array(
		'needphone'=>'请输入您的手机号进行实名认证，认证期间输入0结束认证流程。',
		'needidcard'=>'请上传一张您的身份证照片',
		'confirminfo'=>'您当前信息为: ',
		'confirmidcard'=>"如果识别信息无误请输入1进行实名认证\n重新上传图片请输入2\n您也可以通过输入新的地址信息进行地址的修正.",
		'exit'=>'已退出认证流程.',
		'err_noinit'=>'实名认证尚未开始，请发送 实名 进入认证流程',
		'err_status'=>'请重新发起认证流程',
		'err_phone'=>'您输入的不是有效的手机号，请重新输入。',
		'err_phone_once'=>'您输入的手机号已经被认证过，认证流程退出。',
		'err_idcard'=>'您上传的不是有效的身份证图片，请重新上传。',
		'err_idcard_once'=>'您的身份证号已经被认证过，认证流程退出。',
		'err_addr'=>'请认真填写地址信息（不能小于8位）。',
		'err_txt'=>'不是有效的命令',
		'reg_success'=>'恭喜，实名认证成功！',
		'reg_error'=>'很抱歉，实名认证失败，发送1重试，0退出。'
	);
	private $phone = false;
	private $uidcard = false;
	private $openid = false;

	function __construct($openid){
		if (empty($openid)) {
			return false;
		}
		$this->openid = $openid;
		$status = $this->getStatus($openid);
		if ($status===false) {
			$this->setStatus(self::STATUS_NONE);
		}
	}
	//获取当前流程状态
	public function getStatus(){
		return $GLOBALS['cache_file']->get('weixin',$this->openid);
	}
	//设置当前流程状态
	public function setStatus($val){
		$GLOBALS['cache_file']->set('weixin',$this->openid,$val,TIMEOUT);
	}
	//初始化流程
	public function init(){
		$this->setStatus(self::STATUS_PHONE);
		$this->phone = false;
		$this->idcard = false;
	}
	//结束流程
	public function finish(){
		$this->setStatus(self::STATUS_NONE);
		$this->phone = false;
		$this->idcard = false;
	}
	//验证手机号方法
	public function verifyPhone($number){
		$ret = array('code'=>0,'msg'=>'');
		if (!$this->isMobile($number)) {
			$ret['msg'] = self::$langs['err_phone'];
			return $ret;
		}
		//去数据库比对,验证是否是可认证的手机号，包括是否本系统手机号，是否认证过，开关判断，
		
		//如果可认证
		$this->setStatus(self::STATUS_RIDCARD);
		$this->phone = $number;
		$ret['code'] = 1;
		$ret['msg'] = self::$langs['needidcard'];
		return $ret;
	}

	//识别身份证
	public function recognizeIDCard($img){
		$ret = array('code'=>0,'msg'=>'');
		//调用本地身份识别接口
		//失败 无效身份证idcard
		$infos = array(
			'name'=>'awen',
			'addr'=>'某地',
			'no'=>'130406198211161123'
			);

		//检查有效性
		if(!$this->isUId($infos['no'])){
			$ret['msg']= self::$langs['err_idcard'];
			return $ret;
		}
		//检查改身份证是否认证过
		
		//成功，返回结果给用户，让用户确认或更改
		$this->setStatus(self::STATUS_MIDCARD);
		$this->idcard = $infos;
		$ret['code'] = 1;
		$ret['msg'] = self::$langs['confirminfo'].
							"\n姓名:".$this->idcard['name'].
							"\n身份证号:".$this->idcard['no'].
							"\n地址:".$this->idcard['addr']."\n".
							self::$langs['confirmidcard'];
		return $ret;
	}
	//修改身份证地址信息
	public function modifyidcard($addr){
		$ret = array('code'=>0,'msg'=>'');
		if (mb_strlen($addr)<8) {
			$ret['msg'] = self::$langs['err_addr'];
			return $ret;
		}
		$this->idcard['addr'] = $addr;
		$ret['code']=1;
		$ret['msg']=self::$langs['confirminfo'].$this->idcard['addr'].
								"\n=================\n".
								self::$langs['confirmidcard'];//将修改后的识别信息发给用户
		return $ret;
	}
	//确认身份证信息并根据运营商信息进行接口实名认证
	public function confirmidcard(){
		$ret = array('code'=>0,'msg'=>'');
		$this->setStatus(self::STATUS_REGISTER);
		//吊起实名认证接口
		$result = true;
		if ($result) {
			$ret['code'] = 1;
			$this->setStatus(self::STATUS_DONE);
			$ret['msg'] = self::$langs['reg_success'];
		}else{
			$ret['msg'] = self::$langs['reg_error'];
		}
		return $ret;
	}

	//是否手机号
	public function isMobile($number){
		return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $number);
	}
	//验证身份证号
	public function isUId($no){
		return preg_match("/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i",$no);
	}

}
function logResult($word='') {
	$fp = fopen(BASEPATH."/log.txt","a");
	flock($fp, LOCK_EX) ;
	// fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	fwrite($fp,"\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}
