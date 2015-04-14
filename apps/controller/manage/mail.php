<?php
	// 一些配置信息和系统级页面的综合处理
	class Mail{
		const ERRNAME = '_x_errmsg';
		function __construct(){
			$this->loginuser = Module_User::getloginUser(true);
			if (empty($this->loginuser)) {
				if ($GLOBALS['cur_method']!='login') {
					Uri::build('manage/user','login',false,true);
				}
			}else{
				if ($GLOBALS['cur_method']=='login') {
					Uri::build('manage/home','index',false,true);
				}
				/////////////////// 切入权限管理模块,根据权限来展示树
				$this->rights = Module_Group::isManager($this->loginuser['group']);
				if ($this->loginuser['group']!= Module_Group::GROUP_SUPER && $this->rights===false) {
						// throw new Exception('对不起，您没有权限进行该操作！请与管理员联系', 1);
						showMessage('对不起，您没有权限进行该操作！请与权限管理员联系');
				}
				$this->view->data(array('user'=>$this->loginuser));
			}
		}
		// 邮件列表
		public function lists(){
			
		}
		// 接收邮件
		public function receive(){

		}
		// 邮件配置
		public function cfg(){
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
				die(json_encode($ret));
			}

			$ret1 = Module_Mail::getCfg();
			if ($ret1['code']>0) {
				$ret['code'] = 1;
				$ret['data'] = $ret1['data'];
			}
			$this->view->load('manage/m_mails',$ret);
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
					die(json_encode($ret));
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
				die(json_encode($ret));
			}
			$this->view->load('manage/m_sendmail',$ret);
		}
	}