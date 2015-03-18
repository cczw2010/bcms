<?php
	// 一些配置信息和系统级页面的综合处理
	class Setting{
		const ERRNAME = '_x_errmsg';

		public function info(){
			$datas = array();
			$datas['sinfo'] = Helper::getSystemInfo();
			$datas['winfo'] = Helper::getSiteInfo();
			$datas['dbinfo'] = $GLOBALS['db']->get_db_info();
			$datas['uainfo'] = new Useragent();
			$this->view->load('manage/m_info',$datas);
		}
		// 缓存处理
		public function cache(){
			$datas = array();
			if (isset($_GET['op'])) {
				$op = Uri::get('op');
				$group = $op=='all'?false:$op;
				if (isset($GLOBALS['cache_file'])) {
					$GLOBALS['cache_file']->clear($group);
				}
				if (isset($GLOBALS['cache_memcache'])) {
					$GLOBALS['cache_memcache']->clear($group);
				}
				$datas['msg'] = '操作成功 > '.$group.'>'.date('Y-m-d H:i:s');
			}
			$this->view->load('manage/m_cache',$datas);
		}
		// log列表
		public function logs(){
			$datas = array();
			$datas['errmsg'] = Helper::getSession(self::ERRNAME,true);

			$params = Uri::getParams();
			$params = $params['params'];
			$page = !empty($params[0])?$params[0]:1;
			$psize = 20;
			// 过滤条件
			$conds = array();	//检索条件
			$pageParams = array();//分页搜索参数
			if (!empty($_REQUEST['key'])) {
				$conds['key'] = 'like "%'.$_REQUEST['key'].'%"';
				$pageParams['key'] = $_REQUEST['key'];
			}
			if (!empty($_REQUEST['modulename'])) {
				$conds['modulename'] = 'like "%'.$_REQUEST['modulename'].'%"';
				$pageParams['modulename'] = $_REQUEST['modulename'];
			}
			if (!empty($_REQUEST['username'])) {
				$conds['username'] = 'like "%'.$_REQUEST['username'].'%"';
				$pageParams['username'] = $_REQUEST['username'];
			}
			if (!empty($_REQUEST['key'])) {
				$conds['key'] = 'like "%'.$_REQUEST['key'].'%"';
				$pageParams['key'] = $_REQUEST['key'];
			}
			if (!empty($_REQUEST['createdate'])) {
				$conds[] = 'FROM_UNIXTIME(createdate,"%Y-%m-%d")="'.date('Y-m-d',strtotime($_REQUEST['createdate'])).'"';
				$pageParams['createdate'] = $_REQUEST['createdate'];
			}
			// 检索
			$datas['logs'] = Module_Log::getItems($conds,'order by id desc',$page,$psize);
			$datas['pages'] = multiPages($page,$psize,$datas['logs']['total'],$pageParams,true);		
			$this->view->load('manage/m_logs',$datas);
		}
		// 数据库备份
		public function dbback(){
			if (isset($_POST['dumppath'])) {
				$mysqldump = Uri::post('mysqldump','mysqldump');
				$savepath = Uri::post('savepath','backup');
				$savetype = Uri::post('savetype','all');
				$extstr = $savetype == 'all'?'':'-d';
				$t = time();
				$dbname = $GLOBALS['config']['db']['dbname'];
				$uname = $GLOBALS['config']['db']['user'];
				$upass = $GLOBALS['config']['db']['pass'];
				$host = $GLOBALS['config']['db']['host'];
				$port = $GLOBALS['config']['db']['port'];

				$backuppath = rtrim($savepath,'/').'/';
				$filename = $backuppath.$dbname.'-'.$savetype.date('Y-m-d',$t).'-'.$t.'.sql';
				$logname = $backuppath.'backup.log';
				//--add-drop-database  --add-drop-table --log-error=name --set-charset
				$command = 'mysqldump -h'.$host.' -P'.$port.' -u '.$uname.' -p'.$upass.' -F  --force --log-error='.$logname.' '.$extstr.' '.$dbname;
				// $command = 'mysqldump -h'.$host.' -P'.$port.' -u '.$uname.' -p'.$upass.' -F  --force --log-error='.$logname.' '.$extstr.' '.$dbname.' > '.$filename;

				// 2>&1 使用 2>&1, 命令就会输出shell执行时的错误到$output变量, 输出该变量即可分析。
				// 第三个参数执行的状态，0表示成功，其他都表示失败。
				$output = array();
				$ret;
				exec($command.' 2>&1',$output,$ret);
				if ($ret===0) {
					$i = stripos($output[0], 'Warning')>=0?1:0;
					for ($i,$len=count($output); $i < $len; $i++) { 
						SFile::write($filename,$output[$i].PHP_EOL);
					}
					unset($output);
					$datas['code'] = 1;
					$datas['msg'] = '导出成功！'.date('Y-m-d H:i:s',$t);
				}else{
					$err = json_encode($output);
					$datas['msg'] = '导出失败！'.$err;
				}

				$datas['mysqldump'] = $mysqldump;
				$datas['savepath'] = $savepath;
				die(json_encode($datas));
			}else{
				$this->view->load('manage/m_dbback',$datas);
			}
		}
		// 区域管理
		public function citys(){
			Uri::setPrevPage();
			Uri::redirect('/manage/city/lists/?appid=0');
		}
		// 敏感词
		public function verify(){
			$datas = array();
			// 表单提交
			if (isset($_POST['id'])) {
				$id = Uri::post('id');
				$attr = array('banned'=>Uri::post('banned'),
											'filters'=>Uri::post('filters'),
											'status'=>Uri::post('status',0));
				$ret = Module_Sword::setItem($attr,$id);
				$datas['msg'] = $ret['msg'].' >'.date('Y-m-d H:i:s');
			}
			$ret = Module_Sword::getItem();
			if ($ret['code']==1) {
				$datas['oitem'] = $ret['data'];
			}
			$this->view->load('manage/m_pverify',$datas);
		}
		//支付方式
		public function payment(){
			$pays = Module_Payment::getItems(array('status'=>1));
			$datas = array('items'=>$pays['list']);
			$this->view->load('manage/m_payments',$datas);
		}
		// 支付方式编辑
		public function payedit(){
			// 表单提交
			if (!empty($_POST['id'])) {
				$id = Uri::post('id');
				$ret = Module_Payment::setItem(array('name'=>Uri::post('name'),
																				'key'=>Uri::post('key'),
																				'appid'=>Uri::post('appid'),
																				'appkey'=>Uri::post('appkey'),
																				'appaccount'=>Uri::post('appaccount'),
																				'notifyurl'=>Uri::post('notifyurl'),
																				'returnurl'=>Uri::post('returnurl'),
																				'status'=>Uri::post('status',0),
																				'desc'=>Uri::post('desc')),$id);
				die(json_encode($ret));
			}
			$key = Uri::get('key');
			$ret = Module_Payment::getItemByKey($key);
			$datas = array('oitem'=>$ret['data']);
			$this->view->load('manage/m_paymentedit',$datas);
		}
		// 第三方登陆配置页
		public function thirdlogincfg(){
			$pays = Module_ThirdLogin::getAppCfgs();
			$datas = array('items'=>$pays['list']);
			$this->view->load('manage/m_appcfgs',$datas);
		}
		// 第三方登陆配置编辑
		public function thirdloginedit(){
			// 表单提交
			if (!empty($_POST['id'])) {
				$id = Uri::post('id');
				$attr = array('name'=>Uri::post('name'),
											'appid'=>Uri::post('appid'),
											'key'=>Uri::post('key'),
											'appkey'=>Uri::post('appkey'),
											'secret'=>Uri::post('secret'),
											'callback'=>Uri::post('callback'),
											'status'=>Uri::post('status',0));
				if (isset($_POST['scope'])){
					$scopes = Uri::post('scope');
					$attr['scope'] = empty($scopes)?'':implode(',', $scopes);
				}
				$ret = Module_ThirdLogin::setAppCfg($attr,$id);
				die(json_encode($ret));
			}

			$key = Uri::get('key');
			$ret = Module_ThirdLogin::getAppCfgByKey($key);
			$datas = array('oitem'=>$ret['data']);
			$this->view->load('manage/m_appcfgedit',$datas);
		}
	}