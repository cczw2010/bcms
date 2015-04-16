<?php
// controller
// 目前都是一些测试
Class Home{
	// 首页打印一些测试数据
	public function index(){
		$datas = array('STYLES'=>array('/datas/uploadify/uploadify.css',
																		'/datas/datepicker/css/default.css'),
									'SCRIPTS'=>array('/datas/uploadify/jquery.uploadify.min.js',
																		'/static/dist/js/citys.min.js'
																		)
									);
 		$this->view->load('homeindex',$datas);
	}

	public function test(){
		
	}
	// 显示phpinfo
	public function info(){
		phpinfo();
	}
	// mysql事务，ok
	public function transaction(){
		// $GLOBALS['db']->transBegin();
		// $ret = $GLOBALS['db']->query('INSERT INTO `t_order` (`userid`,`orderno`,`totalfee`,`paymentid`,`shippingid`, `addressid`,`createdate`,`updatedate`) VALUES (1, "E00010", 100.10,2,12,22,123121123,12323499)');
		// $ret1 = $GLOBALS['db']->query('INSERT INTO `t_order` (`userid`,`orderno`,`totalfee`,`paymentid`,`shippingid`, `addressid`,`createdate`,`updatedate`) VALUES (2, "E00012", 120.10,2,12,22,123121123,12323499)');
		// $GLOBALS['db']->query('delete from t_order');
		// $GLOBALS['db']->transCommit();
		// $GLOBALS['db']->transRollback();
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
