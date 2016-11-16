<?php
// controller
// 微信公众平台
Class Weixin{
	private $wxobj=null;
	private $wxmodel=null;
	function __construct(){
		// 初始化微信对象
		include_once BASEPATH."/datas/weixin/wechat.class.php";
		$this->wxobj = new Wechat($GLOBALS['config']['weixin']); //创建实例对象
		$this->wxmodel = $this->model->load('manage/model_weixin');
	}
	public function index(){
		if(!empty($_GET["echostr"])){
			logs(BASEPATH.'/log.txt',$_GET["echostr"]);
			$this->wxobj->valid();
			die();
		}
		$type = $this->wxobj->getRev()->getRevType();
		$data = $this->wxobj->getRevData();
		//logResult(json_encode($data));
		$openid = $this->wxobj->getRevFrom();		//user openid
		// $ctime = $this->wxobj->getRevCtime();
		// $id = $this->wxobj->getRevID();
		$msg = $type.'|'.json_encode($data);
		logs(BASEPATH.'/log.txt',$openid.'|'.$msg);
		$this->wxobj->text($msg)->reply();
		exit;
	}
}