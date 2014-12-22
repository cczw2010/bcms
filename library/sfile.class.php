<?php
/*
 *文件操作类 by awen   如果发生读写错误，请记得更新文件操作目录的【权限】
 */
class SFile{
	/**
	 * 创建多层目录
	 */
	static function mkdirs($path, $mode = 0777)
	{	
		if (is_dir($path) || @mkdir($path, $mode)) return true;
		if (!self::mkdirs(dirname($path), $mode)) return false;
		return @mkdir($path, $mode);
  }
  /**
   * 复制文件
   * @param  string $src 要复制的源文件
   * @param  string $dst 复制文件的目标地址
   * @return boolean   是否复制成功
   */
  static function copy($src , $dst){
    $ret = false;
    if (is_file($src)) {
      $dir = dirname($dst);
      $ret = self::mkdirs($dir);
    }
    if ($ret) {
      $ret =copy($src,$dst);
    }
    return $ret;
  }
  // 移动文件,不检查目录是否存在
  static function move($src,$dst){
    if (self::copy($src, $dst)) {
      @unlink($src);
      return true;
    }
    return false;
  }
  // 获取目录下的所以对象数组,带不带/结尾 无所谓
  //$desc 排序是否字母反序，默认false
  static function getPathList($path,$desc=false){
  	$lists = array();
    if ($local=realpath($path)) {
      $sort = $desc?1:0;
			// echo $local;
			if($sfs = scandir($path,$sort)){
        $lists = $sfs;
      }
		}
		return $lists;
  }
  // 获取目录下所有文件类型的对象
  // $ext 扩展名不区分大小写(如：jpg,或者多个扩展名：如 jpg|png|bmp)，默认所有
  // $desc排序是否字母反序，默认false
  static function getPathFiles($path,$ext='',$desc=false){
  	$fs = array();
  	$lis = self::getPathList($path,$desc);
  	$path = realpath($path);
    $ext = empty($ext)?'':strtolower($ext);
  	foreach ($lis as $item) {
      if (is_file($path.'/'.$item)) {
        if (!empty($ext) && !preg_match("/\.(".$ext.")$/i", strtolower($item))) {
          continue;
        }
  			$fs[] = $item;
  		}
  	}
  	return $fs;
  }
  // 获取目录下所有目录类型的对象
  // 排序是否字母反序，默认false
  static function getPathFolders($path,$desc=false){
  	$fs = array();
  	$lis = self::getPathList($path,$desc);
  	$path = realpath($path);
  	foreach ($lis as $item) {
  		if (is_dir($path.'/'.$item)) {
  			$fs[] = $item;
  		}
  	}
  	return $fs;
  }
  // 清空目录
  static function clearDir($path,$rmdir=false){
  	if (!$dir = @opendir($path)){
			return false;
		}
		while (false!==($item=readdir($dir))) {
			if (in_array($item, array('.','..'))) {
				continue;
			}
			$f=$path.DIRECTORY_SEPARATOR.$item;
 			if (is_dir($f)) {
				 self::clearDir($f,true);
			}else{
				unlink($f);
			}
		}
		closedir($dir);
		if ($rmdir) {
			return rmdir($path);
		}
  }
  // 返回文件信息
  static function getInfo($file){
    return stat($file);
  }
  // 返回文件后缀
  static function getExt($file){
    return pathinfo($file, PATHINFO_EXTENSION);//PATHINFO_FILENAME|PATHINFO_BASENAME|PATHINFO_DIRNAME
  }
  // 读取文件
	static function read($file,$offset=0,$len=null){
 		if (file_exists($file)) {
 			if(isset($len)){
 				return file_get_contents($file,null,null,$offset,$len);
 			}else{
 				return file_get_contents($file,null,null,$offset);
 			}
 		}
		return false;
	}
	// 写文件 成功的话返回写入的字节数否则false
  //$append 如果文件 filename 已经存在，追加数据而不是覆盖。
  static function write($file,$data,$append=true){
    $flag = LOCK_EX;
    if ($append==true) {
      $flag = $flag|FILE_APPEND;
    }
		$num = file_put_contents($file, $data,$flag);
    if ($num!==false) {
			@chmod($path, 0777);
    }
    return $num;
  }
  // 删除文件
  static function remove($file){
    return @unlink($file);
  }
  //	清除文件状态缓存。防止多次操作同一个文件时，缓存文件的状态信息，比如大小，位移等等
  static function clearState(){
    clearstatcache();
  }
}