<?php
/**
 * Module_Mail，
 * 对外提供phpmailer单例接口
 * 使用方法直接实例化之后，调用phpmailer的实际接口
 */
final class Module_Mail{
 	const APPID = 15;
	const APPNAME = '邮件模块';
	const TNAME = 't_mail_config';
	const CACHEKEY = 'xm_mailcfg';
	/**
	 * 实例phpmailler对象
	 */
	static private $instance = null;

	/**
	 * 获取phpmailer实例
	 * @return [type] [description]
	 */
	static public function getInstance(){
		if (self::$instance==null) {
			include_once('datas/phpmailer/class.phpmailer.php');
			include_once('datas/phpmailer/class.smtp.php');
			// include_once('datas/phpmailer/class.pop3.php'); //暂时不需要pop
			return self::$instance = new PHPMailer();
		}else{
			return self::$instance;
		}
	}
	/**
	 * 获取邮件配置信息
	 */
	static public function getCfg(){
		$cachgroup = 'setting';
		// 检查缓存
		if(!($ret = $GLOBALS['cache_file']->get($cachgroup,self::CACHEKEY))){
			$ret =array('code'=>-1,'msg'=>'');
			$result = $GLOBALS['db']->select(self::TNAME);
			if ($result['total']>0) {
				$ret['code'] = 1;
				$ret['data'] = current($result['list']);
			}else{
				$ret['msg'] = '配置不存在';
			}
			$GLOBALS['cache_file']->set($cachgroup,self::CACHEKEY,$ret);
		}
		return $ret;
	}

	/**
	 * 设置邮件配置信息
	 */
	static public function setCfg($attr,$id=0){
		$ret = array('code'=>-1,'msg'=>'');
		if (empty($id)) {
			$id = $GLOBALS['db']->insert(self::TNAME,$attr);
		}else{
			$GLOBALS['db']->update(self::TNAME,$attr,array('id'=>$id));
		}
		$ret['code'] = 1;
		$ret['msg'] = '更新成功';
		$ret['data'] = $id;
		// 删除缓存
		$cachgroup = 'setting';
		$GLOBALS['cache_file']->delete($cachgroup,self::CACHEKEY);
		return $ret;
	}
	/**
	 * 发送邮件（使用全局服务器配置）
	 * @param  string|array $sendtos		收件人地址
	 * @param  string 			$subject		主题
	 * @param  string 			$body				内容，支持html
	 * @param  array 				$attachs		附件数组
	 * @return boolean
	 */
	static public function send($sendtos,$subject,$body='',$attachs=array()){
		$mail = self::getInstance();
		$cfg = self::getCfg();
		if ($cfg['code']<0) {
			throw new Exception("请检查邮件配置");
		}
		$config = $cfg['data'];

    $mail->CharSet ="UTF-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;

		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$mail->Host = $config['smtpservice'];

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = $config['port'];

		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';

		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = $config['username'];

		//Password to use for SMTP authentication
		$mail->Password = $config['password'];

		//Set who the message is to be sent from
		$mail->setFrom($config['username'],$config['nickname']);

		//Set an alternative reply-to address
		// $mail->addReplyTo('replyto@example.com', 'First Last');

		//Set who the message is to be sent to
		if (is_array($sendtos)) {
			foreach ($sendtos as $sendto) {
				$mail->addAddress($sendto, '');
			}
		}else{
				$mail->addAddress($sendtos, '');
		}

		//Set the subject line
		$mail->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		// $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
		$mail->msgHTML($body.$config['mark']);

		//Replace the plain text body with one created manually
		$mail->AltBody = '该邮件中可能包含了html代码，如果无法正常显示，请使用html解析器';

		//Attach an image file
		if (is_array($attachs)) {
			foreach ($attachs as $attach) {
				$mail->addAttachment($attach);
			}
		}

		//send the message, check for errors
		$ret = $mail->send();
		$mail->clearAddresses();
    $mail->clearAttachments();

    if (!$ret) {
    	return $mail->ErrorInfo;
    }
    return true;
	}
}