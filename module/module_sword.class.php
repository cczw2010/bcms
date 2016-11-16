<?php 
// 敏感词验证过滤,目前注册部分使用了敏感词校验和文章内容使用了敏感词过滤 
 class Module_Sword{
	const TNAME = 't_sensitiveword';

	// 获取验证过滤数据，带缓存
	public static function getItem(){
		$cachekey = 'xm_db_verify';
		$cachgroup = 'setting';
		// 检查缓存
		if(!($datas = $GLOBALS['cache_file']->get($cachgroup,$cachekey))){
			$ret = array('code'=>-1,'msg'=>'');
			$query = $GLOBALS['db']->query('select * from '.self::TNAME);
			if ($item = $GLOBALS['db']->fetchArray($query)) {
				$ret['code'] = 1;
				$ret['data'] = $item;
			}else{
				$ret['msg'] = '信息不存在';
			}
			$GLOBALS['cache_file']->set($cachgroup,$cachekey,$ret);
		}else{
			$ret = $datas;
		}
		return $ret;
	}
	/**
	 * （编辑|新增），会更新缓存
	 * @param array $attr 编辑的键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 返回$id
	 */
	public static function setItem($attr,$id=0){
		$ret = array('code'=>-1,'msg'=>'');
		if (empty($id)) {
			$id = $GLOBALS['db']->insert(self::TNAME,$attr);
		}else{
			$GLOBALS['db']->update(self::TNAME,$attr,array('id'=>$id));
		}
		$ret['code'] = 1;
		$ret['msg'] = '更新成功';
		$ret['data'] = $id;
		// 删除缓存
		$cachekey = 'xm_db_verify';
		$cachgroup = 'setting';
		$GLOBALS['cache_file']->delete($cachgroup,$cachekey);
		return $ret;
	}

	/**
	 * 敏感词过滤,并以*号替代，可用于文章，详情等内容类型替换。
	 * 后期考虑与php本地插件结合，提升效率,并且敏感词可能比较多 从文本文件获取而不是从数据库
	 * @param  string $str     要检测的字符串
	 * @param  array $badword	 过滤词汇数组,如果为空,则默认从数敏感词库取
	 * @return string 
	 */
	public static function filter($str,$badword=false){
		if (empty($badword)) {
			$ret = self::getItem();
			if ($ret['code']==1 && $ret['data']['status']==1) {
				$badword = 	$ret['data']['filters'];
				$badword = explode('|', $badword);
			}
		}
		if (!empty($badword)) {
			$badword = array_combine($badword,array_fill(0,count($badword),'*'));
			$str = strtr($str, $badword);
		}
		return $str;
	}
	/**
	 * 敏感词汇校验，可用于表单基础信息检测（用户名，签名，昵称等）
	 * 后期考虑与php本地插件结合，提升效率
	 * @param  string $str     要检测的字符串
	 * @param  array $badword	 禁止词汇数组,如果为空,则默认从数敏感词库取
	 * @return boolean 				如果发现禁止词汇立即返回空
	 */
	public static function banned($str,$badword=false){
		if (empty($badword)) {
			$ret = self::getItem();
			if ($ret['code']==1 && $ret['data']['status']==1) {
				$badword = 	$ret['data']['banned'];
				$badword = explode('|', $badword);
			}
		}
		if (!empty($badword)) {
			foreach ($badword as $word) {
				if (stripos($str, $word)!==false) {
					return false;
				}
			}
		}
		return true;
	}
 }