<?php
// controller
// 微信公众平台
Class Weixin{
	private $wxobj=null;
	private $wxmodel=null;
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
		// 初始化微信对象
		include_once BASEPATH."/datas/weixin/wechat.class.php";
		$this->wxobj = new Wechat($GLOBALS['config']['weixin']); //创建实例对象
		$this->wxmodel = $this->model->load('manage/model_weixin');
	}
	public function index(){
	}
	// 微信菜单
	public function menu(){
		$model = $this->model->load('manage/model_weixin');
		if($data = $model->getMenu($this->wxobj)){
			$html='';
			foreach ($data['menu']['button'] as $m) {
				$this->buildTrMenu($html,$m);
			}
			$data['trs'] = $html;
		}
		$this->view->load('manage/m_wxmenu',$data);
	}

	// 保存编辑过的微信菜单
	public function medit(){
		if (Http::isPost()) {
			$mname = Uri::post('mname');
			$mtype = Uri::post('mtype');
			$mval = Uri::post('mval');
			$mdepth = Uri::post('mdepth');

			if (count($mname) == count($mtype)) {
				$menudata = array();$midx = -1;$submidx=0;
				foreach ($mname as $k=>$v) {
					if (empty($v)) {
						continue;
					}
					$item = array('name'=>$v);
					if (!empty($mtype[$k])) {
						$item['type'] = $mtype[$k];
						switch ($item["type"]) {
							case 'view':
								$_k = 'url';
								break;
							case 'media_id':
							case 'view_limited':
								$_k = 'media_id';
								break;
							default:
								$_k = 'key';
								break;
						}
						$item[$_k] = $mval[$k];
					}
					// 一级菜单
					if ($mdepth[$k]==0) {
						$menudata[++$midx] = $item;
						$submidx = 0;
					}else{
						if (!isset($menudata[$midx]['sub_button'])) {
							$menudata[$midx]['sub_button'] = array();
						}
						$menudata[$midx]['sub_button'][$submidx++] = $item;
					}

				}
				// var_dump(json_encode($menudata));die();
				// var_dump($menudata);die();
				$data = array("button"=>$menudata);
				if ($this->wxmodel->setMenu($this->wxobj,$data)){
					// 载入列表
					$this->menu();
					die();
				}
			}
		}
		die(json_encode(array('ret'=>0,'msg'=>'操作失败')));
	}

	// 菜单迭代表格化
	private function buildTrMenu(&$html,$mitem,$depth=0){
		$hasSub = !empty($mitem['sub_button']);
		$clsname = $depth==0?'wxtopmenu':'wxsubmenu';
		$name = $mitem['name'];

		if (!$hasSub) {
			$type = $mitem['type'];$val = '';
			switch ($type) {
				case 'view':
					$val = $mitem['url'];
					break;
				case 'media_id':
				case 'view_limited':
					$val = $mitem['media_id'];
					break;
				default:
					$val = $mitem['key'];
					break;
			}
			$type = "<input disabled name='mtype[]' value='$type'>";
			$val = "<input disabled name='mval[]' value='$val'>";
		}else{
			$type = '<input disabled name="mtype[]" value="">';
			$val = '<input disabled name="mval[]" value="">';
		}
		$edithtml = '<a class="delwxmitem hide">删除</a>';
		if ($depth==0) {
			 $edithtml.=' <a class="addwxsubmenu hide">添加下级菜单</a>';
		}

		$html.="<tr class='$clsname'><td><input disabled name='mname[]' value='$name'></td>".
				"<td>$type</td>".
				"<td>$val</td><td>$edithtml<input class='hide' name='mdepth[]' value='$depth'></td></tr>";
		if ($hasSub) {
			foreach ($mitem['sub_button'] as $m) {
				$this->buildTrMenu($html,$m,$depth+1);
			}
		}
		return $html;
	}
}

