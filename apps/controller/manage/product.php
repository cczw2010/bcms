<?php
// 商品管理类
class Product{
	const ERRNAME = '_x_errmsg';
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
				Module_log::setItem(array('modulename'=>Module_Brand::APPNAME,
						'moduleid'=>Module_Brand::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Brand::TNAME
						));
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
				Module_log::setItem(array('modulename'=>Module_Brand::APPNAME,
						'moduleid'=>Module_Brand::APPID,
						'key'=>'删除',
						'message'=>'操作id '.$params[0].';操作库:'.Module_Brand::TNAME
						));
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
		
		$datas = array('appid'=>Module_Product::APPID);
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['filter'] = $pageParams;
		$datas['items'] = Module_Product::getItems($conds,'order by id desc',$page,$psize);
		$datas['pages'] = multiPages($page,$psize,$datas['items']['total'],$pageParams,true);		
		// 分类
		$cates = Module_Category::getChilds(0,0,array('appid'=>Module_Product::APPID),-1);
		$cateid = isset($_REQUEST['cateid'])?$_REQUEST['cateid']:0;
		$datas['options'] = Module_Category::getChildsOptions($cates['data']['items'],$cateid);
		Uri::setPrevPage();
		// 品牌列表
		$brands = Module_Brand::getItems(false,'order by name',-1);
		$brandid = isset($filter['brandid'])?$filter['brandid']:'';
		$datas['brandoptions'] = SForm::build_options($brands['list'],'id','name',$brandid);
		$this->view->load('manage/m_products',$datas);
	}
	// 编辑商品
	public function edit(){
		// 表单提交
		if (isset($_POST['id'])) {
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
					'quantity' => Uri::post('quantity',0),
					'maxbuy' => Uri::post('maxbuy',0),
					'oprice' => Uri::post('oprice',0),
					'price' => Uri::post('price',0),
					'ishot' => Uri::post('ishot',0),
					'status' => Uri::post('status',0),
					'updatedate'=>$t,
					'lastdate'=>$t,
				);
			if($id == 0){
				$SUSER = Module_User::getloginUser(true);
				$attrs['userid']  = $SUSER['id'];
				$attrs['username']  = $SUSER['username'];
				$attrs['sales']  = 0;
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
				Module_log::setItem(array('modulename'=>Module_Product::APPNAME,
						'moduleid'=>Module_Product::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Product::TNAME
						));
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
		$cates = Module_Category::getChilds(0,0,array('appid'=>Module_Product::APPID));
		$datas['cates'] = $cates['data']['items'];
		$cateid = isset($datas['oitem'])?$datas['oitem']['cateid']:0;
		$datas['options'] = Module_Category::getChildsOptions($datas['cates'],$cateid);
		// 品牌列表
		$brands = Module_Brand::getItems(false,'order by name',-1);
		$brandid = isset($datas['oitem'])?$datas['oitem']['brandid']:'';
		$datas['brandoptions'] = SForm::build_options($brands['list'],'id','name',$brandid);

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
				Module_log::setItem(array('modulename'=>Module_Product::APPNAME,
						'moduleid'=>Module_Product::APPID,
						'key'=>'删除',
						'message'=>'操作id '.$params[0].';操作库:'.Module_Product::TNAME
						));
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
	// 文章分类管理
	public function cate(){
		Uri::setPrevPage();
		Uri::redirect('/manage/category/lists/?appid='.Module_Product::APPID);
	}
	// 
	public function comm(){
		Uri::setPrevPage();
		Uri::redirect('/manage/comment/lists/?appid='.Module_Product::APPID);
	}
	// 商品属性管理
	public function prop(){
		$datas = array('appid'=>Module_Product::APPID);
		$props = Module_Prop::getItems($datas,false,-1);
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['props'] = $props['list'];
		$datas['appname']=Module_Product::APPNAME;
		Uri::setPrevPage();
		$this->view->load('manage/m_prop',$datas);
	}
	// 编辑属性
	public function propedit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$appid = Uri::post('appid',0);
			$ret = Module_Prop::setItem(array('name'=>Uri::post('name'),
																		'vals'=>Uri::post('vals'),
																		'status'=>Uri::post('status'),
																		'appid'=>$appid,
																		'desc'=>Uri::post('desc'),),$id);
			if ($ret['code']<0) {
				Helper::setSession(self::ERRNAME,$ret['msg']);
			}else{
				// 添加日志
				Module_log::setItem(array('modulename'=>Module_Prop::APPNAME,
						'moduleid'=>Module_Prop::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';分类对应模块APPID '.$appid.';操作库:'.Module_Prop::TNAME
						));
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
			$cnt = $GLOBALS['db']->result('select count(*) as cnt from '.Module_Prop::TPITEM.' where propid ='.$params[0]);
			if ($cnt>0) {
				Helper::setSession(self::ERRNAME,'该属性被其他模块使用着，不能直接删除。');
			}else{
				$ret = Module_Prop::delItem($params[0]);
				// 添加日志
				if ($ret['code']>0) {
					Module_log::setItem(array('modulename'=>Module_Prop::APPNAME,
							'moduleid'=>Module_Prop::APPID,
							'key'=>'删除',
							'message'=>'操作id '.$params[0].$appid.';操作库:'.Module_Prop::TNAME
							));
				}
			}
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}
}