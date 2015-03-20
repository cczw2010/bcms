<?php
// controller
// 目前都是一些测试
Class Home{
	// 首页打印一些测试数据
	public function index(){
		// dump($GLOBALS);
		$data = $GLOBALS['db']->getdata('t_user',array('status'=>1,'types'=>1),'','',1,20,60);
		// $ret = Module_Notify::setItem(array('userid'=>3,'objid'=>1,'objtype'=>'test','msg'=>'这是测试'));
		
		// $ret = Module_Notify::setItem(array('status'=>1),30);
		// dump($ret);

		// $items = Module_Notify::getItems();
		// dump($items);
		// $ret = Module_User::addFollow(3,22);
		// $ret = Module_User::delFollow(3,22);
		// dump($ret);

		// $ret = Module_User::getFollowsCnt(array('uid=3'));
		// dump($ret);
		$datas = array('STYLES'=>array('/datas/uploadify/uploadify.css',
																		'/datas/datepicker/css/default.css'),
									'SCRIPTS'=>array('/datas/uploadify/jquery.uploadify.min.js',
																		'/static/dist/js/citys.min.js'
																		)
									);
		// $a = Helper::encodeString('123123','abc');
		// $b = base64_decode($a);
		// $c = Helper::decodeString($a,'abc');
		// dump($a,$b,$c);
		// $cates = Module_Area::getChildsByApp(0,0,0,array('status'=>1),-1);
		// dump($cates);
		// dump($_SERVER);
		// $ua = new Useragent();
		// dump($ua->is_mobile());
		// dump(Uri::getParams());
		
		// dump($GLOBALS);
		// $model_home = $this->model->load('model_home');
		// $datas = $model_home->getIndex();
		// dump($datas);

 		$this->view->load('homeindex',$datas);
	}

	public function test(){
		//改demo的功能是群发短信和发单条短信。（传一个手机号就是发单条，多个手机号既是群发）

		//您把序列号和密码还有手机号，填上，直接运行就可以了

		//如果您的系统是utf-8,请转成GB2312 后，再提交、
		//请参考 'content'=>iconv( "UTF-8", "gb2312//IGNORE" ,'您好测试短信[XXX公司]'),//短信内容

		$flag = 0;
		$params ='';
    //要post的数据 
		$argv = array( 
			'sn'=>'DXX-BBX-109-20287', ////替换成您自己的序列号
			'pwd'=>strtoupper(md5('DXX-BBX-109-20287'.'026660')), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
			'mobile'=>'13601044138',//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
			// 'content'=>iconv( "UTF-8", "UTF-8//IGNORE" ,'您好测试短信【玩票网】'),//短信内容
			'content'=>'您好测试短信-local-2【玩票网】',//短信内容
			'ext'=>'',		
			'stime'=>'',//定时时间 格式为2011-6-29 11:09:21
			'msgfmt'=>'',
			'rrid'=>''
		); 
		//构造要post的字符串 
		foreach ($argv as $key=>$value) { 
			if ($flag!=0) { 
				$params .= "&"; 
				$flag = 1; 
			}
			$params.= $key."="; 
			$params.= urlencode($value); 
			$flag = 1; 
		} 
		$length = strlen($params); 
		//创建socket连接
		//http://sdk2.entinfo.cn:8061/webservice.asmx?op=mdsmssend
		$fp = fsockopen("sdk.entinfo.cn",8061,$errno,$errstr,10) or exit($errstr."--->".$errno); 
		//构造post请求的头 
		$header = "POST /webservice.asmx/mdsmssend HTTP/1.1\r\n"; 
		$header .= "Host:sdk.entinfo.cn\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
		$header .= "Content-Length: ".$length."\r\n"; 
		$header .= "Connection: Close\r\n\r\n"; 
		//添加post的字符串 
		$header .= $params."\r\n"; 
		//发送post的数据 
		fputs($fp,$header); 
		$inheader = 1; 
		while (!feof($fp)) { 
			$line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
			if ($inheader && ($line == "\n" || $line == "\r\n")) { 
			 $inheader = 0; 
			} 
			if ($inheader == 0) { 
			  // echo $line; 
			} 
		} 
		//<string xmlns="http://tempuri.org/">-5</string>
		$line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
		$line=str_replace("</string>","",$line);
		$result=explode("-",$line);
		// echo $line."-------------";
		if(count($result)>1)
		echo '发送失败返回值为:'.$line.'。请查看webservice返回值对照表';
		else
		echo '发送成功 返回值为:'.$line;  
	}
	// 显示phpinfo
	public function info(){
		phpinfo();
	}
	// mysql事务，ok
	public function transaction(){
		// $GLOBALS['db']->trans_begin();
		// $ret = $GLOBALS['db']->query('INSERT INTO `t_order` (`userid`,`orderno`,`totalfee`,`paymentid`,`shippingid`, `addressid`,`createdate`,`updatedate`) VALUES (1, "E00010", 100.10,2,12,22,123121123,12323499)');
		// $ret1 = $GLOBALS['db']->query('INSERT INTO `t_order` (`userid`,`orderno`,`totalfee`,`paymentid`,`shippingid`, `addressid`,`createdate`,`updatedate`) VALUES (2, "E00012", 120.10,2,12,22,123121123,12323499)');
		// $GLOBALS['db']->query('delete from t_order');
		// $GLOBALS['db']->trans_commit();
		// $GLOBALS['db']->trans_rollback();
	}
	// 测试其支付，成功但是回调未开发完成
	public function pay(){
		//Module_Payment::gotopay('alipay','E100010','测试订单','0.01','这是简介','http://webapper.nat123.net/home/show');
	}
	// 测试发邮件  ok
	public function mail(){
		// $ret = Module_Mail::send(array('2794303057@qq.com','71752352@qq.com'),'这是一个测试','<hr><div stlye="color:red">这是一个测试，我靠</div>',array('datas/phpmailer/examples/images/phpmailer.png'));
		// dump($ret);
	}
}
