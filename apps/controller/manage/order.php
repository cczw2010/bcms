<?php
	// 管理订单页面处理类
	class Order{
		const ERRNAME = '_x_errmsg';
		function __construct(){
			$this->loginuser = Module_User::getloginUser(true);
			if (empty($this->loginuser)) {
				if ($GLOBALS['cur_method']!='login') {
					$this->view->load('manage/m_redirect',array('url'=>'/manage/user/login'));
					die();
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
		// 获取订单列表
		public function lists(){
			$params = Uri::getParams();
			$params = $params['params'];
			$page = !empty($params[0])?$params[0]:1;
			$psize = 20;
			// 过滤条件
			$conds = array();	//检索条件
			$pageParams = array();//分页搜索参数
			if (!empty($_REQUEST['userid'])) {
				$conds['userid'] = '= "'.$_REQUEST['userid'].'"';
				$pageParams['userid'] = $_REQUEST['userid'];
			}
			if (!empty($_REQUEST['orderno'])) {
				$conds['orderno'] = '= "'.$_REQUEST['orderno'].'"';
				$pageParams['orderno'] = $_REQUEST['orderno'];
			}
			if (isset($_REQUEST['status']) && intval($_REQUEST['status'])>-1) {
				// 状态可能为0,所以不能用empty来判断
				$conds['status'] = $_REQUEST['status'];
				$pageParams['status'] = $_REQUEST['status'];
			}
			if (!empty($_REQUEST['createdate'])) {
				$conds[] = 'FROM_UNIXTIME(createdate,"%Y-%m-%d")="'.date('Y-m-d',strtotime($_REQUEST['createdate'])).'"';
				$pageParams['createdate'] = $_REQUEST['createdate'];
			}
			$datas = array('appid'=>Module_Order::APPID);
			$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
			$datas['filter'] = $pageParams;
			$datas['items'] = Module_Order::getItems($conds,'order by id desc',$page,$psize);
			$datas['pages'] = multiPages($page,$psize,$datas['items']['total'],$pageParams,true);
			$curstatus = isset($pageParams['status'])?$pageParams['status']:-1;
			$datas['options'] = SForm::buildOptionsSimple(Module_Order::$statuss,$curstatus);
			Uri::setPrevPage();

			$this->view->load('manage/m_orders',$datas);
		}
		// 编辑订单，没有新增
		public function edit(){
			// 表单提交
			if ($id = Uri::post('id',0)) {
				$ret1 = Module_Order::getItem($id);
				if ($ret1['code']>0) {
					$t = time();
					$attrs = array(
							'totalfee' => $_POST['totalfee'],
							'paymentid' => Uri::post('paymentid',0),
							'shippingid' => Uri::post('shippingid',0),
							'addressid' =>Uri::post('addressid',0),
							'descr' => $_POST['descr'],	//管理员增加的订单附加信息
							'status' => Uri::post('status',0),	//订单没有默认必须有值
							'updatedate'=>$t,
						);
					// 判断是否增加了快递id,变更状态
					$oldshippingno = Uri::post('oldshippingno','');
					$shippingno = Uri::post('shippingno','');
					$attrs['shippingno'] =$shippingno;
					if ($ret1['data']['status']==1 && empty($oldshippingno) && !empty($shippingno)) {
						$attrs['status'] =2;
					}
					$ret = Module_Order::setItem($attrs,$id);

					if ($ret['code']<=0) {
						die(json_encode($ret));
					}else{
						// 添加日志
						Module_log::setItem(array('modulename'=>Module_Order::APPNAME,
								'moduleid'=>Module_Order::APPID,
								'key'=>($id==0?'新增':'更新'),
								'message'=>'订单更新,操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Order::TNAME
								));
					}
					Uri::build('manage/order','lists',false,true);
				}else{
					showmessage($ret1['msg']);
				}
			}
			// 不是表单提交
			$params = Uri::getParams();
			$params = $params['params'];
			$datas = array();
			if (count($params)>0) {
				$ret = Module_Order::getItem($params[0]);
				if ($ret['code']>0) {
					$datas['oitem'] = $ret['data'];
					$datas['options'] = SForm::buildOptionsSimple(Module_Order::$statuss,$datas['oitem']['status']);
				}else{
					Helper::setSession(self::ERRNAME,$ret['msg'].$GLOBALS['db']->getLastSql());
					Uri::build('manage/order','lists',false,true);
				}
			}
			$this->view->load('manage/m_orderedit',$datas);
		}
		// 删除订单
		public function del(){
			$params = Uri::getParams();
			$params = $params['params'];
			if (count($params)>0) {
				$ret = Module_Order::delItem($params[0]);
			}
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('modulename'=>Module_Order::APPNAME,
						'moduleid'=>Module_Order::APPID,
						'key'=>'删除',
						'message'=>'删除订单。操作id '.$params[0].';操作库:'.Module_Order::TNAME
						));
			}
			// 不管删除成功与否直接跳转
			Uri::redirect(Uri::getPrevPage());
		}
	}