<?php
// 文章管理类
class Article{
	const ERRNAME = '_x_errmsg';

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
		$datas = array('appid'=>Module_Article::APPID);
		$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);
		$datas['filter'] = $pageParams;
		$datas['items'] = Module_Article::getItems($conds,'order by id desc',$page,$psize);
		$datas['pages'] = multiPages($page,$psize,$datas['items']['total'],$pageParams,true);		
		// 分类
		$cates = Module_Category::getChilds(0,0,array('appid'=>Module_Article::APPID),-1);
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
				$SUSER = Module_User::getloginUser(true);
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
				Module_log::setItem(array('modulename'=>Module_Article::APPNAME,
						'moduleid'=>Module_Article::APPID,
						'key'=>($id==0?'新增':'更新'),
						'message'=>'操作id '.($id==0?$ret['data']:$id).';操作库:'.Module_Article::TNAME
						));
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
		$cates = Module_Category::getChilds(0,0,array('appid'=>Module_Article::APPID));
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
			Module_log::setItem(array('modulename'=>Module_Article::APPNAME,
					'moduleid'=>Module_Article::APPID,
					'key'=>'删除',
					'message'=>'操作id '.$params[0].';操作库:'.Module_Article::TNAME
					));
		}
		// 不管删除成功与否直接跳转
		Uri::redirect(Uri::getPrevPage());
	}

	// 文章分类管理
	public function cate(){
		Uri::setPrevPage();
		Uri::redirect('/manage/category/lists/?appid='.Module_Article::APPID);
	}
	// 
	public function comm(){
		Uri::setPrevPage();
		Uri::redirect('/manage/comment/lists/?appid='.Module_Article::APPID);
	}
}