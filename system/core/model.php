<?php
// 用户model层加载类，内核直接加载
// 缓存用到了文件缓存
class Core_Model{
	// model目录
	public $path = '';

	function __construct(){
		$this->path = $GLOBALS['path_app'].DIRECTORY_SEPARATOR.$GLOBALS['config']['folder_m'];
	}

	/**
	 * 加载用户model
	 * @param  string $modelname model的名称（类名为model文件名首字母大写）
	 * @param  boolean $sys 是否加载系统model(module文件夹下的model)
	 * @return class instance   model类的实例
	 */
	public function load($modelname,$sys=false){
		$path = $this->path.DIRECTORY_SEPARATOR.$modelname.'.php';
		if (file_exists($path)) {
			include_once($path);
			$cname = ucfirst(basename($modelname));
			if(class_exists($cname)){
				$cls = new ReflectionClass($cname);
				return $cls->newInstance();
			}
			throw new Exception('model类不存在，请检查>'.$cname);
		}
		throw new Exception('model文件不存在，请检查>'.$modelname);
	}
}