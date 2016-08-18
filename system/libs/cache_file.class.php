<?php
//----------------------------------------------
/*
 *file 缓存类，依赖于file.class.php配置参数如下：  by awen
 *目前时间信息写在文件里，后期改成 读文件时间信息
 *	array(
 *		'path' =>'./cache',
 *		'ext'  =>'.cache',
 *		'ttl'=>60*24, //秒，0代表永不过期
 *	)
 */
class Cache_file implements Cache{
	private $path='';
	private $def_path='';
	private $ttl=0;
	private $ext='.cache';

	function __construct($config=array()){
		if (isset($config['ttl'])) {
			$this->ttl=$config['ttl'];
		}
		if(isset($config['path'])) {
			$path=rtrim($config['path'],DIRECTORY_SEPARATOR);
			$this->def_path=$this->path=$path;
		}else{
			$this->path=$this->def_path;
		}
		if(!file_exists($this->path)){
			SFile::mkdirs($this->path);
		}
	}
	
	public function get($group,$key){
		$this->setGroup($group);

		$fname=$this->_get_fname($key);
		$data=@SFile::read($fname);
		if ($data) {
			$data=unserialize($data);
 			//判断过期
			if (is_array($data)) {
				if ($data['ttl']===0 || time()-$data['stime']<$data['ttl']) {
					return $data['data'];
				}
			}
 			SFile::remove($fname);
		}
		return FALSE;
	}
	public function set($group,$key,$val,$ttl=null){
		$this->setGroup($group);

		$ttl=is_null($ttl)?$this->ttl:$ttl;
		// 如果系统默认设置的也是-1，不缓存
    if ($this->ttl==-1) {
        return FALSE;
    }
		$path=$this->_get_fname($key);
		//组合数据
		$data=array(
			'ttl'=>$ttl,
			'stime'=>time(),
			'data'=>$val,
		);
		$data=serialize($data);
		return SFile::write($path,$data,false);
	}
	public function delete($group,$key){
		$this->setGroup($group);
		return SFile::remove($this->_get_fname($key));
	}
	public function clear($group=false){
		$this->setGroup($group);
		return SFile::clearDir($this->path);
	}
	private function _get_fname($key){
		return $this->path.DIRECTORY_SEPARATOR.$key.$this->ext;
	}
	//在主缓存路径下设置缓存文件夹,借此实现命名空间
	// "./",".",""都表示初始缓存目录
	private function setGroup($dirname){
		$dirname = empty($dirname)?'./':$dirname;
		$dirname=trim($dirname,DIRECTORY_SEPARATOR);
		$path=$this->def_path.DIRECTORY_SEPARATOR.$dirname;
		try {
			if (!file_exists($path)) {
				if (!SFile::mkdirs($path)) {
					return false;
				}
			}
			$this->path=$path;
			return TRUE;
		} catch (Exception $e) {
			return FALSE;
		}
	}
}