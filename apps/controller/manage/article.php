<?php
// 文章管理类
class Article{
	const ERRNAME = '_x_errmsg';
	const MODULEID = 'article';

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
	// 文章列表
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
		if (isset($_REQUEST['status']) && $_REQUEST['status']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['status'] = $_REQUEST['status'];
			$pageParams['status'] = $_REQUEST['status'];
		}
		if (isset($_REQUEST['istop']) && $_REQUEST['istop']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['istop'] = $_REQUEST['istop'];
			$pageParams['istop'] = $_REQUEST['istop'];
		}
		if (isset($_REQUEST['ishot']) && $_REQUEST['ishot']!='-1') {
			// 状态可能为0,所以不能用empty来判断
			$conds['ishot'] = $_REQUEST['ishot'];
			$pageParams['ishot'] = $_REQUEST['ishot'];
		}
		$datas = array('moduleid'=>self::MODULEID);
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['filter'] = $pageParams;
		$datas['items'] = Module_Article::getItems($conds,'order by id desc',$page,$psize);
		$datas['pages'] = multiPages4Ace($page,$psize,$datas['items']['total'],$pageParams,true);
		// 分类
		$cates = Module_Category::getChilds(0,0,array('moduleid'=>self::MODULEID),-1);
		$cateid = isset($_REQUEST['cateid'])?$_REQUEST['cateid']:0;
		$datas['options'] = Module_Category::getChildsOptions($cates['data']['items'],$cateid);
		Uri::setPrevPage();

		$this->view->load('manage/m_articles',$datas);
	}
	// 编辑文章
	public function edit(){
		// 表单提交
		if (isset($_POST['id'])) {
			$id = Uri::post('id',0);
			$t = time();
			$attrs = array(
					'cateid' => Uri::post('cateid',0),
					'title' => Uri::post('title'),
					'subtitle' => Uri::post('subtitle'),
					'summary' => Uri::post('summary'),
					'content' => $_POST['content'],
					'tags' => Uri::post('tags'),
					'istop' => Uri::post('istop',0),
					'ishot' => Uri::post('ishot',0),
					'status' => Uri::post('status',0),
					'updatedate'=>$t,
					'lastdate'=>$t,
				);
			if($id == 0){
				$SUSER = Module_Manager::getloginUser();
				$attrs['userid']  = $SUSER['id'];
				$attrs['username']  = $SUSER['username'];
				$attrs['createdate']  = $t;
			}
			$ret = Module_Article::setItem($attrs,$id);
			if ($ret['code']<=0) {
				die(json_encode($ret));
			}else{
				// 处理封面图
				$coverids = Uri::post('uplodify_ids');
				$coverpaths = Uri::post('uplodify_fpaths');
				$covernames = Uri::post('uplodify_fnames');
				$coveronames = Uri::post('uplodify_onames');
				// dump($coverids,$coverpaths,$covernames);die();

				if (!empty($coverpaths)) {
					$ok = Module_Article::setCovers($coverids,$coverpaths,$covernames,$coveronames,$ret['data']);
					if (!$ok) {
						$ret['msg'] = '封面保存失败';
						die(json_encode($ret));
					}
				}
				// 添加日志
				Module_log::setItem(array('message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Article::TNAME));
			}
			Uri::build('manage/article','lists',false,true);
		}
		// 不是表单提交
		$params = Uri::getParams();
		$params = $params['params'];
		$datas = array();
		if (count($params)>0) {
			$ret = Module_Article::getItem($params[0]);
			if ($ret['code']>0) {
				$datas['oitem'] = $ret['data'];
			}else{
				Helper::setSession(self::ERRNAME,$ret['msg'].$GLOBALS['db']->getLastSql());
				Uri::build('manage/article','lists',false,true);
			}
		}
		$cates = Module_Category::getChilds(0,0,array('moduleid'=>self::MODULEID));
		$datas['cates'] = $cates['data']['items'];
		$cateid = isset($datas['oitem']['cateid'])?$datas['oitem']['cateid']:0;
		$datas['options'] = Module_Category::getChildsOptions($datas['cates'],$cateid);
		$this->view->load('manage/m_articleedit',$datas);
	}
	// 删除文章
	public function del(){
		$params = Uri::getParams();
		$params = $params['params'];
		if (count($params)>0) {
			$ret = Module_Article::delItem($params[0]);
		}
		// 添加日志
		if ($ret['code']>0) {
			Module_log::setItem(array('message'=>'操作id '.$params[0].';操作库:'.Module_Article::TNAME));
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
		$objid = Uri::get('objid',0);
		Uri::setPrevPage();
		Uri::redirect('/manage/comment/lists/?moduleid='.self::MODULEID.'&objid='.$objid);
	}
}