<?php
// controller
Class Home{
	function __construct(){
		$this->loginuser = Module_Manager::getloginUser();
		if (empty($this->loginuser)) {
			if ($GLOBALS['cur_method']!='login') {
				$this->view->load('manage/m_redirect',array('url'=>'/manage/manager/login'));
				die();
			}
		}else{
			if ($GLOBALS['cur_method']=='login') {
				Uri::build('manage/home','index',false,true);
			}
			/////////////////// 切入权限管理模块,根据权限来展示树
			if ($this->loginuser['username']!=$GLOBALS['config']['supermanager']['username']) {
				$group = Module_Group::getGroup($this->loginuser['group']);
				if ($group['code']==1) {
						if(empty($group['data']['rights'])){
							showMessage('对不起，您没有权限进行该操作！请与权限管理员联系');
						}
						// throw new Exception('对不起，您没有权限进行该操作！请与管理员联系', 1);
				}else{
					showMessage('管理员组信息错误!');
				}
			}
			$this->view->data(array('user'=>$this->loginuser));
		}
	}

	function index(){
		//导航树
		$datas['menuTree'] = array(
				array('name'=>'系统管理','con'=>'setting','subs'=>array(
																		'info'=>'系统信息',
																		'cache'=>'缓存处理',
																		'dbback'=>'数据备份',
																		'logs'=>'操作日志')),
				array('name'=>'用户管理','con'=>'user','subs'=>array(
																		'ugroup'=>'用户分组',
																		'users'=>'用户列表',
																		'edit'=>'新增用户')),
				array('name'=>'管理员信息','con'=>'manager','subs'=>array(
																		'mgroup'=>'管理员分组',
																		'managers'=>'管理员列表')),
				array('name'=>'内容管理','con'=>'article','subs'=>array(
																		'lists'=>'文章列表',
																		'edit'=>'新增文章',
																		'cate'=>'文章分类',
																		'comm'=>'评论管理')),
				array('name'=>'商品管理','con'=>'product','subs'=>array(
																		'lists'=>'商品列表',
																		'cate'=>'商品分类',
																		'brands'=>'品牌列表',
																		'prop'=>'商品属性',
																		'comm'=>'评论管理')),
				array('name'=>'订单管理','con'=>'order','subs'=>array(
																		'lists'=>'订单列表')),
				array('name'=>'高级扩展','con'=>'setting','subs'=>array(
																		'verify'=>'敏感词汇',
																		'thirdlogincfg'=>'第三方登陆',
																		'payment'=>'支付管理')),
				array('name'=>'邮件管理','con'=>'mail','subs'=>array(
																		'cfg'=>'SMTP邮件配置',
																		'send'=>'发送邮件')),
				array('name'=>'微信管理','con'=>'weixin','subs'=>array(
																		'menu'=>'菜单列表')),
			);
		//根据用户权限过滤一下
		if ($this->loginuser['username']!= $GLOBALS['config']['supermanager']['username']) {
			foreach ($datas['menuTree'] as $mkey=>&$submenus) {
				foreach ($submenus['subs'] as $pkey => $pname) {
					// if (preg_match('/'.$GLOBALS['cur_controller'].'-'.$GLOBALS['cur_method'].'($|,)/', $this->rights)==0) {
					if (preg_match('/'.$submenus['con'].'-'.$pkey.'($|,)/', $this->rights)==0) {
						unset($submenus['subs'][$pkey]);
					}
				}
				if (count($submenus['subs'])==0) {
					unset($datas['menuTree'][$mkey]);
				}
			}
		}
		// // 获取运营统计信息
		// $datas['yysj'] = array();
		// $today_start = strtotime(date("Y-m-d"));
		// // var_dump($today_start);
		// // 用户
		// $_query = $GLOBALS['db']->query('select count(*) as num ,status from t_user where types=1 group by status');
		// $datas['yysj']['user_total'] = $GLOBALS['db']->fetchAll($_query,'status');
		// $datas['yysj']['user_new_today'] = $GLOBALS['db']->result('select count(*) as num from t_user where types=1 and status>0 and addtime>='.$today_start);
		// $datas['yysj']['user_login_today'] = $GLOBALS['db']->result('select count(*) as num from t_user where types=1 and status>0 and lasttime>='.$today_start);
		// // 文章
		// $_query = $GLOBALS['db']->query('select count(*) as num ,status from t_article group by status');
		// $datas['yysj']['article_total'] = $GLOBALS['db']->fetchAll($_query,'status');
		// $datas['yysj']['article_new_today'] = $GLOBALS['db']->result('select count(*) as num from t_article  where status>0 and createdate>='.$today_start);
		// // 产品
		// $_query = $GLOBALS['db']->query('select count(*) as num ,status from t_product group by status');
		// $datas['yysj']['product_total'] = $GLOBALS['db']->fetchAll($_query,'status');
		// $datas['yysj']['product_new_today'] = $GLOBALS['db']->result('select count(*) as num from t_product where status>0 and createdate>='.$today_start);
		// // 订单
		// $_query = $GLOBALS['db']->query('select count(*) as num ,status from t_order group by status');
		// $datas['yysj']['order_total'] = $GLOBALS['db']->fetchAll($_query,'status');
		// $datas['yysj']['order_new_today'] = $GLOBALS['db']->result('select count(*) as num from t_order where status<4 and createdate>='.$today_start);

 		$this->view->load('manage/m_index',$datas);
	}
}