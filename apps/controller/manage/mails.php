<?php
	// 一些配置信息和系统级页面的综合处理
	class Mails{
		const ERRNAME = '_x_errmsg';
		// 邮件列表
		public function mails(){
			
		}
		// 接收邮件
		public function receive(){

		}
		// 邮件配置
		public function mailcfg(){
			$ret = array('code'=>-1,'msg'=>'');
			// 表单提交
			if (isset($_POST['id'])) {
				$id = Uri::post('id');
				$params = array(
						'smtpservice' => Uri::post('smtpservice'),
						'port' => Uri::post('port'),
						'nickname' => Uri::post('nickname'),
						'username' => Uri::post('username'),
						'password' => Uri::post('password'),
						'mark' => addslashes(stripslashes($_POST['mark'])),
						'language' => Uri::post('language'),
					);
				$ret1 = Module_Mail::setCfg($params,$id);
				if ($ret1['code']>0) {
					$ret['code'] = 1;
					$ret['msg'] = '更新成功';
					$ret['data'] = $ret1['data'];
				}
			}else{
				$ret1 = Module_Mail::getCfg();
				if ($ret1['code']>0) {
					$ret['code'] = 1;
					$ret['data'] = $ret1['data'];
				}
			}
			return $ret;
		}
		// 发送邮件
		public function send(){
			$ret = array('code'=>-1,'msg'=>'');
			if (isset($_POST['receiver'])) {
				$receiver = Uri::post('receiver');
				$subject = Uri::post('subject');
				$body = $_POST['body'];
				$check = FormVerify::rule(
					array(FormVerify::must($receiver),'收件人不能为空'),
					array(FormVerify::must($subject),'标题不能为空'),
					array(FormVerify::must($body),'正文不能为空')
				);
				if ($check!==true) {
					$ret['msg'] = $check;
					return $ret;
				}
				//
				$receivers = explode(';', $receiver);
				$attach_paths = Uri::post('uplodify_fpaths');
				$attach_names = Uri::post('uplodify_fnames');
				$attachs = array();
				if (count($attach_paths)>0) {
					foreach ($attach_paths as $k=>$apath) {
						$attachs[] = ltrim($apath.'/'.$attach_names[$k],'/');
					}
				}
				$ret1 = Module_Mail::send($receivers,$subject,$body,$attachs);
				if ($ret1) {
					$ret['code'] = 1;
					$ret['msg'] = '发送成功！';
				}else{
					$ret['msg'] = $ret1;
				}
			}
			return $ret;
		}
	}