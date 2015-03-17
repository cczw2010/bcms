-- MySQL dump 10.13  Distrib 5.6.16, for osx10.9 (x86_64)
--
-- Host: 127.0.0.1    Database: ssms
-- ------------------------------------------------------
-- Server version	5.6.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `t_address`
--

DROP TABLE IF EXISTS `t_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(50) NOT NULL COMMENT '收货人',
  `province` varchar(50) NOT NULL COMMENT '省份（1级）',
  `city` varchar(50) NOT NULL COMMENT '城市（2级）',
  `area` varchar(50) NOT NULL COMMENT '地区（3级）',
  `detail` varchar(120) NOT NULL COMMENT '详细地址',
  `mobile` int(11) unsigned NOT NULL COMMENT '手机',
  `phone` varchar(50) DEFAULT '' COMMENT '固话',
  `nickname` varchar(100) DEFAULT '' COMMENT '收货地址别名',
  `default` int(11) DEFAULT '0' COMMENT '是否默认地址',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='地址信息列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_app_config`
--

DROP TABLE IF EXISTS `t_app_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_app_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '登陆方式名称',
  `key` varchar(20) DEFAULT '' COMMENT '登陆方式关键字，初始固定',
  `appid` varchar(50) NOT NULL DEFAULT '',
  `appkey` varchar(100) NOT NULL DEFAULT '',
  `secret` varchar(100) DEFAULT '',
  `callback` varchar(150) DEFAULT '' COMMENT '回调地址',
  `scope` varchar(350) DEFAULT '' COMMENT '授权',
  `idx` int(11) DEFAULT '0' COMMENT '排序',
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='第三方登陆配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_app_token`
--

DROP TABLE IF EXISTS `t_app_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_app_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appname` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方类型',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方openid或者uid',
  `token` varchar(50) DEFAULT '' COMMENT '第三方token',
  `expires_in` int(11) DEFAULT '0' COMMENT '有效期',
  `refresh_token` varchar(50) DEFAULT '' COMMENT '刷新令牌',
  `gender` varchar(10) DEFAULT '' COMMENT '性别',
  `nickname` varchar(60) NOT NULL DEFAULT '' COMMENT '第三方昵称',
  `cover` varchar(150) DEFAULT '' COMMENT '头像地址',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `addip` varchar(20) NOT NULL DEFAULT '',
  `lasttime` int(11) DEFAULT '0',
  `lastip` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_openid` (`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='第三方登陆应用信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_app_user`
--

DROP TABLE IF EXISTS `t_app_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_app_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT '0' COMMENT '用户id',
  `username` varchar(64) DEFAULT NULL COMMENT '用户昵称|用户名',
  `appid` int(11) DEFAULT '0' COMMENT '第三方登陆信息表id',
  `appname` varchar(64) DEFAULT '' COMMENT '第三方登陆应用名称; qq / sina /baidu',
  `status` int(11) DEFAULT '1',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `addip` varchar(20) NOT NULL DEFAULT '',
  `lasttime` int(11) DEFAULT '0',
  `lastip` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_userid` (`userid`),
  KEY `idx_objtype` (`appname`),
  KEY `idx_userid_objtypee` (`userid`,`appname`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='第三方登陆 应用信息和用户信息关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_article`
--

DROP TABLE IF EXISTS `t_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cateid` int(11) DEFAULT '0' COMMENT '分类id',
  `userid` int(11) DEFAULT '0' COMMENT '作者id',
  `username` varchar(50) DEFAULT '' COMMENT '作者',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `subtitle` varchar(50) DEFAULT '' COMMENT '副标题',
  `summary` varchar(300) DEFAULT '' COMMENT '简介',
  `content` text NOT NULL COMMENT '详情',
  `tags` varchar(50) DEFAULT '' COMMENT '标签，可用于搜索，seo',
  `istop` int(1) DEFAULT '0' COMMENT '置顶',
  `ishot` int(1) DEFAULT '0' COMMENT '热推',
  `order` int(11) DEFAULT '0' COMMENT '排序 - 预留',
  `viewnum` int(11) DEFAULT '0' COMMENT '浏览数',
  `comnum` int(11) DEFAULT '0' COMMENT '评论数',
  `favnum` int(11) DEFAULT '0' COMMENT '收藏数',
  `likenum` int(11) DEFAULT '0' COMMENT '喜欢数',
  `createdate` int(11) DEFAULT '0' COMMENT '创建时间',
  `updatedate` int(11) DEFAULT '0' COMMENT '最后更新',
  `lastdate` int(11) DEFAULT '0' COMMENT '最后访问时间',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='文章表，文章封面图是以附件方式存在的';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_attach`
--

DROP TABLE IF EXISTS `t_attach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objid` int(11) NOT NULL DEFAULT '0' COMMENT '目标对象的id',
  `objtype` varchar(50) NOT NULL DEFAULT '' COMMENT '目标对象的类型（product|article...）',
  `otype` varchar(50) DEFAULT '' COMMENT '附件类型(image|zip|doc)',
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '附件地址',
  `picpath` varchar(150) DEFAULT '' COMMENT '附件地址的路径部分',
  `picname` varchar(50) DEFAULT NULL COMMENT '附件地址的名称部分',
  `desc` varchar(200) DEFAULT '' COMMENT '描述',
  `createdate` int(11) DEFAULT '0' COMMENT '创建时间',
  `lastdate` int(11) DEFAULT '0' COMMENT '最后访问时间',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='通用附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_brand`
--

DROP TABLE IF EXISTS `t_brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `ename` varchar(50) DEFAULT '' COMMENT '品牌英文名称',
  `logo` varchar(150) DEFAULT '' COMMENT '品牌logo地址',
  `site` varchar(100) DEFAULT '' COMMENT '官网网站地址',
  `desc` varchar(200) NOT NULL DEFAULT '' COMMENT '品牌描述',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='品牌表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_category`
--

DROP TABLE IF EXISTS `t_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` int(11) DEFAULT '0' COMMENT '该分类的类型标识，可区分多个模块分类系统，',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '类名',
  `desc` varchar(100) DEFAULT NULL COMMENT '描述',
  `parentId` int(11) DEFAULT '0' COMMENT '父id',
  `depth` int(11) NOT NULL COMMENT '深度',
  `path` varchar(1000) NOT NULL COMMENT '类别分级的标志路径，由一级一级的父级id组成。',
  `status` int(11) DEFAULT '1' COMMENT '状态:0不启用，1启用',
  `weight` int(11) DEFAULT '0' COMMENT ' 同级优先级',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='分类模块管理，方便管理各个模块，用appid区分各个模块';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_comment`
--

DROP TABLE IF EXISTS `t_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appid` int(11) NOT NULL COMMENT '评论对象对应的模块id',
  `pid` int(11) DEFAULT '0' COMMENT '回复树的上级id',
  `userid` int(11) NOT NULL COMMENT '用户id',
  `username` varchar(64) DEFAULT '' COMMENT '用户名',
  `objid` int(11) NOT NULL COMMENT '评论对象的id',
  `scroe` int(11) DEFAULT '0' COMMENT '评分',
  `pics` varchar(400) DEFAULT '' COMMENT '回复中的图片，预留，可以放到t_attach表中去',
  `message` varchar(2000) NOT NULL COMMENT '评论内容',
  `ip` varchar(32) DEFAULT '' COMMENT '评论者ip',
  `createdate` int(11) DEFAULT '0' COMMENT '评论时间',
  `befrom` varchar(64) DEFAULT '' COMMENT '评论来源（www|ios|android|h5...）',
  `status` int(11) DEFAULT '0' COMMENT '状态 0 屏蔽，1正常',
  PRIMARY KEY (`id`),
  KEY `idx_objtype_objid` (`appid`,`objid`),
  KEY `idx_uid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='通用评论模块，方便管理各个模块的评论，用appid区分各个模块';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_config`
--

DROP TABLE IF EXISTS `t_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '' COMMENT '站点名称',
  `desc` varchar(200) DEFAULT '' COMMENT '站点简单描述meta',
  `keywords` varchar(200) DEFAULT '' COMMENT 'seo',
  `mail_smtpservice` varchar(100) DEFAULT '' COMMENT '邮件配置-smtp服务器',
  `mail_port` varchar(50) DEFAULT '' COMMENT '邮件配置-smtp服务器端口',
  `mail_username` varchar(50) DEFAULT '' COMMENT '邮件配置-用户名',
  `mail_nickname` varchar(100) DEFAULT '' COMMENT '邮件配置-发件人昵称',
  `mail_password` varchar(50) DEFAULT '' COMMENT '邮件配置-密码',
  `mail_mark` varchar(500) DEFAULT '' COMMENT '邮件配置-签名',
  `mail_language` varchar(50) DEFAULT '' COMMENT '邮件配置-语言',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='网站配置，目前没有启用';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_gift`
--

DROP TABLE IF EXISTS `t_gift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_gift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cateid` int(11) DEFAULT '0' COMMENT '分类id',
  `userid` int(11) DEFAULT '0' COMMENT '作者id',
  `username` varchar(50) DEFAULT '' COMMENT '作者',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '礼包名称',
  `subtitle` varchar(50) DEFAULT '' COMMENT '副标题',
  `cover` varchar(100) DEFAULT '' COMMENT '封面图',
  `summary` varchar(300) DEFAULT '' COMMENT '简介',
  `content` text NOT NULL COMMENT '详情',
  `tags` varchar(50) DEFAULT NULL COMMENT '标签，可用于搜索，seo',
  `oprice` int(11) NOT NULL COMMENT '原价',
  `price` int(11) NOT NULL COMMENT '售价',
  `oquantity` int(11) NOT NULL COMMENT '初始库存量，便于统计，不参与计算',
  `quantity` int(11) NOT NULL COMMENT '当前库存量',
  `sales` int(11) NOT NULL COMMENT '已售数量',
  `ishot` int(1) DEFAULT '0' COMMENT '热推',
  `order` int(11) DEFAULT '0' COMMENT '排序 - 预留',
  `viewnum` int(11) DEFAULT '0' COMMENT '浏览数',
  `comnum` int(11) DEFAULT '0' COMMENT '评论数',
  `favnum` int(11) DEFAULT '0' COMMENT '收藏数',
  `likenum` int(11) DEFAULT '0' COMMENT '喜欢数',
  `createdate` int(11) DEFAULT '0' COMMENT '创建时间',
  `updatedate` int(11) DEFAULT '0' COMMENT '最后更新',
  `lastdate` int(11) DEFAULT '0' COMMENT '最后访问时间',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='礼包表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_group`
--

DROP TABLE IF EXISTS `t_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `types` int(11) DEFAULT '0' COMMENT '0 管理员分组，1 用户分组',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '权限组名称',
  `rights` text COMMENT '权限字符串组，格式 controllers-method|controller-method|...',
  `desc` varchar(200) DEFAULT '' COMMENT '模块说明',
  `status` int(11) DEFAULT '0' COMMENT '状态，预留',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='用户权限组';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_log`
--

DROP TABLE IF EXISTS `t_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '操作用户id',
  `username` varchar(50) NOT NULL COMMENT '操作用户名',
  `ip` varchar(50) NOT NULL COMMENT '操作ip',
  `moduleid` varchar(200) DEFAULT NULL COMMENT 'log对象id',
  `modulename` varchar(200) NOT NULL DEFAULT '' COMMENT 'log对象类型',
  `key` varchar(50) NOT NULL COMMENT 'log的key',
  `message` varchar(500) NOT NULL DEFAULT '' COMMENT 'log消息',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='一些重要的操作,例如操作商品数量，订单状态等是需要记录日志的';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_module`
--

DROP TABLE IF EXISTS `t_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '模块名称',
  `desc` varchar(200) DEFAULT NULL COMMENT '模块说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分类模块管理，方便管理各个模块，用于分类管理中的appid';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_order`
--

DROP TABLE IF EXISTS `t_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `orderno` varchar(50) NOT NULL COMMENT '自动生成的订单唯一no',
  `name` varchar(200) DEFAULT '' COMMENT '订单名称，可为空',
  `totalfee` float(9,2) NOT NULL COMMENT '订单总价（上限制999万）',
  `paymentid` int(11) NOT NULL COMMENT '支付方式id',
  `payment` varchar(50) DEFAULT '' COMMENT '支付方式',
  `paymentno` varchar(50) DEFAULT '' COMMENT '支付方式返回的订单号',
  `paymentstatus` varchar(50) DEFAULT '' COMMENT '支付方式回调状态',
  `shippingid` int(11) NOT NULL COMMENT '配送方式id',
  `shipping` varchar(50) DEFAULT '' COMMENT '配送方式',
  `shippingno` varchar(20) DEFAULT '' COMMENT '快递单号',
  `addressid` int(11) NOT NULL COMMENT '配送地址id',
  `address` varchar(200) DEFAULT NULL COMMENT '配送地址',
  `tips` varchar(200) DEFAULT NULL COMMENT '用户备注',
  `descr` varchar(300) DEFAULT '' COMMENT '管理员添加的订单说明',
  `status` int(11) DEFAULT '0' COMMENT '订单状态，0未支付（自动），1已支付（自动），2已完成（彻底完成，包括款项到账，物流配送）（自动），3已关闭（手动关闭，取消）',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  `updatedate` int(11) NOT NULL COMMENT '最后状态更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订单表，很多冗余字段，空间换时间，支持事务';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_order_item`
--

DROP TABLE IF EXISTS `t_order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL COMMENT '订单id',
  `objtype` varchar(50) NOT NULL DEFAULT '' COMMENT '订单项类型（商品product|礼包gift）',
  `objid` int(11) NOT NULL COMMENT '订单项id',
  `objname` varchar(100) DEFAULT NULL COMMENT '订单项名称（冗余，防止商品被删除）',
  `cover` varchar(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL COMMENT '订单项数量',
  `price` float(9,2) NOT NULL COMMENT '订单项单价（上限制999万）',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  `status` int(11) DEFAULT '1' COMMENT '商品项状态，不需处理，程序逻辑来判断（1正常，0不存在）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订单中的项的关联表,很多冗余字段，空间换时间，支持事务';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_payment`
--

DROP TABLE IF EXISTS `t_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '支付方式名称',
  `key` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式的唯一key,初始固定',
  `appid` varchar(50) DEFAULT '' COMMENT '商家的id',
  `appkey` varchar(50) DEFAULT '' COMMENT '商家的key，安全检验码',
  `appaccount` varchar(50) DEFAULT '' COMMENT '商家账号',
  `notifyurl` varchar(100) DEFAULT '' COMMENT '异步通知地址',
  `returnurl` varchar(100) DEFAULT '' COMMENT '同步通知地址',
  `desc` varchar(200) DEFAULT '' COMMENT '支付方式简介',
  `status` int(11) DEFAULT '0' COMMENT '状态，0未启用，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='支付表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_product`
--

DROP TABLE IF EXISTS `t_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cateid` int(11) DEFAULT '0' COMMENT '分类id',
  `brandid` int(11) DEFAULT '0' COMMENT '品牌id',
  `producttype` int(11) DEFAULT '0' COMMENT '商品类型，可选值:0(一口价),1(拍卖)。',
  `userid` int(11) DEFAULT '0' COMMENT '作者id',
  `username` varchar(50) DEFAULT '' COMMENT '作者',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  `subtitle` varchar(50) DEFAULT '' COMMENT '副标题',
  `cover` varchar(100) DEFAULT '' COMMENT '封面图',
  `summary` varchar(300) DEFAULT '' COMMENT '简介',
  `content` text NOT NULL COMMENT '详情',
  `tags` varchar(50) DEFAULT NULL COMMENT '标签，可用于搜索，seo',
  `oprice` int(11) NOT NULL DEFAULT '0' COMMENT '市场原价',
  `price` int(11) NOT NULL DEFAULT '0' COMMENT '售价',
  `oquantity` int(11) NOT NULL DEFAULT '0' COMMENT '初始库存量，便于统计，不参与计算',
  `quantity` int(11) NOT NULL DEFAULT '0' COMMENT '当前库存量',
  `maxbuy` int(11) NOT NULL DEFAULT '0' COMMENT '单笔购买上限设定，0代表不限',
  `sales` int(11) NOT NULL DEFAULT '0' COMMENT '已售数量',
  `ishot` int(1) DEFAULT '0' COMMENT '热推',
  `order` int(11) DEFAULT '0' COMMENT '排序 - 预留',
  `viewnum` int(11) DEFAULT '0' COMMENT '浏览数',
  `comnum` int(11) DEFAULT '0' COMMENT '评论数',
  `favnum` int(11) DEFAULT '0' COMMENT '收藏数',
  `likenum` int(11) DEFAULT '0' COMMENT '喜欢数',
  `createdate` int(11) DEFAULT '0' COMMENT '创建时间',
  `updatedate` int(11) DEFAULT '0' COMMENT '最后更新',
  `lastdate` int(11) DEFAULT '0' COMMENT '最后访问时间',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `cateid_id` (`cateid`,`id`),
  KEY `brandid_id` (`brandid`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_prop`
--

DROP TABLE IF EXISTS `t_prop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_prop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` int(11) DEFAULT '0' COMMENT '模块id,可以将属性表根据模块来分类',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '属性名称',
  `vals` varchar(500) DEFAULT '' COMMENT '属性的值键值对数组序列化后的字符串。没有留空',
  `desc` varchar(300) DEFAULT '' COMMENT '简介',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='数据表的基础属性表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_prop_item`
--

DROP TABLE IF EXISTS `t_prop_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_prop_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cateid` int(11) DEFAULT '0' COMMENT '分类id，根据分类来定义属性列表',
  `propid` varchar(50) NOT NULL DEFAULT '' COMMENT '属性id',
  `status` int(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='数据表的基础属性表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_sensitiveword`
--

DROP TABLE IF EXISTS `t_sensitiveword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_sensitiveword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banned` text COMMENT '禁止的敏感词',
  `filters` text COMMENT '要过滤的敏感词',
  `status` int(11) DEFAULT '0' COMMENT '0不生效，1生效',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='敏感词汇表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_shipping`
--

DROP TABLE IF EXISTS `t_shipping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_shipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '配送方式名称',
  `desc` varchar(500) NOT NULL DEFAULT '' COMMENT '配送方式介绍',
  `status` int(11) DEFAULT '0' COMMENT '0未启用，1启用',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='配送方式表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_shipping_log`
--

DROP TABLE IF EXISTS `t_shipping_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_shipping_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shippingid` int(11) NOT NULL COMMENT '配送方式id',
  `shipping` varchar(50) DEFAULT '' COMMENT '配送方式名称',
  `desc` varchar(500) DEFAULT '' COMMENT '当前配送记录说明',
  `createdate` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='配送记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_user`
--

DROP TABLE IF EXISTS `t_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT '' COMMENT '邮箱，建议登陆用邮箱',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) DEFAULT '' COMMENT '昵称',
  `password` varchar(64) DEFAULT '' COMMENT '可能为空，因为第三方登录时可能直接登录而不注册所以没有密码',
  `avatar` varchar(200) DEFAULT '' COMMENT '头像，建议在程序中设置规则直接访问，而不是存数据库里',
  `sign` varchar(2000) DEFAULT '' COMMENT '签名',
  `mobile` varchar(20) DEFAULT '' COMMENT '手机',
  `sex` int(11) DEFAULT NULL COMMENT '性别  1男 2女 3人妖',
  `uuid` varchar(20) DEFAULT '' COMMENT '可以给每个用户生成一个唯一的uuid,方便客户端等地方验证，预留',
  `status` int(11) DEFAULT '1' COMMENT '状态，-1锁定，1正常，2未完善信息',
  `logincount` int(11) DEFAULT '0' COMMENT '登录次数记录',
  `addtime` int(11) DEFAULT '0' COMMENT '注册时间',
  `lasttime` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `addip` varchar(20) DEFAULT '' COMMENT '注册ip',
  `lastip` varchar(20) DEFAULT '' COMMENT '最后登录ip',
  `befrom` varchar(64) DEFAULT '' COMMENT '访问来源',
  `group` int(11) DEFAULT '0' COMMENT '用户分组， -1 表示超级管理员，不在管理范围之内，默认0普通用户',
  `rank` int(11) DEFAULT '0' COMMENT '等级',
  `credit` int(11) DEFAULT '0' COMMENT '积分',
  `experience` int(11) DEFAULT '0' COMMENT '经验值',
  `viewnum` int(11) DEFAULT '0' COMMENT '浏览数',
  `followingnum` int(11) DEFAULT '0' COMMENT '关注数',
  `followednum` int(11) DEFAULT '0' COMMENT '粉丝数',
  `longitude` double(10,6) DEFAULT NULL COMMENT '最后经度',
  `latitude` double(10,6) DEFAULT NULL COMMENT '最后维度',
  `lastarea` varchar(64) DEFAULT '' COMMENT '最后地区',
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_user_log`
--

DROP TABLE IF EXISTS `t_user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `username` varchar(50) DEFAULT NULL COMMENT '用户名',
  `longitude` double(10,6) DEFAULT NULL COMMENT '经度',
  `latitude` double(10,6) DEFAULT NULL COMMENT '维度',
  `lastarea` varchar(64) DEFAULT NULL COMMENT '地区',
  `addtime` int(11) DEFAULT '0' COMMENT '登陆时间',
  `ip` varchar(20) DEFAULT NULL COMMENT '登陆ip',
  `befrom` varchar(64) DEFAULT NULL COMMENT '访问来源',
  `useragent` varchar(200) DEFAULT NULL COMMENT 'useragent',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户日志表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-21 13:58:02