<?php
// home页的model层
class Model_home{

	// 获取首页数据
	public function getIndex(){
		// 检查缓存
		if(!($datas = $GLOBALS['cache']->get($GLOBALS['config']['db']['group'],'home_index'))){
			$datas = array(
				'ip' => Helper::getClientIp(),
				't' => time(),
				'user' => Module_User::getLoginUser()
			);
			// $GLOBALS['cache']->set($GLOBALS['config']['db']['group'],'home_index',$datas);
			$GLOBALS['cache']->set($GLOBALS['config']['db']['group'],'home_index',$datas,10);
		}
		return $datas;
	}
}