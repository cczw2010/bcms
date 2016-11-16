<?php
// manage/weixin 页的model层
class Model_weixin{
	// 默认菜单数据
	private $menuData = array(

		);

	// 获取菜单
	public function getMenu($wxobj){
		// 检查缓存
		if(!($datas = $GLOBALS['cache']->get('weixin','menu'))){
			if($datas = $wxobj->getMenu()){
				$GLOBALS['cache']->set('weixin','menu',$datas,10000);
			}
		}
		return $datas;
	}

	// 设置菜单
	public function setMenu($wxobj,$mdata){
		if($result = $wxobj->createMenu($mdata)){
			// 清空系统旧数据缓存
			$GLOBALS['cache']->clear('weixin');
		}
		return $result;
	}


	// 获取测试菜单数据
	private function testMenuData(){
		return array(
				"button"=>array(
				    0=>array(
				    	'name'=>'常规事件',
				    	'sub_button'=>array(
			    			0=>array(
				    			"type"=>"view",
				            	"name"=>"跳转到url",
				            	"url"=>"http://www.baidu.com/"
			            	),
			            	1=>array(
					        	"type"=>"click",
					        	"name"=>"点击推事件",
					        	"key"=>"E_CCC1"
						    ),
				    	)
					),
					1=>array(
				    	'name'=>'其他事件',
				    	'sub_button'=>array(
				            	0=>array(
					    			"type"=>"pic_sysphoto",
					            	"name"=>"系统拍照发图",
					            	"key"=>"rselfmenu_1_0"
				            	),
				            	1=>array(
					    			"type"=>"pic_photo_or_album",
				                    "name"=>"拍照或者相册发图",
				                    "key"=>"rselfmenu_1_1",
				            	),
				            	2=>array(
					    			"type"=> "pic_weixin",
                    				"name"=> "微信相册发图",
                    				"key"=> "rselfmenu_1_2",
                    				"sub_button"=> [ ]
				            	),
				    	)
					),
					2=>array(
						'name'=>'其他事件1',
						'sub_button' => array(
								0=>array(
					    			"type"=>"scancode_waitmsg",
					            	"name"=>"扫码带提示",
					            	"key"=>"scancode_waitmsg"
				            	),
				            	1=>array(
					    			"type"=>"scancode_push",
					            	"name"=>"扫码推事件",
					            	"key"=>"scancode_push"
				            	),
				            	2=>array(
				            		"name"=> "发送位置",
						            "type"=> "location_select",
						            "key"=> "rselfmenu_2_0"
				            	)
						)
					)
				)
			);
	}

}