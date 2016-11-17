<?php
// 商品管理类
class Product{
	const ERRNAME = '_x_errmsg';
	const MODULEID = 'product';
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
	// 品牌管理
	public function brands(){
		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 20;

		$conds = array();
		$pageParams = array();//分页搜索参数
		if (!empty($_REQUEST['name'])) {
			$conds['name'] = 'like "%'.$_REQUEST['name'].'%"';
			$pageParams['name'] = $_REQUEST['name'];
		}
		if (isset($_REQUEST['status']) && $_REQUEST['status']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['status'] = $_REQUEST['status'];
			$pageParams['status'] = $_REQUEST['status'];
		}
		$datas = array();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['filter'] = $pageParams;
		$datas['items'] = Module_Brand::getItems($conds,'order by id desc',$page,$psize);
		$datas['pages'] = multiPages($page,$psize,$datas['items']['total'],$pageParams,true);
		// 分类
		Uri::setPrevPage();

		$this->view->load('manage/m_brands',$datas);
	}
	// 编辑品牌
	public function brandedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$t = time();
			$attrs = array(
				'name' => Uri::post('name'),
				'ename' => Uri::post('ename'),
				'site' => Uri::post('site'),
				'desc' => Uri::post('desc'),
				'status' => Uri::post('status',0),
			);
			// logo文件,只取第一个logo
			$uplodify_fpaths = Uri::post('uplodify_fpaths');
			$uplodify_fnames = Uri::post('uplodify_fnames');
			if (!empty($uplodify_fpaths[0])) {
				$attrs['logo'] = $uplodify_fpaths[0].'/'.$uplodify_fnames[0];
			}
			$ret = Module_Brand::setItem($attrs,$id);
			if ($ret['code']<=0) {
				die(json_encode($ret));
			}else{
				// 添加日志
				Module_log::setItem(array('message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Brand::TNAME));
			}
			Uri::build('manage/product','brands',false,true);
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Brand::getItem($params[0]);
			if ($ret['code']>0) {
				$datas['oitem'] = $ret['data'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg'].$GLOBALS['db']->getLastSql());
				Uri::build('manage/product','brands',false,true);
			}
		}
		$this->view->load('manage/m_brandedit',$datas);
	}
	// 删除品牌
	public function branddel(){
		$params = Uri::getParams();
		$params = $params['params'];
		if (count($params)>0) {
			$ret = Module_Brand::delItem($params[0]);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('message'=>'操作id '.$params[0].';操作库:'.Module_Brand::TNAME));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
	// 商品列表
	public function lists(){
		$params = Uri::getParams();
		$params = $params['params'];
		$page = !empty($params[0])?$params[0]:1;
		$psize = 15;

		// 过滤条件
		$conds = array();	//检索条件
		$pageParams = array();//分页搜索参数
		if (!empty($_REQUEST['username'])) {
			$conds['username'] = 'like "%'.$_REQUEST['username'].'%"';
			$pageParams['username'] = $_REQUEST['username'];
		}
		if (!empty($_REQUEST['title'])) {
			$conds['title'] = 'like "%'.$_REQUEST['title'].'%"';
			$pageParams['title'] = $_REQUEST['title'];
		}
		if (!empty($_REQUEST['cateid'])) {
			$conds['cateid'] = $_REQUEST['cateid'];
			$pageParams['cateid'] = $_REQUEST['cateid'];
		}
		if (!empty($_REQUEST['brandid'])) {
			$conds['brandid'] = $_REQUEST['brandid'];
			$pageParams['brandid'] = $_REQUEST['brandid'];
		}
		if (isset($_REQUEST['status']) && $_REQUEST['status']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['status'] = $_REQUEST['status'];
			$pageParams['status'] = $_REQUEST['status'];
		}
		if (isset($_REQUEST['ishot']) && $_REQUEST['ishot']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['ishot'] = $_REQUEST['ishot'];
			$pageParams['ishot'] = $_REQUEST['ishot'];
		}
		$datas = array();
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['filter'] = $pageParams;
		$datas['items'] = Module_Product::getItems($conds,'order by id desc',$page,$psize);
		$datas['pages'] = multiPages($page,$psize,$datas['items']['total'],$pageParams,true);
		// 分类
		$cates = Module_Category::getChilds(0,0,array('moduleid'=>self::MODULEID),-1);
		$cateid = isset($_REQUEST['cateid'])?$_REQUEST['cateid']:0;
		$datas['options'] = Module_Category::getChildsOptions($cates['data']['items'],$cateid);
		Uri::setPrevPage();
		// 品牌列表
		$brands = Module_Brand::getItems(false,'order by name',-1);
		$brandid = isset($filter['brandid'])?$filter['brandid']:'';
		$datas['brandoptions'] = SForm::buildOptions($brands['list'],'id','name',$brandid);
		$this->view->load('manage/m_products',$datas);
	}
	// 编辑商品
	public function edit(){
		// 表单提交
		if (isset($_POST['id'])) {

			// echo json_encode($_POST);
			// die();
			$id = Uri::post('id',0);
			$t = time();
			$attrs = array(
					'cateid' => Uri::post('cateid',0),
					'brandid' => Uri::post('brandid',0),
					'title' => Uri::post('title'),
					'subtitle' => Uri::post('subtitle'),
					'summary' => Uri::post('summary'),
					'content' => $_POST['content'],
					'tags' => Uri::post('tags'),
					'ishot' => Uri::post('ishot',0),
					'status' => Uri::post('status',0),
					'updatedate'=>$t,
					'lastdate'=>$t,
				);
			// 必填项，判断
			$check = FormVerify::rule(
				array(FormVerify::len($attrs['title'],Module_Product::MINTITLE),'标题长度不能小于'.Module_Product::MINTITLE),
				array(FormVerify::must($attrs['content']),'商品详情不能为空'),
				array(FormVerify::must($attrs['brandid']),'品牌不能为空'),
				array(FormVerify::must($attrs['cateid']),'分类不能为空')
				);
			if ($check!==true) {
				$ret['msg'] = $check;
				die(json_encode($ret));
			}
			if($id == 0){
				$SUSER = Module_Manager::getloginUser();
				$attrs['userid']  = $SUSER['id'];
				$attrs['username']  = $SUSER['username'];
				$attrs['createdate']  = $t;
			}

			$ret = Module_Product::setItem($attrs,$id);
			if ($ret['code']<=0) {
				die(json_encode($ret));
			}else{
				// 处理封面图
				$coverids = Uri::post('uplodify_ids');
				$coverpaths = Uri::post('uplodify_fpaths');
				$covernames = Uri::post('uplodify_fnames');
				$coveronames = Uri::post('uplodify_onames');
				if (!empty($coverpaths)) {
					$ok = Module_Product::setCovers($coverids,$coverpaths,$covernames,$coveronames,$ret['data']);
					if (!$ok) {
						$ret['msg'] = '封面保存失败';
						die(json_encode($ret));
					}
				}
				// 添加日志
				Module_log::setItem(array('message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Product::TNAME));
			}
			Uri::build('manage/product','lists',false,true);
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Product::getItem($params[0]);
			if ($ret['code']>0) {
				$datas['oitem'] = $ret['data'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg'].$GLOBALS['db']->getLastSql());
				Uri::build('manage/product','lists',false,true);
			}
		}
		// 分类
		$cates = Module_Category::getChilds(0,0,array('moduleid'=>self::MODULEID));
		$datas['cates'] = $cates['data']['items'];
		$cateid = isset($datas['oitem'])?$datas['oitem']['cateid']:0;
		$datas['options'] = Module_Category::getChildsOptions($datas['cates'],$cateid);
		// 品牌列表
		$brands = Module_Brand::getItems(false,'order by name',-1);
		$brandid = isset($datas['oitem'])?$datas['oitem']['brandid']:'';
		$datas['brandoptions'] = SForm::buildOptions($brands['list'],'id','name',$brandid);

		$this->view->load('manage/m_productedit',$datas);
	}
	// 删除商品
	public function del(){
		$params = Uri::getParams();
		$params = $params['params'];
		if (count($params)>0) {
			$ret = Module_Product::delItem($params[0]);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('message'=>'操作id '.$params[0].';操作库:'.Module_Product::TNAME));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
	// 文章分类管理
	public function cate(){
		Uri::setPrevPage();
		Uri::redirect('/manage/category/lists/?moduleid='.self::MODULEID);
	}
	//
	public function comm(){
		Uri::setPrevPage();
		Uri::redirect('/manage/comment/lists/?moduleid='.self::MODULEID);
	}
	// 商品属性管理
	public function prop(){
		$datas = array('moduleid'=>self::MODULEID);
		$props = Module_Prop::getItems($datas,false,-1);
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['props'] = $props['list'];
		Uri::setPrevPage();
		$this->view->load('manage/m_prop',$datas);
	}
	// 编辑属性
	public function propedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$moduleid = Uri::post('moduleid',0);
			$ret = Module_Prop::setItem(array('name'=>Uri::post('name'),
								'vals'=>Uri::post('vals'),
								'status'=>Uri::post('status'),
								'moduleid'=>$moduleid,
								'desc'=>Uri::post('desc'),),$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('message'=>'操作id '.($id==0?$ret['data']:$id).';分类对应模块moduleid '.$moduleid.';操作库:'.Module_Prop::TNAME));
			}
			Uri::redirect(Uri::getPrevPage());
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Prop::getItem($params[0]);
			if ($ret['code']>0) {
				$datas['prop'] = $ret['data'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg']);
				Uri::redirect(Uri::getPrevPage());
			}
		}
		$this->view->load('manage/m_propedit',$datas);
	}
	// 删除属性
	public function propdel(){
		$params = Uri::getParams();
		$params = $params['params'];
		if (count($params)>0) {

			// 检查占用情况, 不能直接删除???!!! doing

			$ret = Module_Prop::delItem($params[0]);
			// 添加日志
			if ($ret['code']>0) {
				Module_log::setItem(array('message'=>'操作id '.$params[0].',模块id '.$moduleid.';操作库:'.Module_Prop::TNAME));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
}