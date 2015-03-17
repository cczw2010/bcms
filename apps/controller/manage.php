<?php
/**
 * 后台管理模块  by awen
 */
class Manage{
	function __construct(){
		$this->user = Module_User::getloginUser();
		if (empty($this->user)) {
			// 直接设置前一个页面为当前页
			Uri::setPrevPage();
			Uri::build('manage/user','login',false,true);
		}
		/////////////////// 切入权限管理模块,根据权限来展示树
		$this->rights = Module_Group::isManager($this->user['group']);
		if ($this->user['group']!= Module_Group::GROUP_SUPER && $this->rights===false) {
				// throw new Exception('对不起，您没有权限进行该操作！请与管理员联系', 1);
				showMessage('对不起，您没有权限进行该操作！请与权限管理员联系');
		}
	}
	// 首页
	public function index(){
		$datas['user'] = $this->user;
		//导航树
		$datas['menuTree'] = array(
				array('name'=>'系统管理','subs'=>array(
																		'pinfo'=>'系统信息',
																		'pcache'=>'缓存处理',
																		'plogs'=>'log日志')),
				
				array('name'=>'用户管理','subs'=>array(
																		'pusers'=>'用户列表',
																		'pugroup'=>'管理员分组',
																		'pusergroup'=>'用户分组',
																		'puserlog'=>'登录日志')),
				array('name'=>'内容管理','subs'=>array(
																		'particles'=>'文章列表',
																		'particlecate'=>'文章分类',
																		'particlecomm'=>'评论管理')),
				array('name'=>'商品管理','subs'=>array(
																		'pbrands'=>'品牌列表',
																		'pproducts'=>'商品列表',
																		'pproductcate'=>'商品分类',
																		'pproductcomm'=>'评论管理')),
				// array('name'=>'礼包管理','subs'=>array(
				// 														'pgifts'=>'礼包列表',
				// 														'pgifts/cate'=>'礼包分类',
				// 														'pgifts/comm'=>'评论管理')),
				array('name'=>'订单管理','subs'=>array(
																		'porders'=>'订单列表')),
				// array('name'=>'问答系统','subs'=>array(
				// 														'panswers'=>'问答列表')),
				// array('name'=>'流程管理','subs'=>array(
				// 														'pprocess'=>'流程列表',
				// 														'pprocescate'=>'流程分类')),
				array('name'=>'高级扩展','subs'=>array(
																		// 'pproductprop'=>'属性管理',
																		'pcitys'=>'区域管理',
																		'pverify'=>'敏感词汇',
																		'pappcfgs'=>'第三方登陆',
																		'ppayments'=>'支付管理',
																		'pmails'=>'SMTP邮件',))
			);
		//根据用户权限过滤一下
		if ($this->user['group']!=Module_Group::GROUP_SUPER) {
			foreach ($datas['menuTree'] as $mkey=>&$submenus) {
				foreach ($submenus['subs'] as $pkey => $pname) {
					// if (preg_match('/'.$GLOBALS['cur_controller'].'-'.$GLOBALS['cur_method'].'($|,)/', $this->rights)==0) {
					if (preg_match('/manage-'.$pkey.'($|,)/', $this->rights)==0) {
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
	///////一下页面都是ajax获取的，所以不要头尾
	//汇总页，系统信息，数据库信息
	public function pinfo(){
		$datas = array();
		$datas['sinfo'] = Helper::getSystemInfo();
		$datas['winfo'] = Helper::getSiteInfo();
		$datas['dbinfo'] = $GLOBALS['db']->get_db_info();
		$datas['uainfo'] = new Useragent();
		$this->view->load('manage/m_info',$datas);
	}
	// 数据库备份
	public function pdbback(){
		include_once('manage/setting.php');
		$instance = new Setting();
		$datas = $instance->dbback();
		if ($_SERVER['REQUEST_METHOD']=='POST') {
			die(json_encode($datas));
		}else{
			$this->view->load('manage/m_dbback',$datas);
		}
	}
	// 缓存处理
	public function pcache(){
		include_once('manage/setting.php');
		$instance = new Setting();
		$datas = $instance->cache();
		$this->view->load('manage/m_cache',$datas);
	}
	// log列表
	public function plogs(){
		include_once('manage/setting.php');
		$instance = new Setting();
		$datas = $instance->logs();		
		$this->view->load('manage/m_logs',$datas);
	}
	//邮件配置
	public function pmails(){
		include_once('manage/mails.php');
		$instance = new Mails();
		$datas = $instance->mailcfg();
		if ($_SERVER['REQUEST_METHOD']=='POST') {
			die(json_encode($datas));
		}else{
			$this->view->load('manage/m_mails',$datas);
		}
	}
	//发送邮件
	public function psendmail(){
		include_once('manage/mails.php');
		$instance = new Mails();
		$ret = $instance->send();
		die(json_encode($ret));
	}
	// 文章列表
	public function particles(){
		include_once('manage/article.php');
		$instance = new MArticle();
		$datas = $instance->lists();
		$this->view->load('manage/m_articles',$datas);
	}
	// 编辑文章
	public function particleedit(){
		include_once('manage/article.php');
		$instance = new MArticle();
		$datas = $instance->edit();
		$this->view->load('manage/m_articleedit',$datas);
	}
	// 删除文章
	public function particledel(){
		include_once('manage/article.php');
		$instance = new MArticle();
		$instance->del();
	}
	// 文章分类管理
	public function particlecate(){
		Uri::setPrevPage();

		$url = Uri::build('manage','pcategory').'?appid='.Module_Article::APPID;
		Uri::redirect($url);
	}
	// 文章评论管理
	public function particlecomm(){
		Uri::setPrevPage();
		$url = Uri::build('manage','pcomms').'?appid='.Module_Article::APPID;
		Uri::redirect($url);
	}
	// 品牌管理
	public function pbrands(){
		include_once('manage/product.php');
		$instance = new MProduct();
		$datas = $instance->brands();
		$this->view->load('manage/m_brands',$datas);
	}
	// 编辑品牌
	public function pbrandedit(){
		include_once('manage/product.php');
		$instance = new MProduct();
		$datas = $instance->bedit();
		$this->view->load('manage/m_brandedit',$datas);
	}
	// 删除品牌
	public function pbranddel(){
		include_once('manage/product.php');
		$instance = new MProduct();
		$instance->bdel();
	}
	// 商品列表
	public function pproducts(){
		include_once('manage/product.php');
		$instance = new MProduct();
		$datas = $instance->products();
		$this->view->load('manage/m_products',$datas);
	}
	// 编辑商品
	public function pproductedit(){
		include_once('manage/product.php');
		$instance = new MProduct();
		$datas = $instance->pedit();
		$this->view->load('manage/m_productedit',$datas);
	}
	// 删除商品
	public function pproductdel(){
		include_once('manage/product.php');
		$instance = new MProduct();
		$datas = $instance->pdel();
	}
	// 商品分类管理
	public function pproductcate(){
		Uri::setPrevPage();
		$url = Uri::build('manage','pcategory').'?appid='.Module_Product::APPID;
		Uri::redirect($url);
	}
	// 商品属性管理
	public function pproductprop(){
		include_once('manage/product.php');
		$instance = new MProduct();
		$datas = $instance->pprop();
		$this->view->load('manage/m_prop',$datas);
	}
	// 商品评论
	public function pproductcomm(){
		Uri::setPrevPage();
		$url = Uri::build('manage','pcomms').'?appid='.Module_Product::APPID;
		Uri::redirect($url);
	}
	// 分类列表
	public function pcategory(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->cates();
		$this->view->load('manage/m_category',$datas);
	}
	// 编辑分类
	public function pcateedit(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->cateedit();
		$this->view->load('manage/m_cateedit',$datas);
	}
	// 删除分类
	public function pcatedel(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->catedel();
	}
	// 评论列表
	public function pcomms(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->comms();
		$this->view->load('manage/m_comments',$datas);
	}
	// 编辑评论
	public function pcommedit(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->commedit();
		$this->view->load('manage/m_commedit',$datas);
	}
	// 删除评论
	public function pcommdel(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$instance->catedel();
	}
	// 编辑属性
	public function ppropedit(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->propedit();
		$this->view->load('manage/m_propedit',$datas);
	}
	// 删除属性
	public function ppropdel(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$instance->propdel();
	}
	// 用户列表
	public function pusers(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->users();	
		$this->view->load('manage/m_users',$datas);
	}
	// 编辑用户
	public function puseredit(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->uedit();	
		$this->view->load('manage/m_useredit',$datas);
	}
	// 修改密码
	public function purepass(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->repass();	
		$this->view->load('manage/m_urepass',$datas);
	}
	// 修改个人信息
	public function pueditinfo(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->ueditinfo();
		$this->view->load('manage/m_userinfoedit',$datas);
	}
	// 删除用户
	public function puserdel(){
		include_once('manage/user.php');
		$instance = new MUser();
		$instance->udel();	
	}
	// 用户收货地址
	public function puseraddress(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->uaddress();	
		$this->view->load('manage/m_address',$datas);
	}
	// 用户登陆日志列表
	public function puserlog(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->ulogs();	
		$this->view->load('manage/m_ulogs',$datas);
	}
	// 管理员分组
	public function pugroup(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->groups(0);
		$datas['types'] = 0;
		Uri::setPrevPage();
		$this->view->load('manage/m_group',$datas);
	}
	// 用户组
	public function pusergroup(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->groups(1);
		$datas['types'] = 1;
		Uri::setPrevPage();
		$this->view->load('manage/m_group',$datas);
	}
	// 编辑用户组
	public function pgroupedit(){
		include_once('manage/user.php');
		$instance = new MUser();
		$datas = $instance->gedit();
		// dump($datas);
		$this->view->load('manage/m_groupedit',$datas);
	}
	// 删除用户组
	public function pgroupdel(){
		include_once('manage/user.php');
		$instance = new MUser();
		$instance->gdel();
	}
	// 区域列表
	public function pcitys(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->citys();
		$this->view->load('manage/m_citys',$datas);
	}
	// 区域编辑
	public function pcityedit(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->cityedit();
		$this->view->load('manage/m_cityedit',$datas);
	}
	// 区域删除
	public function pcitydel(){
		include_once('manage/ccp.php');
		$instance = new MCCP();
		$datas = $instance->citydel();
		$this->view->load('manage/m_citydel',$datas);
	}
	// 敏感词处理
	public function pverify(){
		include_once('manage/setting.php');
		$instance = new Setting();
		$datas = $instance->verify();
		$this->view->load('manage/m_pverify',$datas);
	}
	// 支付方式列表
	public function ppayments(){
		$pays = Module_Payment::getItems(array('status'=>1));
		$datas = array('items'=>$pays['list']);
		$this->view->load('manage/m_payments',$datas);
	}
	// 支付方式编辑
	public function ppaymentedit(){
		include_once('manage/setting.php');
		$instance = new Setting();
		$datas = $instance->payedit();
		$this->view->load('manage/m_paymentedit',$datas);
	}
	// 第三方登陆配置页
	public function pappcfgs(){
		$pays = Module_ThirdLogin::getAppCfgs();
		$datas = array('items'=>$pays['list']);
		$this->view->load('manage/m_appcfgs',$datas);
	}
	// 第三方登陆配置编辑
	public function pappcfgedit(){
		include_once('manage/setting.php');
		$instance = new Setting();
		$datas = $instance->thirdloginedit();
		$this->view->load('manage/m_appcfgedit',$datas);
	}
	// 订单列表
	public function porders(){
		include_once('manage/order.php');
		$instance = new M_Order();
		$datas = $instance->lists();
		$this->view->load('manage/m_orders',$datas);
	}
	// 订单修改
	public function porderedit(){
		include_once('manage/order.php');
		$instance = new M_Order();
		$datas = $instance->edit();
		$this->view->load('manage/m_orderedit',$datas);
	}
	// 删除用户组
	public function porderdel(){
		include_once('manage/order.php');
		$instance = new M_Order();
		$instance->del();
	}
}
