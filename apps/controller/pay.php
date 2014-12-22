<?php
// 支付相关页面
Class Pay{
	/**
	 * 支付宝-支付结果的服务器异步通知页面
	 * @return string (success|fail)
	 */
	public function alipay_notify(){
		$result = Module_Payment::alipayCallback('notify');
        if($result['code']>0) {//验证成功
            //商户订单号
            $orderno = $result['data']['orderno'];
            //支付宝交易号
            $trade_no = $result['data']['trade_no'];
            //交易状态
            $trade_status = $result['data']['trade_status'];

            switch ($trade_status) {
            	case 'TRADE_FINISHED':
            	case 'TRADE_SUCCESS':
          		  //判断该笔订单是否在商户网站中已经做过处理
    						//如果没有做过处理，根据订单号（orderno）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
    						//如果有做过处理，不执行商户的业务程序
            		break;
            	case 'WAIT_BUYER_PAY':
            		// 交易创建,等待买家付款。
            		break;
            	break;
            	case 'TRADE_CLOSED':
            		// 在指定时间段内未支付时关闭的交易|在交易完成全额退款成功时关闭的交易。
            		break;
            	case 'TRADE_PENDING':
            		//等待卖家收款(买家付款后,如果卖家账号被冻结)
            		break;
            }
            die('success');
        }
        else {
            die('fail');
        }
	}

	// 支付宝-支付成功后的跳转页面的完整路径
	public function alipay_return(){
		$result = Module_Payment::alipayCallback('notify');
        if($result['code']>0) {//验证成功
            //商户订单号
            $orderno = $result['data']['orderno'];
            //支付宝交易号
            $trade_no = $result['data']['trade_no'];
            //交易状态
            $trade_status = $result['data']['trade_status'];

            switch ($trade_status) {
            	case 'TRADE_FINISHED':
            	case 'TRADE_SUCCESS':
          		  //判断该笔订单是否在商户网站中已经做过处理
    						//如果没有做过处理，根据订单号（orderno）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
    						//如果有做过处理，不执行商户的业务程序
            		break;
            	case 'WAIT_BUYER_PAY':
            		// 交易创建,等待买家付款。
            		break;
            	break;
            	case 'TRADE_CLOSED':
            		// 在指定时间段内未支付时关闭的交易|在交易完成全额退款成功时关闭的交易。
            		break;
            	case 'TRADE_PENDING':
            		//等待卖家收款(买家付款后,如果卖家账号被冻结)
            		break;
            }
        }
        else {
        	// 验证失败
        }
	}
}