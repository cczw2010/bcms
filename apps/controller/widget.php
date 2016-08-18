<?php
/**
 * 一些通用的ajax获取的接口
 */
class Widget{
	// 上传图片通用方法，可以扩展上传类型
	public function upload(){
		$ret = array('code' =>-1 ,'msg'=>'' );
		$user = Module_User::getLoginUser();

		if ($user) {
			/****** 通用参数*******/
			//文件的表单域名称
			$fiedname  = Uri::post('_fieldname','file');	
			// 对象类型（上传的用途,生成对应的文件夹，并有对应的回调处理,可为空）
			$objtype  = Uri::post('objtype');

			// 生成实际上传地址
			$ufolder = Module_User::getAlbumBase($user['id']);
			$uppath =BASEPATH.$GLOBALS['config']['uploadpath'].'/'.$ufolder.'/';
			if (!empty($objtype)) {
				$uppath.=$objtype;
			}
			// 上传
			$up = new SUpload($fiedname,$uppath);
			if($up->upload()){
				$filepath = $up->filePath();
				$fpath = Uri::path2url($up -> UpFilePath());
    		$fname = $up -> UpFile();
				// $fileurl = Uri::path2url($filepath);
				// $ret['data'] = $fileurl;
				$ret['data'] = array(
					'fpath' => $fpath,
					'fname' => $fname,
					'oname'=>$up->getName(),
					'osize'=>round($up->getSize('M'),2),
					'oext'=>$up->getExt(),
					);
				//根据对象类型来进行对应的操作
				// switch ($objtype) {
				// }
				if (empty($ret['msg'])) {
					$ret['code'] = 1;
				}
			}else{
  			$ret['msg'] = '上传失败！请重试';
			}
		}else{
			$ret['msg'] = '请先登录！';
		}
		// 输出json
		echo json_encode($ret);
	}
	// 获取验证码,直接输出图片
	public function captcha(){
		$cap = new Captcha();
 		$cap->buildAndExportImage();
	}
}