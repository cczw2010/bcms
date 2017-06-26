<?php
// 用户配置文件
// 别忘了再nginx或者apache中设置index.php为唯一入口
// 后台地址：http://xxx/manage    用户名:admin  密码：123456

return array(
	"sitename"=> 'BCMS',	//网站名称
	"debug" =>true,				//是否显示应用级报错（调试）和常规错误警告
	"sessionttl"=>3600,		//session过期时间

	// controller和view和model文件夹名称
	"folder_c"=>'controller',
	"folder_v"=>'view',
	"folder_m"=>'model',

	// 默认模块和入口
	"def_c"=>'home',
	"def_m"=>'index',
	// 404, 请使用相对路径
	'page404'=>'/404.html',

	// view模板设置，如果设置的缓存（伪静态），请注意这里使用的文件缓存
	// 模板引擎，可以使用任何以实现的引擎,false混编模式（推荐）
	"view"=>array(
		'version'=>'ace',	//引入模板的版本目录（方便多套模板之间切换例如：v1|v2|v3）
		'engine'=>false,			//模板引擎，其他第三方模板引擎，请在view中增加实现即可
		'ext'		=>'.php',			//模板后缀
		'group' =>'view',		//缓存分组
	),
	// 数据库配置
	'db' => array(
		'dbtype'	=> 'mysql',
		'host'		=> '127.0.0.1',
		'port'		=> 3306,
		'user'		=> 'root',
		'pass'		=> '12345678',
		'dbname'	=> 'bcms',
		'group'		=>'db',		//缓存分组
		'charset'	=>'utf8',
	),

	// 缓存配置,default代表默认的缓存,如果为true,则可以通过 $GLOBALS['cache']来访问。都不设置default默认第一个
	// 文件缓存可以通过  $GLOBALS['cache_file']来访问，memcache可以通过$GLOBALS['cache_memcache']来访问
	// 文件缓存是必须的(如果设置了模板引擎，模板缓存会用到，数据缓存用啥请自便)
	'cache'=>array(
		// 'memcache' =>array(
		// 	'servers'	=>array(
		// 				'memcache1'=>array('host'=>'112.126.70.35','port'=>11211,'weight'=>1),
		// 				'memcache2'=>array('host'=>'112.126.70.35','port'=>11212,'weight'=>2),
		// 				// 'memcache3'=>array('host'=>'112.126.70.35','port'=>11213,'weight'=>3),
		// 			),
		// 	//当设置缓存的时候如果不指定缓存时间（秒），将使用该默认缓存时间，0表示永不过期
	 	// 	'ttl'		=>3600,
		//  'default'	=>true,
		// ),
		// 'redis' =>array(
		//        'ttl'=>1,
		//        'servers'=>array (
		//                         array (
		//                             'host'      =>  '127.0.0.1' ,   //默认 127.0.0.1
		//                             'port'      =>  6379 ,          //默认 6379
		//                             'database'  =>  15 ,            //默认使用redis自己默认的数据库
		//                             'password'  =>  '',             //链接密码,如果没有密码移除此项,默认无密码
		//                             'timeout'   =>  5.0             //链接服务器超时时间, 默认5s
		//                             'read_write_timeout' =>  5.0    //读写超时时间,默认为系统设置,-1为不读写永不超时
	 	//                             'alias'	   => 'first'
	 	//                         ),
	 	//                         array (
		//                             'host'      =>  '127.0.0.1' ,   //默认 127.0.0.1
		//                             'port'      =>  6380 ,          //默认 6379
		//                             'database'  =>  15 ,            //默认使用redis自己默认的数据库
		//                             'alias'	   => 'second'
		//                         )
		//              		),
		// 	),
		'file' =>array(
			'path' 		=>'/cache',
			'ext'  		=>'.cache',
			//当设置缓存的时候如果不指定缓存时间（秒），将使用该默认缓存时间，0表示永不过期
			'ttl'		=>3600,
	 		'default'	=>true,
		),
	),
	// 文件上传路径
	'uploadpath' => '/upfiles',
	// 编辑器文件上传（elfinder）
	'elfinder' => array(
		'debug' => false,
		// 文件浏览器绝对路径path，因为是插件调用的所以地址要绝对路径，注意可读写权限 (REQUIRED)
		'path' => dirname(__FILE__).'/upfiles', 
		// 文件访问url (REQUIRED)
		'url' => '/upfiles',
		// 上传的文件是否重命名
		'uploadRename' => true,
	),
	// 管理后台超级账号
	'supermanager'=>array(
			'username'=>'admin',
			'password'=>'123456'
		),
	// 微信服务号配置,如果需要的话
	// 
	'weixin'=>array(
			'token'=>'',
			'encodingaeskey'=>'',
			'appid'=>'',
			'appsecret'=>'9P_PhaLApEbpxVE47t3OXa5F_bNIjKV8TmgLqrgKCUE',
			),
);

/**！！！其他需要配置的地方！！！
 * 1 目录权限， upfiles,cache,backup目录必须拥有可读写权限
 * 2 php扩展，用户头像和图片处理库SImage依托于imagick,请注意安装
 * 3 本程序用的是utf-8字符，所以数据库的默认配置中的字符请设置成utf-8
 * 4 本程序和一些第三方控件要求服务器short_open_tag=On,还好一般都开启的，另外需要openssl支持
 * 5 initTinymce js方法需要根据页面显示模板不同注入不同的css样式到编辑器中，
 * 		目前后台文章编辑和商品编辑中用到了编辑器，如果网站的展示页的css文件有改变，
 * 		请修改相应页面的css文件设置，以确保编辑器中与最终显示的时候效果一致（很多网站系统都忽略了该问题）
 * 6 mysql
 * 	my.cnf 里面的sql_mode去掉only_full_group_by
 */
