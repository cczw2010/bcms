<?php
/* 支付方式统一管理类,纯静态类
 * 新增一个支付方式只需要再数据库中增加一行记录(key为唯一标示)，修改相应的后台编辑代码（必填项），
 * 然后在本类的gotoPay和callback中增加相应的处理模块，其他功能自动实现
 * ps：1 以实现的支付宝支付 会自动判断平台调用pc和wap支付sdk
 * 				
 */
final class Module_Payment{
	const APPID = 8;
	const APPNAME = '支付模块';
	const TNAME = 't_payment';
	const KEY_ALIPAY = 'alipay';
	// 判断移动端
	
	/**************支付前端业务逻辑部分*************/
	/**
	 * 根据支付类型去支付
	 * @param  string $key      		支付类型key = alipay|
	 * @param  string $out_trade_no 商户网站订单系统中唯一订单号，必填
	 * @param  string $subject 			订单名称 必填
	 * @param  string $total_fee 		付款金额 必填
	 * @param  string $body					订单描述
	 * @param  string $show_url 		商品展示地址,（值得注意的是如果是支付宝wap支付的返回地址不能带参数）
	 * @return void           
	 */
	static function gotoPay($key,$out_trade_no,$subject,$total_fee,$body,$show_url){
		$ret = self::getItemByKey(self::KEY_ALIPAY);
		if ($ret['code']<0) {
			die('配置不存在');
		}
		$item = $ret['data'];
		switch ($key) {
			case self::KEY_ALIPAY:
				// 构建config
				$conf = array(
					//合作身份者id，以2088开头的16位纯数字
					'partner'	=> $item['appid'],
					//安全检验码，以数字和字母组成的32位字符
					'key'	=> $item['appkey'],
					//签名方式
					'sign_type'=> 'MD5',
					//ca证书路径地址，用于curl中ssl校验
					'cacert'=> '/datas/alipay/cacert.pem',
					//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
					'transport'=> 'http',
					//（商家）合作者支付宝账号
					'WIDseller_email'    => $item['appaccount'],
					//字符编码格式 目前支持 gbk 或 utf-8
					'input_charset'=> 'utf-8'
					);
				$ua = new Useragent();
				$ismobile = $ua->is_mobile();
				if ($ismobile) {
					include_once(BASEPATH.'/datas/alipay_wap/lib/alipay_submit.class.php');
					//请求业务参数详细,必填
					$req_data = '<direct_trade_create_req><notify_url>' . $item['notifyurl'] . '</notify_url><call_back_url>' . $item['returnurl'] . '</call_back_url><seller_account_name>' . $conf['WIDseller_email'] . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $show_url . '</merchant_url></direct_trade_create_req>';
					$para_token = array(
						"service" => "alipay.wap.trade.create.direct",
						"partner" => trim($conf['partner']),
						"sec_id" => trim($conf['sign_type']),
						"format"	=> 'xml',			//返回格式,必填，不需要修改
						"v"	=> '2.0',						//必填，不需要修改
						"req_id"	=> mt_rand(),	//请求号,必填，须保证每次请求都是唯一
						"req_data"	=> $req_data,
						"_input_charset"	=> $conf['input_charset']
					);
					//建立请求
					$alipaySubmit = new AlipaySubmit($conf);
					$html_text = $alipaySubmit->buildRequestHttp($para_token);
					//URLDECODE返回的信息
					$html_text = urldecode($html_text);
					//解析远程模拟提交后返回的信息
					$para_html_text = $alipaySubmit->parseResponse($html_text);
					//获取request_token
					$request_token = $para_html_text['request_token'];
					/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
					//业务详细
					$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
					//构造要请求的参数数组，无需改动
					$para_token['service'] = "alipay.wap.auth.authAndExecute";
					$para_token['req_data'] = $req_data;

					//建立请求
					$alipaySubmit = new AlipaySubmit($conf);
					$html_text = $alipaySubmit->buildRequestForm($para_token, 'get', '确认');
				}else{
					include_once(BASEPATH.'/datas/alipay/lib/alipay_submit.class.php');
					//构造要请求的参数数组，无需改动
					$parameter = array(
					    //必填，不能修改
							"service" => "create_direct_pay_by_user",
							"partner" => trim($conf['partner']),
					    "seller_email"  => $conf['WIDseller_email'],
							"payment_type"	=> 1,
              "_input_charset"=> trim(strtolower($conf['input_charset'])),
					    //服务器异步通知页面路径,需http://格式的完整路径，不能加?id=123这类自定义参数
							"notify_url"	=> $item['notifyurl'],
              //页面跳转同步通知页面路径.需http://格式的完整路径，不能加?id=123这类自定义参数
							"return_url"	=>$item['returnurl'],
              //商户网站订单系统中唯一订单号，必填
							"out_trade_no"	=> $out_trade_no,
              //订单名称 必填
							"subject"	=> $subject,
              //付款金额 必填
							"total_fee"	=> $total_fee,
              //订单描述
							"body"	  => $body,
              //商品展示地址
							"show_url"	=> $show_url,
              //防钓鱼时间戳
              //若要使用请调用类文件submit中的query_timestamp函数
							"anti_phishing_key"	=> '',
              //客户端的IP地址.非局域网的外网IP地址
							"exter_invoke_ip"	=> ''
					);
					//建立请求
					$alipaySubmit = new AlipaySubmit($conf);
					$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
				}
				// dump($item,$conf);
				// die();
        echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>正在跳转到支付宝支付，请稍后...<div style="display:none">'.$html_text.'</div></body></html>';
				break;
			default:
				break;
		}
	}
	/**
	 * 阿里支付回调验证（自动区分pc和wap）
	 * return验证经常出错的原因是有些系统会自动给get增加一些参数，导致验证失败，本系统不存在该问题
	 * @param string $type 回调类型 (notify|return）
	 * @return  array  代表验证结果，并返回该次交易必要参数（订单号|支付宝交易号|交易状态）
	 */
	static function aliCallbackVerify($type){
    $ret = self::getItemByKey(self::KEY_ALIPAY);
		if ($ret['code']<0) {
			die('配置不存在');
		}
		$ua = new Useragent();
		$ismobile = $ua->is_mobile();
		$item = $ret['data'];
		if ($ismobile) {
    	include_once BASEPATH.'/datas/alipay_wap/lib/alipay_notify.class.php';
		}else{
    	include_once BASEPATH.'/datas/alipay/lib/alipay_notify.class.php';
			// 构建config
				$alipay_config = array(
					//合作身份者id，以2088开头的16位纯数字
					'partner'	=> $item['appid'],
					//安全检验码，以数字和字母组成的32位字符
					'key'	=> $item['appkey'],
					//签名方式
					'sign_type'=> 'MD5',
					//ca证书路径地址，用于curl中ssl校验
					'cacert'=> '/datas/alipay/cacert.pem',
					//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
					'transport'=> 'http',
					//（商家）合作者支付宝账号
					'WIDseller_email'    => $item['appaccount'],
					//字符编码格式 目前支持 gbk 或 utf-8
					'input_charset'=> 'utf-8'
				);
				$alipay_config = self::getPayConfig(self::KEY_ALIPAY);
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

	/**************************支付后端配置部分***************************/

	// 获取支付列表
	static function getItems($cond=array()){
		return $GLOBALS['db']->select(self::TNAME,$cond);
	}

	// 根据key获取开启的支付配置，(**自动刷新缓存)
	static function getItemByKey($key){
		$cachekey = 'xm_paymentcfg_'.$key;
		$cachgroup = $GLOBALS['config']['db']['group'];
		// 检查缓存
		if(!($datas = $GLOBALS['cache_file']->get($cachgroup,$cachekey))){
			$ret = array('code'=>-1,'msg'=>'');
			$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where status>0 and `key`="'.$key.'"');
			if ($item = $GLOBALS['db']->fetch_array($query)) {
				$ret['code'] = 1;
				$ret['data'] = $item;
			}else{
				$ret['msg'] = '支付方式不存在';
			}
			$GLOBALS['cache_file']->set($cachgroup,$cachekey,$ret);
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
			// 必填项，判断
			$check = FormVerify::rule(
				array(FormVerify::must($arrs['name']),'名称不能为空'),
				array(FormVerify::must($arrs['appid']),'支付必填信息不能为空'),
				array(FormVerify::must($arrs['appkey']),'支付必填信息不能为空'),
				array(FormVerify::must($arrs['notifyurl']),'服务器异步通知页面路径不能为空'),
				array(FormVerify::must($arrs['returnurl']),'页面跳转同步通知页面路径不能为空')
				);
			if ($check!==true) {
				$ret['msg'] = $check;
			}
		}
		if (empty($ret['msg'])) {
			$GLOBALS['db']->update(self::TNAME,$arrs,array('id'=>$id));
			$ret['code'] = 1;
			$ret['msg'] = '编辑成功';
			// 更新缓存
			$cachekey = 'xm_paymentcfg_'.$arrs['key'];
			$cachgroup = $GLOBALS['config']['db']['group'];
			$arrs['id'] = $id;
			$GLOBALS['cache_file']->delete($cachgroup,$cachekey);
		}
		return $ret;
	}
}