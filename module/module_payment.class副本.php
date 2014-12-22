<?php
// 支付方式通用类,支付类型再数据库中写死，只能修改不能删除
final class Module_Payment{
	const APPID = 8;
	const APPNAME = '支付模块';
	const TNAME = 't_payment';
	const alipayid = 1;			//支付宝直接到账id(数据库中的id)

	// 获取支付列表
	static function getItems($cond=array()){
		return $GLOBALS['db']->select(self::TNAME,$cond);
	}

	// 根据id获取支付配置，(**带缓存)
	static function getItem($id){
		$cachekey = 'xm_paymentcfg_'.$id;
		$cachgroup = $GLOBALS['config']['db']['group'];
		// 检查缓存
		if(!($datas = $GLOBALS['cache']->get($cachgroup,$cachekey))){
			$ret = array('code'=>-1,'msg'=>'');
			$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
			if ($item = $GLOBALS['db']->fetch_array($query)) {
				$ret['code'] = 1;
				$ret['data'] = $item;
			}else{
				$ret['msg'] = '支付方式不存在';
			}
			$GLOBALS['cache']->set($cachgroup,$cachekey,$ret);
		}else{
			$ret = $datas;
		}
		return $ret;
	}

	// 设置支付配置,只给后台用，所以不用太多判断，(**自动刷新缓存)
	// 每个支付方式都要实现相应的判断
	static function setItem($arrs,$id){
		$ret = array('code'=>-1,'msg'=>'');
		if ($arrs['status']==1) {
			$id = intval($id);
			switch ($id) {
				case self::alipayid:
					// 必填项，判断
					$check = FormVerify::rule(
						array(FormVerify::must($arrs['appid']),'合作者id不能为空'),
						array(FormVerify::must($arrs['appkey']),'安全检验码不能为空'),
						array(FormVerify::must($arrs['appaccount']),'商家账号不能为空')
						);
					if ($check!==true) {
						$ret['msg'] = $check;
					}
					break;
				default:
					break;
			}
		}
		if (empty($ret['msg'])) {
			$GLOBALS['db']->update(self::TNAME,$arrs,array('id'=>$id));
			$ret['code'] = 1;
			$ret['msg'] = '编辑成功';
			// 更新缓存
			$cachekey = 'xm_paymentcfg_'.$id;
			$cachgroup = $GLOBALS['config']['db']['group'];
			$arrs['id'] = $id;
			$GLOBALS['cache']->delete($cachgroup,$cachekey);
		}
		return $ret;
	}

	// 构建支付宝配置
	static function buildAlipayConf(){
		$alipay_config = array(
			//签名方式
			'sign_type'=> 'MD5',
			//字符编码格式 目前支持 gbk 或 utf-8
			'input_charset'=> 'utf-8',
			//ca证书路径地址，用于curl中ssl校验
			'cacert'=> '/datas/alipay/cacert.pem',
			//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
			'transport'=> 'http',
			//合作身份者id，以2088开头的16位纯数字
			'partner'=>'',
			//安全检验码，以数字和字母组成的32位字符
			'key'=>'',
			//（商家）合作者支付宝账号
			'WIDseller_email'=>'',
			);

		$ret = self::getItem(self::alipayid);
		if ($ret['code']=1) {
			$alipay_config['partner']	= $ret['data']['appid'];
			$alipay_config['key']	= $ret['data']['appkey'];
			$alipay_config['notify_url']	= $ret['data']['notifyurl'];
			$alipay_config['return_url']	= $ret['data']['returnurl'];
			$alipay_config['WIDseller_email']    = $ret['data']['appaccount'];
		}
		return $alipay_config;
	}

	/**
	 * 支付宝去支付
	 * @param  string $tradeno     商户订单号,必填
	 * @param  string $subject     订单名称，必填
	 * @param  int 		$totalfee    付款金额，必填
	 * @param  string $desc 订单描述
	 * @param  string $showurl     商品展示地址
	 * @return void  页面直接跳转到支付宝
	 */
	static function alipayGoto($tradeno,$subject,$totalfee,$desc,$showurl){
		require_once(BASEPATH."/datas/alipay/lib/alipay_submit.class.php");
		/**************************请求参数**************************/
    $out_trade_no = $tradeno;
    $subject = $subject;
    $total_fee = $totalfee;
    $body = $desc;
    $show_url = $showurl;
		/************************************************************/
		$alipay_config = self::buildAlipayConf();
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "create_direct_pay_by_user",	//直接到账
				"partner" => trim($alipay_config['partner']),
		    "seller_email"  => $alipay_config['WIDseller_email'],
				"payment_type"	=> 1,
				"_input_charset"=> trim(strtolower($alipay_config['input_charset'])),
				//支付结果的服务器异步通知页面完整路径，不能加?id=123这类自定义参数，必填
				"notify_url"	=> $alipay_config['notifyurl'],
				//支付成功后的跳转页面的完整路径，不能加?id=123这类自定义参数，必填
				"return_url"	=> $alipay_config['returnurl'],
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	    => $body,
				"show_url"	=> $show_url,
				//防钓鱼时间戳
				//若要使用请调用类文件submit中的query_timestamp函数
				"anti_phishing_key"	=> '',
				//客户端的IP地址.非局域网的外网IP地址
				"exter_invoke_ip"	=> ''
		);
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);

		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "");
   	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>正在跳转到支付宝支付，请稍后...<div style="display:none">'.$html_text.'</div></body></html>';
	}
	/**
	 * 支付宝回调验证
	 * return验证经常出错的原因是有些系统会自动给get增加一些参数，导致验证失败，本系统不存在该问题
	 * @param string $type 回调类型 (notify|return）
	 * @return  array  代表验证结果，并返回该次交易必要参数（订单号|支付宝交易号|交易状态）
	 */
	static function alipayCallback($type){
    require_once BASEPATH.'/datas/alipay/lib/alipay_notify.class.php';
		$alipay_config = self::buildAlipayConf();
		$alipayNotify = new AlipayNotify($alipay_config);
		$ret = array('code'=>-1,'msg'=>'','data'=>array());
		switch ($type) {
			case 'notify':
		    $verify_result = $alipayNotify->verifyNotify();
		   	$params = $_POST;
				break;
			case 'return':
				$verify_result = $alipayNotify->verifyReturn();
				$params = $_GET;
				break;
		}
	  // 订单号
    $ret['data']['orderno'] = $params['out_trade_no'];
		//支付宝交易号
    $ret['data']['trade_no'] = $params['trade_no'];
		//交易状态 
		//WAIT_BUYER_PAY  交易创建,等待买家付款。
		//TRADE_CLOSED   在指定时间段内未支付时关闭的交易|在交易完成全额退款成功时关闭的交易。
    //TRADE_SUCCESS  交易成功,且可对该交易做操作,如:多级分润、退款等。
    //TRADE_PENDING   等待卖家收款(买家付款后,如果卖家账号被冻结)。
    //TRADE_FINISHED   交易成功且结束,即不可再做任何操作
    $ret['data']['trade_status'] = $params['trade_status'];
		if ($verify_result) {
			$ret['code'] = 1;
    	$ret['msg'] = '验证成功';
    }else{
    	$ret['msg'] = '验证失败';
    }
		return $verify_result;
	}
}