<?php
/*
 *文件上传类 by awen   如果发生读写错误，请记得更新文件操作目录的【权限】
 */
class SUpload{
	private $FormName; //文件域名称
	private $Directroy; //上传至目录
	private $MaxSize; //最大上传大小
	private $CanUpload; //是否可以上传
	private $doUpFile; //上传的文件名
	private $Error;  //错误参数

 	//(1024*2)*1024=2097152 就是 8M
	function __construct($formName='', $dirPath='', $maxSize=8388608){
		//初始化各种参数
		$this->FormName = $formName;
		$this->MaxSize = $maxSize;
		$this->CanUpload = true;
		$this->doUpFile = '';
		$this->Error = 0;
		if ($formName == ''){
			$this->CanUpload = false;
			$this->Error = 1;
			break;
		}
		// 初始化文件存放目录
		$dirPath = trim($dirPath);
		$this->Directroy = $dirPath.'/'.date("Ym");
		$array_dir=explode("/",$this->Directroy);//把多级目录分别放到数组中

		$path = '';
		for($i=0;$i<count($array_dir);$i++){
			$path .= $array_dir[$i]."/";
			if(!file_exists($path)){
				mkdir($path);
			}
		}
 	}

	//获取文件大小
	function getSize($format = 'B')	{
		if ($this->CanUpload){
			if ($_FILES[$this->FormName]['size'] == 0){
				$this->Error = 3;
				$this->CanUpload = false;
			}
			switch ($format){
				case 'B':
					return $_FILES[$this->FormName]['size'];
					break;
				case 'M':
					return ($_FILES[$this->FormName]['size'])/(1024*1024);
			}
		}
	}

	//获取文件类型
	function getExt()	{
		if ($this->CanUpload){
			$ext=$_FILES[$this->FormName]['name'];
			$ext = strtolower($ext);
			$extStr=explode('.',$ext);
			$count=count($extStr)-1;
			//if(($extStr[$count] != "gif") and ($extStr[$count] != "jpeg" ) and ($extStr[$count] != "jpg" ) and ($extStr[$count] != "png" )) {
			//	echo "上传文件类型不是gif,jpeg,jpg,png格式！";
			//	break;
			//}
			return $extStr[$count];
		}else{
			return false;
		}
	}

	//获取原文件名称
	function getName()	{
		if ($this->CanUpload){
			return $_FILES[$this->FormName]['name'];
		}
	}
	//根据时间戳新建文件名
	function newName()	{
		if ($this->CanUpload){
			$ext = $this->getExt();

			list($msec, $sec) = explode(" ", microtime());
			$fix = intval($msec*1000000000)+rand(0,1000);
			return (date('YmdHis').$fix.'.'.$ext);
		}
	}

	//上传文件,$filename 自定义的保存文件名，如果为空则自动创建新文件名
	function upload($fileName = '')	{
		if ($this->CanUpload){
			if ($_FILES[$this->FormName]['size'] == 0){
				$this->Error = 3;
				$this->CanUpload = false;
				return $this->Error;
				break;
			}
		}
		if($this->CanUpload){
			if (empty($fileName)){
				// $fileName = $_FILES[$this->FormName]['name'];
				if ($this->CanUpload){
					$fileName = $this->newName();
				}
			}
			$doUpload=@copy($_FILES[$this->FormName]['tmp_name'], $this->Directroy.'/'.$fileName);
			if($doUpload)	{
				$this->doUpFile = $fileName;
				chmod($this->Directroy.'/'.$fileName, 0777);
				return true;
			}else{
				$this->Error = 4;
				return $this->Error;
			}
		}
	}

	//显示错误参数
	function Err(){
		return $this->Error;
	}

	//上传后的文件名
	function UpFile(){
		if ($this->doUpFile != ''){
			return $this->doUpFile;
		}else{
			$this->Error = 6;
		}
	}
	//上传后文件的目录
	function UpFilePath(){
		return $this->Directroy;
	}
	//上传后文件的路径
	function filePath(){
		if ($this->doUpFile != ''){
			return $this->Directroy.'/'.$this->doUpFile;
		}else{
			$this->Error = 6;
		}
	}
	//显示版本信息
	function version(){
		return 'UPLOAD CLASS Ver 1.1 [mdf awen:2013-02-08]';
	}
}