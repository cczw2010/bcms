<?php
// 通知模块数据库记录类  未实验
class Module_Notify{
	const APPID = 19;
	const APPNAME = '通知模块';
	const TNAME = 't_notify';

	public static $ObjTypes = array('article'=>'文章',	//收藏，喜欢，点赞，评论
																	'product'=>'商品',//.....
																	'comment'=>'评论',//点赞，反对，再评论
																	'user'=>'用户', //关注，评论
																	);
	/**
 	 * 根据id获取内容
 	 * @param  int $id 内容id
 	 * @return array
 	 */
	static public function getItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetch_array($query)) {
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '内容不存在';
		}
		return $ret;
	}
	/**
	 * 获取列表
	 * @param  array	$cond 查询条件
	 * @param  string $orderby 排序条件字符串
	 * @param  int 		$page 页码（-1代表全部）
	 * @param  int 		$psize 每页数量
	 * @return array
	 */
	static function getItems($cond=array(),$orderby='',$page=1,$psize=10){
		return $GLOBALS['db']->select(self::TNAME,$cond,'id',$orderby,$page,$psize);
	}

	/**
	 * 新增|修改通知,改方法不判断登陆 请自行判断
	 * @param array $arrs 编辑的键值对,不能包含id;一般修改只改status
	 * @param $id 要修改的内容id,
	 * @return 返回$id
	 */
	static public function setItem($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		if (empty($id)) {
			// 新增时必填项判断
			$check = FormVerify::rule(
				array(isset($arrs['userid']) && FormVerify::must($arrs['userid']),'用户id不能为空'),
				array(isset($arrs['objid']) && FormVerify::must($arrs['objid']),'操作对象id不能为空'),
				array(isset($arrs['objtype']) && FormVerify::must($arrs['objtype']),'操作对象类型不能为空'),
				array(isset($arrs['msg']) && FormVerify::must($arrs['msg']),'消息内容不能为空')
				);
			if ($check!==true) {
				$ret['msg'] = $check;
				return $ret;
			}
			$arrs['msg'] = addslashes(stripslashes($arrs['msg']));
			$arrs['createdate'] = $_SERVER['REQUEST_TIME'];
			$arrs['lastip'] = Helper::getClientIp();
			$id = $GLOBALS['db']->insert(self::TNAME,$arrs);
			if (empty($id)) {
				$ret['msg'] = '更新失败';
			}
		}else{
			$user = Module_User::getLoginUser();
			// 判断是否存在
			$item = self::getItem($id);
			if ($item['code']<0) {
				$ret['msg'] = '要编辑的内容不存在';
			}elseif($item['data']['touid']>0 && $item['data']['touid']==$user['id']){
				//如果有touid 则判断当前用户是不是touid 
				$ret['msg'] = '对不起，你没有操作的权限';
			}else{
				$arrs['updatedate'] = $_SERVER['REQUEST_TIME'];
				$arrs['operid'] = $user['id'];
				$arrs['opername'] = $user['username'];
				$arrs['lastip'] = Helper::getClientIp();
				// 更新数据
				$result = $GLOBALS['db']->update(self::TNAME,$arrs,array('id'=>$id));
				if (!$result) {
					$ret['msg'] = '更新失败';
				}
			}
		}
		
		if ($id>0&&empty($ret['msg'])) {
			$ret['code'] = 1;
			$ret['data'] = $id;
			$ret['msg'] = '操作成功';
		}
		return $ret;
	}
	/**
 	 * 删除内容
 	 * @param  int $id 日志id
 	 * @return array
 	 */
	static public function delItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		$cate = self::getItem($id);
		if ($cate['code']>0) {
			$GLOBALS['db']->query("delete from ".self::TNAME.' where id='.$id);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affected_rows();
		}else{
			$ret['msg']='内容不存在';
		}
		return $ret;
	}

	/**
	 * 批量删除内容,慎重
	 * @param  array $conds 条件,不能为空 防止误清空
	 * @return array
	 */
	static public function delItems($conds){
		$ret = array('code'=>-1,'msg'=>'');
		if (empty($conds)) {
			$ret['msg'] = '条件不能为空';
		}else{
			$GLOBALS['db']->delete(self::TNAME,$conds);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affected_rows();
		}
		return $ret;
	}
}
