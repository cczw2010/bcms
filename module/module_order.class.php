<?php
// 订单类
// 🐷暂时没有处理库存， 需要完善
final class Module_Order{
	const TNAME ='t_order';
	const TITEMNAME ='t_order_item';
	const TSHIPPING ='t_shipping';
	const TSHIPPINGLOG ='t_shipping_log';
	// 0未支付（自动），1已支付（自动），2已发货（管理员后台修改），3已完成（彻底完成，包括款项到账，物流配送）（自动），4已取消（手动关闭，取消）,5已过期（服务器自动定期运行任务）
 	public static $statuss = array('待支付','已支付','已发货','已完成','已取消','已过期');
 	// 订单商品类型   普通商品和礼包（暂未实现）
 	public static $itemtype = array('product','gift');
	/**
	 * 获取订单列表,只返回订单表中的信息，不包含订单的详细商品列表
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
 	 * 根据id获取订单详情
 	 * @param  int $id 内容id
 	 * @return array
 	 */
	static public function getItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where id='.$id);
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			// 获取订单商品列表
			$item['items'] = self::getOrderItems($id);
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '内容不存在';
		}
		return $ret;
	}
	/**
	 * 删除订单（同时删除商品）
	 */
	static public function delItem($id){
		$ret = array('code'=>-1,'msg'=>'');
		$cate = self::getItem($id);
		if ($cate['code']>0) {

			$GLOBALS['db']->query("delete from ".self::TNAME.' where id='.$id);
			// 删除关联的商品
			$GLOBALS['db']->query("delete from ".self::TITEMNAME.' where orderid='.$id);
			$ret['code'] = 1;
			$ret['data'] = $GLOBALS['db']->affectedRows();
		}else{
			$ret['msg']='内容不存在';
		}
		return $ret;
	}
	/**
	 * （编辑|新增）订单简介, 不处理订单内的商品（所以金额需要自行计算），可使用setOrderItems处理关联的商品
	 * @param array $arrs 编辑的键值对,不能包含id;
	 * @param int $id 如果有id则为修改，否则为新增
	 * @return 返回$id
	 */
	static public function setItem($arrs,$id=0){
		$ret = array('code'=> -1,'msg'=>'');
		// 必填项，判断
		$check = FormVerify::rule(
			array(FormVerify::must($arrs['totalfee']),'金额总数不能为空'),
			array(FormVerify::must($arrs['paymentid']),'支付方式不能为空'),
			array(FormVerify::must($arrs['shippingid']),'配送方式不能为空'),
			array(FormVerify::must($arrs['addressid']),'配送地址不能为空')
			);
		if ($check!==true) {
			$ret['msg'] = $check;
			return $ret;
		}
		if ($id>0) {
			// 判断是否存在
			$cnt = $GLOBALS['db']->result('select count(*) from '.self::TNAME.' where id='.$id);
			if ($cnt==0) {
				$ret['msg'] = '要编辑的内容不存在';
			}else{
				if (!empty($arrs['descr'])) {
					$arrs['descr'] = addslashes(stripslashes($arrs['descr']));
				}
				//更新数据
				$result = $GLOBALS['db']->update(self::TNAME,$arrs,array('id'=>$id));
				if (!$result) {
					$ret['msg'] = '更新失败';
				}
			}
		}else{
			// 生成新订单号
			$arrs['orderno'] = self::buildOrderNo();
			$id = $GLOBALS['db']->insert(self::TNAME,$arrs);
			if (!empty($id)) {
				$ret['msg'] = '新增失败';
			}
		}
		if ($id>0 && empty($ret['msg'])) {
			$ret['code'] = 1;
			$ret['data'] = $id;
		}
		return $ret;
	}
	/**
	 * 增加订单商品列表，（因为订单商品禁止自行修改，所以只提供新增）
	 * @param array $items 商品列表
	 *        array('product'=>array(1=>1,2=>1,3=>1),	// id=>quantity
	 *        			'gift'=>array(11=>1,22=>2,32=>1))
	 * @param int $orderid 订单id
	 */
	static public function setOrderItems($items,$orderid){
		foreach ($items as $otype=>$oitems) {
			switch ($otype) {
				case 'gift':
					//暂不支持礼包
					break;
				case 'product':
				default:
					$ids = array_keys($oitems);
					$result = Module_Product::getItems(array('id'=>'in ('.implode(',',$ids).')'),false,-1);
					foreach ($result['list'] as  $oitem) {
						$GLOBALS['db']->insert(self::TITEMNAME,array(
								'orderid'=>$orderid,
								'objid'=>$oitem['id'],
								'objtype'=>'product',
								'objname'=>$oitem['title'],
								'quantity'=>$oitems[$oitem['id']],
								'price'=>$oitem['price'],
								'createdate'=>$_SERVER['REQUEST_TIME'],
							));
					}
					break;
			}
		}
	}
	/**
 	 * 根据订单号获取内容
 	 * @param  string $orderno 订单号
 	 * @return array
 	 */
	static public function getOrderByNo($orderno){
		$ret = array('code'=>-1,'msg'=>'');
		$query = $GLOBALS['db']->query('select * from '.self::TNAME.' where orderno='.$orderno);
		if ($item = $GLOBALS['db']->fetchArray($query)) {
			// 获取订单商品列表
			$item['items'] = self::getOrderItems($id);
			$ret['code'] = 1;
			$ret['data'] = $item;
		}else{
			$ret['msg'] = '内容不存在';
		}
		return $ret;
	}
	// 获取某订单的商品列表,关联表中的冗余字段足以
	static public function getOrderItems($id){
		// $items = array();
		$result = $GLOBALS['db']->select(self::TITEMNAME,array('id'=>$id),false,false,-1);
		// foreach ($result['list'] as $item) {
		// 	switch ($item['objtype']) {
		// 		case 'product':
		// 			$ret = Module_Product::getItem($item['objid']);
		// 			if ($ret['code']>0) {
		// 				$items[] = $ret['data'];
		// 			}else{
		// 				// 手动将订单项变成 非正常状态，方便前端判断展示
		// 				$item['status'] = 0;
		// 				$item['statusmsg'] = '该商品不存在，或者已经下架';
		// 				$items[] = $item;
		// 			}
		// 			break;
		// 		case 'gift':
		// 		default:
		// 			$items[] = $item;
		// 			break;
		// 	}
		// }
		return $result;
	}

	// 生成唯一订单号
	static public function buildOrderNo(){
		$prefix = chr(date('y')%26+65).date('md');
		return uniqid($prefix);
	}
}