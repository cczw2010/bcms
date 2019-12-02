<?php
//  图片管理类
class Picture{
	const ERRNAME = '_x_errmsg';
	public $moduleid = '';   //默认的moduleid为空
	public $num = 0;		 //0代表不限制

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
		// moduleid处理
		if(isset($_GET['moduleid'])){
			$this->moduleid = $_GET['moduleid'];
		}
		if(!empty($_GET['num'])){
			$this->num = $_GET['num'];
		}
  }
    // 编辑图集合   基于moduelid
  public function edit(){
    if(empty($this->moduleid)){
        die('no module');
    }
    if(!empty($_POST['moduleid'])){
			$ret = array('code'=>-1,'msg'=>'参数错误');
      $ids = Uri::post('uplodify_ids');
			$paths = Uri::post('uplodify_fpaths');
			$names = Uri::post('uplodify_fnames');
			$descs = Uri::post('uplodify_descs');
			$links = Uri::post('uplodify_links');
			// dump($ids,$paths,$names);die();
			if (!empty($paths)) {
				$ok = Module_Attach::setPics($this->moduleid,1,$ids,$paths,$names,$descs,$links);
				if ($ok) {
					$ret['code'] = 1;
					$ret['msg'] = '更新成功';
				}else{
					$ret['msg'] = '更新失败';
				}
			}
			die(json_encode($ret));
    }
    // 尺寸列表
    $sizes = array(
      'mindex'=>'首页背景图集:<br>1920x1145',        //首页背景图集
      'building'=>'途远建造Banner:<br>1920x482',       //途远建造 - banner
      'mabout'=>'关于途远Banner:<br>1920x482',         //关于途远 - banner
      'business'=>'业务模式Banner:<br>1920x482',       //业务模式 - banner
      'business_slider'=>'业务模式幻灯:<br>737x464', //业务模式 - 幻灯
    );
		$pics = Module_Attach::getItems(array('objtype'=>$this->moduleid),'order by orderid');
		$data = array('moduleid'=>$this->moduleid,
          'num'=>$this->num,
          'sizes'=>$sizes,
					'pics'=>array_values($pics['list']));
		$this->view->load('manage/m_picture',$data);
	}
	
}