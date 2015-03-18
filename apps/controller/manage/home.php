<?php
// controller
Class Home{
	function __construct(){
		$this->loginuser = Module_User::getloginUser(true);
		if (empty($this->loginuser)) {
			// 直接设置前一个页面为当前页
			Uri::setPrevPage();
			Uri::build('manage/user','login',false,true);
		}
		/////////////////// 切入权限管理模块,根据权限来展示树
		$this->rights = Module_Group::isManager($this->loginuser['group']);
		if ($this->loginuser['group']!= Module_Group::GROUP_SUPER && $this->rights===false) {
				// throw new Exception('对不起，您没有权限进行该操作！请与管理员联系', 1);
				showMessage('对不起，您没有权限进行该操作！请与权限管理员联系');
		}
		$this->view->data(array('user'=>$this->loginuser));
	}

	function index(){	
		//导航树
		$datas['menuTree'] = array(
				array('name'=>'系统管理','con'=>'setting','subs'=>array(
																		'info'=>'系统信息',
																		'cache'=>'缓存处理',
																		'logs'=>'log日志')),
				
				array('name'=>'用户管理','con'=>'user','subs'=>array(
																		'users'=>'用户列表',
																		'mgroup'=>'管理员分组',
																		'ugroup'=>'用户分组',
																		'ulogs'=>'登录日志')),
				array('name'=>'内容管理','con'=>'article','subs'=>array(
																		'lists'=>'文章列表',
																		'particlecate'=>'文章分类',
																		'particlecomm'=>'评论管理')),
				array('name'=>'商品管理','con'=>'product','subs'=>array(
																		'pbrands'=>'品牌列表',
																		'pproducts'=>'商品列表',
																		'pproductcate'=>'商品分类',
																		'pproductcomm'=>'评论管理')),
				// array('name'=>'礼包管理','con'=>'gift','subs'=>array(
				// 														'pgifts'=>'礼包列表',
				// 														'pgifts/cate'=>'礼包分类',
				// 														'pgifts/comm'=>'评论管理')),
				array('name'=>'订单管理','con'=>'order','subs'=>array(
																		'porders'=>'订单列表')),
				// array('name'=>'问答系统','con'=>'answers','subs'=>array(
				// 														'panswers'=>'问答列表')),
				// array('name'=>'流程管理','con'=>'process','subs'=>array(
				// 														'pprocess'=>'流程列表',
				// 														'pprocescate'=>'流程分类')),
				array('name'=>'高级扩展','con'=>'setting','subs'=>array(
																		// 'pproductprop'=>'属性管理',
																		'pcitys'=>'区域管理',
																		'pverify'=>'敏感词汇',
																		'pappcfgs'=>'第三方登陆',
																		'ppayments'=>'支付管理',
																		'pmails'=>'SMTP邮件',))
			);
		//根据用户权限过滤一下
		if ($this->loginuser['group']!=Module_Group::GROUP_SUPER) {
			foreach ($datas['menuTree'] as $mkey=>&$submenus) {
				foreach ($submenus['subs'] as $pkey => $pname) {
					// if (preg_match('/'.$GLOBALS['cur_controller'].'-'.$GLOBALS['cur_method'].'($|,)/', $this->rights)==0) {
					if (preg_match('/m-'.$submenus['con'].'-'.$pkey.'($|,)/', $this->rights)==0) {
						unset($submenus['subs'][$pkey]);
					}
				}
				if (count($submenus['subs'])==0) {
					unset($datas['menuTree'][$mkey]);
				}
			}
		}
 		$this->view->load('manage/m_index',$datas);
	}
}