<?php
// 模板view类，内核直接加载，可以在配置中设定模板类型，并在此实现
// 缓存用到了文件缓存
Class Core_View{
	private $engine = false;
	private $ext = '.php';
	private $viewPath = '';
	private $datas = array();	//加入模板的变量数组
	// 构造函数
	function __construct(){
		$vconfig = $GLOBALS['config']['view'];
		$this->viewPath = $GLOBALS['path_app'].DIRECTORY_SEPARATOR.$GLOBALS['config']['folder_v'].DIRECTORY_SEPARATOR.$vconfig['version'].DIRECTORY_SEPARATOR;
		$this->ext = $vconfig['ext'];
		$this->engine = $vconfig['engine'];
	}
	// 手动的增加模板中的提供的数据数组，load中传入的数据也是通过这个方法来组合数据的
	// 重名变量将以最后一次为准
	function data($data){
		if (!empty($data)&&is_array($data)) {
			$this->datas = array_merge($this->datas,$data);
		}
	}
	// 根据配置$defaut的默认加载类
	function load($name,$datas=array()){
		$path = $this->viewPath.$name.$this->ext;
		// 合并变量数组
		$this->data($datas);
		$this->__load($path);
	}
	// 更换模板版本
	function setVersion($ver='default'){
		$this->viewPath = $GLOBALS['path_app'].DIRECTORY_SEPARATOR.$GLOBALS['config']['folder_v'].DIRECTORY_SEPARATOR.$ver.DIRECTORY_SEPARATOR;
	}
	// 更换模板引擎
	function setEngine($engine=false){
		$this->engine = $engine;
	}
	/**不对外开放方法************************************************/
	// 根据模板类型生成内容
	private function __load($path){
		if (!file_exists($path)) {
			throw new Exception('模板文件不存在:'.$path, 1);
		}
		switch ($this->engine) {
			case 'smarty':
				$this->load_smarty($path,$this->datas);
				break;
			case 'template':
				$this->load_template($path,$this->datas);
				break;
			default:
				$this->load_default($path,$this->datas);
				break;
		}
	}

	// 默认的无模板引擎实现
	private function load_default($file,$datas){
		if (!empty($datas)) {
			extract($datas);
		}
		include_once($file);
	}
	// smarty模板实现
	private function load_smarty($file,$datas){

	}
	// template模板实现
	private function load_template($file,$datas){
		
	}
}