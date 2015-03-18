<?php
// App入口类，aop实现，用户自己的controller不用继承该类
Class Core_App{
	//当前的controller实例
	static $instance=null;

	private function __construct(){
		// 解析配置文件成成全局,并生成全局的应用相关的变量，包括，app路径，数据类和缓存类句柄
		$applibs = self::resolveConfig($GLOBALS['config']);
		Uri::init();
		$params = Uri::getParams();
		// dump($params);die();
		$GLOBALS['cur_cpath'] = $subpath = $params[Uri::key_p];
		$GLOBALS['cur_controller'] = $c = $params[Uri::key_c];
		$GLOBALS['cur_method'] = $m = $params[Uri::key_m];
		
		// 生成实际的controller实例
		$refc = $this->_refcontroller($subpath,$c);
		// $$uri,$applibs;
		if ($refc->hasMethod($m)) {
			$instance = $refc->newInstanceWithoutConstructor();
			// $instance = $refc->newInstance();
			// 注入model对象
			$instance->model=new Core_Model();
			// 注入view对象
			$instance->view=new Core_View();
			// 注入之后再执行构造函数,这样model和view就可以再构造函数中用了
			if ($construct = $refc->getConstructor()) {
				$construct->invoke($instance);
			}
			/////////////////// 执行相应的方法
			$method = $refc->getMethod($m);
			return $method->invoke($instance);
		}else{
			throw new Exception('controller文件【'.$c.'】中不包含【'.$m.'】方法。请检查！');
		}
	}
	/**
	 * 反射生成对应的controller类
	 */
	private function _refcontroller($subpath,$name){
		$path = $GLOBALS['path_app'].DIRECTORY_SEPARATOR.$GLOBALS['config']['folder_c'].DIRECTORY_SEPARATOR.$subpath.DIRECTORY_SEPARATOR.$name.'.php';
		if (file_exists($path)) {
			include_once($path);
			$c = ucfirst($name);//首字母大写
			if(class_exists($c)){
				return new ReflectionClass($c);
			}
			throw new Exception('文件中的类不符合规则，请检查：'.$path);
		}
		throw new Exception('文件不存在，请检查：'.$path);
	}
	/**
		*将某方法集成到当前实例的方法
		*func 	函数,字符串或者函数名
		*method 集成到对象后的方法名，默认与func相同
		*/
	private function _extend($func,$method=null){
		$reffunc=new ReflectionFunction($func);
		$method=is_string($method)?$method:$func;
		$this->methods[$method]=array('func'=>$reffunc);
	}

	// 单次执行入口方法
	static function load(){
		if (is_null(self::$instance)) {
			new self();
		}
	}
	// 解析配置文件，生成所有的应用级实例数组（db,cache。。。）
	static function resolveConfig($config){
		// 应用目录
		$apppath = BASEPATH.DIRECTORY_SEPARATOR.'apps';
		if (!is_dir($apppath)) {
			throw new Exception('当前版本的应用不存在，请检查配置文件!'.$apppath);
		}
		// 将解析过的app路径加入全局配置文件中
		$GLOBALS['path_app'] = $apppath;

		// 生成缓存类实例,其中默认的缓存实例是 cache
		if (!isset($config['cache'])) {
			throw new Exception('无法获取缓存配置信息，请检查配置文件');
		}

		// 系统libs目录
		$syslibs = SYSDIR.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR;
		// 缓存
		include_once $syslibs.'cache.class.php';
		$configCache = $config['cache'];
		// 文件缓存的目录需要整理一下 
		$configCache['file']['path'] = BASEPATH.$configCache['file']['path'];
		if (isset($configCache) && count($configCache)>0) {
			foreach ($configCache as $k => $v) {
				$classname = 'cache_'. $k;
				include_once $syslibs.$classname.'.class.php';
				$GLOBALS[$classname] = Helper::refClass($classname,$v);
				if (isset($v['default']) && $v['default'] == true) {
					$GLOBALS['cache'] = $GLOBALS[$classname];
				}
			}
			if (!isset($GLOBALS['cache'])) {
				$default = key($configCache);
				$GLOBALS['cache'] = $GLOBALS['cache_'.$default];
			}
		}
		// 生成数据库类的默认实例
		include_once $syslibs.'db.class.php';
		if (!isset($config['db'])) {
			throw new Exception('无法获取数据库配置信息，请检查配置文件！');
		}
		$configDB = $config['db'];
		$classname = 'db_'.$configDB['dbtype'];
		include_once $syslibs.$classname.'.class.php';
		$GLOBALS['db'] = Helper::refClass($classname,$configDB);
	}
}
