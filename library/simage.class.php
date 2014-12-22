<?php
 /**文件操作类 by awen   
 	*1 如果发生读写错误，请记得更新文件操作目录的【权限】,
 	*2 依托于Imagick，请安装相关扩展
 */
class SImage{

	// getimagesize 返回的数组第二个参数代表的图片类型
	static $imgTypes = array('','gif','jpg','png','swf','psd','bmp','tiff','tiff','jpc','jp2','jpx','jb2','swc','iff','wbmp','xbm');
	/**
	 * 给图片增加水印,不新生成图片，无返回
	 * @param string $pic :  图片路径
	 * @param string $water :	水印图片路径
	 * @param int int $offx , $offy : 相对于图片右下角坐标，也可以是left，center,right，top,bottom
	 * @return  无   
	 */
	static function addMark($pic , $water , $offx , $offy){
		$im = new Imagick($pic);
		$mark = new Imagick($water);
		$mark->stripImage();

		try {
			$im->enhanceimage();
			$mark->enhanceimage();
		} catch (Exception $e) {
		}

		$picinfo=getimagesize($pic);
		$info=getimagesize($water);
		// 计算水印x坐标
		switch ($offx) {
			case 'left':
				$sX = 0;
				break;
			case 'center':
				$sX= intval($picinfo[0]/2-$info[0]/2);
				break;
			case 'right':
				$sX = $picinfo[0]-$info[0];
				break;
			default:
				$sX=$picinfo[0]-$info[0]-intval($offx);
				break;
		}
		// 计算水印y坐标
		switch ($offy) {
			case 'top':
				$sY=0;
				break;
			case 'center':
				$sY=$picinfo[1]/2-$info[1]/2;
				break;
			case 'bottom':
				$sY = $picinfo[1]-$info[1];
				break;
			default:
				$sY=$picinfo[1]-$info[1]-intval($offy);
				break;
		}
		$im->compositeImage($mark,Imagick::COMPOSITE_OVER,$sX,$sY);
		$im->stripImage();
		$im->setimagecompressionquality(60);
		$im->writeimage($pic);

		$mark->clear();
		$mark->destroy();
		$im->clear();
		$im->destroy();
	}
	/**
	 * 获取图片信息
	 * @param  string $pic 图片地址
	 * @return array  图片信息或者false
	 */
	static function getImgInfo($pic){
		$picInfo=false;
		if(is_file($pic)){
			if ($picInfo = @getimagesize($pic)) {
				$ptype = self::$imgTypes[$picInfo[2]];
				$picInfo['imgType'] = $ptype;
			}
		}
		return $picInfo;
	}

	/**
	 * 不改动原图，在同目录下生成缩放裁切后生成指定尺寸的缩略图
	 * @param int $type 缩放方式 type=1  按原图像尺寸等比例缩放 ；其他 剪裁成正方形,然后缩放
	 * @param int $width 目标宽度
	 * @param string $prefix 文件名前缀
	 * @return string 生成的文件路径
	 */
	static function resize($src,$type , $width , $prefix='')	{
		if (!is_file($src)) {
			return false;
		}
		$im = new Imagick($src);
		$pic_info = self::getImgInfo($src);
		$dst_path = dirname($src).'/'.$prefix.basename($src);

		try {
			$im->enhanceimage();
		} catch (Exception $e) {}

		$src_w = $pic_info[0];
		$src_h = $pic_info[1];
		$src_x = 0;
		$src_y = 0;
		$dst_w = $width;
		$dst_h = $width;

		if($type==1){					// 原图像尺寸等比例缩放
			$scale = $src_h / $src_w ;
			$dst_h = intval( $dst_w * $scale );
			$im->resizeimage($dst_w, $dst_h, Imagick::FILTER_CATROM, 1,false);
		}else{								// 剪裁成正方形,然后缩放
			if($src_w > $src_h){
				$indent = $src_w - $src_h;
				$src_x = intval($indent*3/5);
				$src_w = $src_h ;
				$im->cropimage($src_w, $src_h, $src_x, 0);
			}else{
				$indent = $src_h - $src_w;
				$src_y = intval($indent*3/5);
				$src_h = $src_w;
				$im->cropimage($src_w, $src_h, 0, $src_y);
			}
			$im->resizeimage($dst_w, $dst_h, Imagick::FILTER_CATROM, 1,true);
		}
		$im->stripImage();
		//$im->despeckleimage();
		$im->setimagecompressionquality(60);
		$im->writeimage($dst_path);

		$im->clear();
		$im->destroy();

		return	$dst_path;
	}
	/**
	 * 不改变原图，在同目录下生成根据宽度等比缩放的缩略图
	 * @param  int $width 目标宽度
	 * @return string 生成的文件路径
	 */
	static function psize($src,$width){
		return self::resize($src,1, $width, 'p'.$width.'_');
	}
	/**
	 * 不改变原图，在同目录下生成根据宽度剪裁成正方形,然后缩放的缩略图
	 * @param  int $width 目标宽度
	 * @return string 生成的文件路径
	 */
	static function csize($src,$width){
		return self::resize($src,2, $width, 'c'.$width.'_');
	}
}