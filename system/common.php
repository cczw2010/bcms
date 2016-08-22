<?php
// 加载配置文件
$GLOBALS['config'] = require(BASEPATH.DIRECTORY_SEPARATOR.'config.php');
// 异常处理
if ($GLOBALS['config']['debug']) {
	ini_set('display_errors','On');
	// 设置当前错误显示模式E_ALL，0
	error_reporting(E_ALL);
	// error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
	// 设置自己的异常处理函数
	function handleException($e){
			$html = '错误消息:'.$e->getMessage().
				'<br>文件:'.$e->getFile().
				'<br>行数:'.$e->getLine();
			echo $html;
			dump($e->getTraceAsString());
			exit();
			// showMessage($html);
	}
	set_exception_handler('handleException');
}else{
	error_reporting(0);
}

// 加载内核
require(SYSDIR.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'app.php');
require(SYSDIR.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'model.php');
require(SYSDIR.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'view.php');
require(SYSDIR.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'uri.php');
//=========自动加载类文件============================================
//会从system/libs和aspps/model两个目录加载类文件，文件名为类名的小写
function autoload($classname) {
	$_cname=strtolower($classname);
 	foreach (array(BASEPATH.DIRECTORY_SEPARATOR.'module',BASEPATH.DIRECTORY_SEPARATOR.'library') as $path)
 	// foreach (array(BASEPATH.DIRECTORY_SEPARATOR.'models',BASEPATH.DIRECTORY_SEPARATOR.'library') as $path)
	{
		$path=$path.DIRECTORY_SEPARATOR.$_cname.'.class.php';
 		if (file_exists($path))
		{
			require_once($path);
			break;
		}
	}
}
spl_autoload_register('autoload');
//====================这里存放全局的方法==============================
//调试输出函数，支持多参数同时打印
function dump(){
	echo '<div style="border:1px solid #990000;line=height:20px;font=size:16px;margin:10px;">';
	$regs=func_get_args();
 	foreach ($regs as $val) {
		echo '<div style="margin:2px;padding:2px;border:1px solid #990000;">';
			$output = print_r($val,true);
			// $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
		  $output = '<pre>'. htmlspecialchars($output, ENT_QUOTES). '</pre>';
			echo $output;
		echo '</div>';
	}
	echo '</div>';
}
/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $file 要写入的文件
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logs($file,$word='') {
	if (!empty($file) && file_exists($file)) {
		$fp = fopen($file,"a");
		flock($fp, LOCK_EX) ;
		fwrite($fp,"时间:".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}
/**
 * 显示消息，中断代码执行，支持跳转
 * @param  string $msg    要显示的消息
 * @param  string $url    要跳转的url
 * @param  int $timeout 是否定时跳转(秒),-1代表不自动跳转
 * @return
 */
function showMessage($msg,$url='',$timeout=-1){
	$view = new Core_View();
	$view->load('message',array('msg'=>$msg,'url'=>$url,'timeout'=>$timeout));
	exit();
}
/**
 * 分页 该程序会自动补全idx，size在末尾，样式用户可以再css文件中自己重写,依赖jquery
 * @param  number $curpage   当前页
 * @param  number $psize  每页大小，只参与计算不会再传给后端
 * @param  number $itemcount 总记录数
 * @param  array $params 参数数组（键值对以？形式拼接到末尾）
 * @param  boolean $isajax  是否ajax按钮，前端的ajax事件回调请自己实现
 * @return string
 */
function multiPages($curpage,$psize,$itemcount,$params=false,$isajax=false){
	if (empty($itemcount)) {
		return '';
	}
	$url = '/'.$GLOBALS['cur_cpath'].Uri::$uritype.$GLOBALS['cur_controller'].Uri::$uritype.$GLOBALS['cur_method'].Uri::$uritype;
	$query = http_build_query($params);
	$tag = $isajax?'span':'a';
	$urlattr = $isajax?'data-url':'href';

	$totalpage = ceil($itemcount/$psize);
	// 上一页
	$pre = '<'.$tag.' class="jpagebtn';
	if ($curpage>1) {
		$pre .= ' jpagecan" '.$urlattr.'="'.$url.($curpage-1).'?'.$query.'">';
	}else{
		$pre .= '">';
	}
	$pre .= '上一页</'.$tag.'>';
	// 下一页
	$next = '<'.$tag.' class="jpagebtn';
	if ($curpage<$totalpage) {
		$next .= ' jpagecan" '.$urlattr.'="'.$url.($curpage+1).'?'.$query.'">';
	}else{
		$next .= '">';
	}
	$next .= '下一页</'.$tag.'>';
	// 中间页签
	$pagemid = '<'.$tag.'>'.$curpage.'/'.$totalpage.'</'.$tag.'>';
	// 尾部跳页
	$jumppage = '<input style="width: 20px;margin-right: 0px;" data-maxpage="'.$totalpage.'" onkeyup="';
	if ($isajax) {
		$jumppage.= '$(this.nextSibling).data(\'url\',\''.$url.'\'+$(this).val()+\''.'?'.$query.'\')';
	}else{
		$jumppage.= '$(this.nextSibling).attr(\'href\',\''.$url.'\'+$(this).val()+\''.'?'.$query.'\')';
	}
	$jumppage.='" value="'.$curpage.'"  class="jpagebtn"><'.$tag.' class="jpagebtn jpagecan" '.$urlattr.'="'.$url.$curpage.'?'.$query.'">GO</'.$tag.'>';
	return '<div class="jpages">'.$pre.$pagemid.$next.$jumppage.'</div>';
}


/**
 * 同上 ace版本
 */
function multiPages4Ace($curpage,$psize,$itemcount,$params=false,$isajax=false){
	if (empty($itemcount)) {
		return '';
	}
	$url = '/'.$GLOBALS['cur_cpath'].Uri::$uritype.$GLOBALS['cur_controller'].Uri::$uritype.$GLOBALS['cur_method'].Uri::$uritype;
	$query = http_build_query($params);

	$totalpage = ceil($itemcount/$psize);
	// 上一页
	$pre = '<span class="badge badge-primary';
	if ($curpage<=1) {
		$pre .= ' disabled';
	}
	$pre .= '" data-page="'.($curpage-1).'" ><i class="icon-angle-left bigger-140"></i></span> ';
	// 下一页
	$next = '<span class="badge badge-primary';
	if ($curpage>=$totalpage) {
		$next .= ' disabled';
	}
	$next .= '" data-page="'.($curpage+1).'"><i class="icon-angle-right bigger-140"></i></span> ';
	
	$ret = '<p class="ace-page center" data-isajax="'.($isajax?1:0).'" data-url="'.$url.'" data-query="'.$query.'" data-maxpage="'.$totalpage.'">
						<span class="badge badge-primary" data-page="1">
							<i class="icon-double-angle-left bigger-140"></i>
						</span> '.
		$pre.' <span>Page <span data-page='.$curpage.'></span><input class="input-sm" onkeyup="this.previousSibling.dataset.page = this.value;console.log(this.value);if(event.keyCode==13){event.preventDefault();$(this.previousSibling).trigger(\'click\');return false;}" type="number" size="2" min="1" max="'.$totalpage.'" value="'.$curpage.'"> of <span>'.$totalpage.'</span></span> '.
		$next.'<span class="badge badge-primary" data-page="'.$totalpage.'">
							<i class="icon-double-angle-right bigger-140"></i>
						</span>
					</p>';
	return $ret;
}

/**
 * 分页处理
 * @param int $count 总数
 * @param int $num 每页数
 * @param int $thisNum 当前页
 */
function set_php_page_menu($count,$num,$page){
    //总页数
    $countPage = ceil($count/$num);
    if ($countPage <= 1 || !$countPage) {
        return "";
    }
    $content = '<li><a href="">首页</a></li>';
    if($page==1){$prePage = 1;}else{$prePage = $page-1;}//判断上一页的值
    $content .= '<li><a href="">&lt;</a></li>';
    if ($page == 1) {//固定显示第一页，并判断是否加样式
        $content .= '<li><a class="active">1</a></li>';
    } else {
        $content .= '<li><a>1</a></li>';
    }
    if ($page > 3) {//中间固定显示5页，判断5页的起始页
        $star = $page -2;
    } else {
        $star = 2;
    }
    if ($star >= 3) {//当起始页大于3的时候加...
        $content .= '<li><a>···</a></li>';
    }
    if ($countPage-$page>2 ) {//中间固定显示5页，判断5页的结束页
        $end = $page+2;
    } else {
        $end = $countPage-1;
    }
    for ($i=$star;$i<=$end;$i++) {//中间固定显示5页，循环中间5页
        if ($i == $page) {
            $content .= '<li><a class="active">'.$i.'</a></li>';
        } else {
            $content .= '<li><a>'.$i.'</a></li>';
        }
    }
    if ($countPage-$page> 3) {//当结束页和总页数差值大于3时加...
        $content .= '<li><a>···</a></li>';
    }
    if ($page == $countPage) {//固定显示最后一页，并判断是否加样式
        $content .= '<li><a class="active">'.$countPage.'</a></li>';
    } else {
        $content .= '<li><a>'.$countPage.'</a></li>';
    }
    if($page==$countPage){$nextPage = $countPage;}else{$nextPage = $page+1;}//判断下一页的值
    $content .= '<li><a>&gt;</a></li>';
    $content .= '<li><a>尾页</a></li>';
    // $content .= '<span class="txt">共'.$countPage.'页'.$count.'条数据</span> <span class="txt">到第</span>';
    // $content .= '<form method="get" style="display:inline-block" action="">';
    // $content .= '<input name=p type="text" value="" class="inputPage"><span class="txt">页</span>';
    // $content .= '<input type="submit" value="确定" class="sure">';
    // $content .= '</form>';
    return $content;
}