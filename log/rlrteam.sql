-- MySQL dump 10.13  Distrib 5.7.14, for Win64 (x86_64)
--
-- Host: localhost    Database: rlrteam
-- ------------------------------------------------------
-- Server version	5.7.14

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
-- Table structure for table `community`
--

DROP TABLE IF EXISTS `community`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community` (
  `community_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绀惧尯涓婚敭ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绀惧尯鍒涘缓鏃堕棿鎴?,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍏宠仈鐨勪富瑙佽瘉浜鸿处鍙稩D',
  `minpercent` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '璧勫姪鑰呰祫鍔╂椂瀵瑰簲蹇冩効鏈€灏忎綑棰濇瘮',
  `community_name` varchar(255) NOT NULL DEFAULT '' COMMENT '绀惧尯鍚嶇О锛氬闄?瀛︽牎',
  `remarks` varchar(255) NOT NULL DEFAULT '' COMMENT '绀惧尯澶囨敞/浠嬬粛/鎻忚堪',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '鐘舵€侊細1->姝ｅ父浣跨敤锛?->鍐荤粨鐘舵€?,
  PRIMARY KEY (`community_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community`
--

LOCK TABLES `community` WRITE;
/*!40000 ALTER TABLE `community` DISABLE KEYS */;
INSERT INTO `community` VALUES (51400007,1569932691,23,27,'姊呭窞钑夊箔涓','鍟у暓鍟?,1);
/*!40000 ALTER TABLE `community` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flows`
--

DROP TABLE IF EXISTS `flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flows` (
  `flows_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '娴佹按涓婚敭ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浜х敓鏃堕棿鎴?,
  `out_role` char(30) NOT NULL DEFAULT '' COMMENT '鍑鸿处鏂硅鑹诧細vip/team/admin',
  `out_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍑鸿处鏂笽D锛氫釜浜篿d/鍥綋id/骞冲彴',
  `in_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍏ヨ处鏂笽D锛氫釜浜篿d/鍥綋id/骞冲彴',
  `in_role` char(30) NOT NULL DEFAULT '' COMMENT '鍏ヨ处鏂硅鑹诧細vip/team/admin',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '閲戦锛屼袱浣嶅皬鏁?,
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '娴佹按绫诲瀷锛?->涓汉閽卞寘鍏呭€硷紝2->涓汉閽卞寘鎻愮幇锛?->璧勫姪鍛ㄦ湡鑷姩鎷ㄦ锛?->涓汉閽卞寘鑷冲洟浣撻挶鍖咃紝5->绔欏唴杞处',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '瀹屾垚鏃堕棿鎴?,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '褰撳墠鐘舵€侊細0->宸插畬鎴愶紝1->鎻愮幇鐢宠寰呭畬鎴?,
  PRIMARY KEY (`flows_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flows`
--

LOCK TABLES `flows` WRITE;
/*!40000 ALTER TABLE `flows` DISABLE KEYS */;
/*!40000 ALTER TABLE `flows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guardian`
--

DROP TABLE IF EXISTS `guardian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guardian` (
  `guardian_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '鐩戞姢浜轰俊鎭富閿甀D',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍏宠仈瀛︾敓user_id',
  `truename` varchar(255) NOT NULL DEFAULT '' COMMENT '鐩戞姢浜虹湡瀹炲鍚?,
  `relation` varchar(255) NOT NULL DEFAULT '鐖跺瓙' COMMENT '瀛︾敓涓庣洃鎶や汉鍏崇郴',
  `idcard` varchar(255) NOT NULL DEFAULT '韬唤璇佸彿鐮佹湭鐭? COMMENT '鐩戞姢浜鸿韩浠借瘉鍙风爜',
  `number` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '鐩戞姢浜烘墜鏈哄彿鐮?,
  `address` varchar(255) NOT NULL DEFAULT '鍦板潃鏈煡' COMMENT '鐩戞姢浜哄父灞呬綇鍦板潃',
  PRIMARY KEY (`guardian_id`,`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardian`
--

LOCK TABLES `guardian` WRITE;
/*!40000 ALTER TABLE `guardian` DISABLE KEYS */;
/*!40000 ALTER TABLE `guardian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jointeam`
--

DROP TABLE IF EXISTS `jointeam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jointeam` (
  `jointeam_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '鍔犲叆鍥綋鐨勯個璇?鐢宠',
  `sendtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍙戠敓鏃堕棿鎴?,
  `from` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍙戣捣鑰呯殑user_id',
  `to` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鎺ュ彈鑰呯殑user_id',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍏宠仈鐨勫洟浣搃d',
  `message` varchar(255) NOT NULL DEFAULT '' COMMENT '鍙戦€佽€呴檮涓婄殑鐣欒█',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '瀹炴椂鐘舵€侊細1->寰呭洖澶嶏紝2->鍚屾剰锛?->鎷掓帴',
  PRIMARY KEY (`jointeam_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jointeam`
--

LOCK TABLES `jointeam` WRITE;
/*!40000 ALTER TABLE `jointeam` DISABLE KEYS */;
/*!40000 ALTER TABLE `jointeam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '绔欏唴淇′富閿甀D',
  `sendtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍙戦€佹椂闂存埑',
  `from` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍙戦€佽€卽ser_id',
  `to` int(10) unsigned NOT NULL COMMENT '鎺ュ彈鑰卽ser',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '鏍囬',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '姝ｆ枃鍐呭',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '鐘舵€侊細1->鍙戦€佹垚鍔燂紝2->宸茶',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `team_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '鍥綋涓婚敭ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍥綋鍒涘缓鏃堕棿鎴?,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍒涘缓鑰呯殑user_id',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '鍥綋鐘舵€侊細0->鍐荤粨锛?->姝ｅ父杩愯鐘舵€侊紝2->鍒涘缓瀹℃牳涓姸鎬?,
  `balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '鍥綋瀹炴椂浣欓锛屼袱浣嶅皬鏁?,
  `name` varchar(255) NOT NULL DEFAULT '鏌愬闄㈡煇鐝骇' COMMENT '鍥綋鍚嶇О锛氱彮绾у悕绉?,
  `community_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '闅跺睘绀惧尯id',
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=514000015 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` VALUES (514000014,1569045882,4,1,0.00,'鍖栧1603鐝?,51400001);
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '涓婚敭ID',
  `number` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '鎵嬫満鍙风爜',
  `email` char(100) NOT NULL DEFAULT '' COMMENT '鐢靛瓙閭鍦板潃',
  `username` char(32) NOT NULL DEFAULT '' COMMENT '鐢ㄦ埛鍚嶏紝鏄电О',
  `password` char(100) NOT NULL DEFAULT '' COMMENT '瀵嗙爜',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍒涘缓鏃堕棿鎴?,
  `logintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鏈€鍚庝竴娆＄櫥褰曟椂闂?,
  `loginip` char(32) NOT NULL DEFAULT '' COMMENT '鏈€鍚庝竴娆＄櫥褰旾P鍦板潃',
  `status` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '褰撳墠鐘舵€侊細0->鍐荤粨鏃犳硶鐧诲叆鐘舵€侊紝1->姝ｅ父浣跨敤鐘舵€侊紝2->璧勬枡寰呭畬鍠勭姸鎬侊紝3->瀹屽杽璧勬枡寰呭鏍哥姸鎬侊紝4->鏈縺娲婚樆姝㈢櫥褰曠姸鎬?,
  `token` char(100) NOT NULL DEFAULT '' COMMENT '娉ㄥ唽閭欢閾炬帴婵€娲诲弬鏁皌oken',
  `role` char(32) NOT NULL DEFAULT '' COMMENT '韬唤锛氳秴绾х鐞嗗憳admin,璧勫姪鑰卻ponsor,鍦ㄦ牎瀛︾敓student,瑙佽瘉浜?绀惧尯绠＄悊鑰厀itness',
  `truename` char(32) NOT NULL DEFAULT '' COMMENT '鐪熷疄濮撳悕',
  `image` char(100) NOT NULL DEFAULT './image/default.jpg' COMMENT '澶村儚鐩稿璺緞',
  `idcard` char(32) NOT NULL DEFAULT '' COMMENT '韬唤璇佸彿鐮?,
  `idcardfront` char(100) NOT NULL DEFAULT './image/idcardfront.jpg' COMMENT '韬唤璇佹闈㈢浉瀵硅矾寰?,
  `idcardback` char(100) NOT NULL DEFAULT './image/idcardback.jpg' COMMENT '韬唤璇佸弽闈㈢浉瀵硅矾寰?,
  `verification` char(32) NOT NULL DEFAULT '' COMMENT '閭/鐭俊楠岃瘉鐮?,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '浣欓',
  `alipay` char(100) NOT NULL DEFAULT '' COMMENT '鏀粯瀹濊处鍙?,
  `wechat` char(100) NOT NULL DEFAULT '' COMMENT '寰俊璐﹀彿',
  `sex` char(30) NOT NULL DEFAULT '' COMMENT '鎬у埆',
  `birthday` char(30) NOT NULL DEFAULT '' COMMENT '鐢熸棩',
  `address` char(100) NOT NULL DEFAULT '' COMMENT '甯稿眳浣忓湴鍧€',
  `company` char(100) NOT NULL DEFAULT '' COMMENT '鍗曚綅/鍏徃',
  `remarks` char(250) NOT NULL DEFAULT '' COMMENT '澶囨敞',
  `version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鐗堟湰鍙凤紙涔愯閿侊級',
  PRIMARY KEY (`user_id`,`number`,`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,15989119518,'972436798@rlr.com','iamadmin','$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty',0,1569930202,'::1',1,'','admin','','./image/default.jpg','','./image/idcardfront.jpg','./image/idcardback.jpg','',0.00,'','','','','','','',8),(23,0,'972436798@qq.com','yui','$2y$10$brbpuArBb1mJ7tHmQFGO8eCzSTBw6Rvzcj2ci2j96TXjoHBpQ8oKW',0,1569937024,'::1',1,'$2y$10$sHxVwWsZghp2BOodNrPHZOTeiQ/Nh3YugAK3BPyi69JpeChS8wA/e','witness','','./image/default.jpg','','./image/idcardfront.jpg','./image/idcardback.jpg','',0.00,'','','','','','','',20),(24,17875303902,'liu972436798@163.com','鍟婂灒','$2y$10$xjmDmhiWCo8/POOrGFWpOuZQKwkUZOiNS5vcRU8J8z7JY4gVPYX/a',1569593875,1569935598,'::1',1,'$2y$10$wLdJijolr3gLIojWLQuK5eNLub/jRUIuDtzwadySukGxRljzjJFN.','vip','鍚村溅绁?,'./image/default.jpg','','./image/idcardfront.jpg','./image/idcardback.jpg','',0.00,'15989119518','','淇濆瘑','','','','',37),(25,18379874835,'avip@rlr.com','gakki','$2y$10$xjmDmhiWCo8/POOrGFWpOuZQKwkUZOiNS5vcRU8J8z7JY4gVPYX/a',0,1569935291,'::1',1,'','vip','鍟婂灒鍟?,'./image/default.jpg','','./image/idcardfront.jpg','./image/idcardback.jpg','',0.00,'15989119518','','濂?,'','','','',7);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_team`
--

DROP TABLE IF EXISTS `user_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_team` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鐢ㄦ埛ID',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍥綋ID',
  PRIMARY KEY (`user_id`,`team_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_team`
--

LOCK TABLES `user_team` WRITE;
/*!40000 ALTER TABLE `user_team` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_vote`
--

DROP TABLE IF EXISTS `user_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_vote` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鎶曠エ鑰卽ser_id',
  `vote_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鎵€鍙備笌姝ゆ鎶曠エ娲诲姩鐨処D',
  `vote_res_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鎶曠粰鏌愪釜鍊欓€夎€呯殑ID',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_vote`
--

LOCK TABLES `user_vote` WRITE;
/*!40000 ALTER TABLE `user_vote` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_vote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote`
--

DROP TABLE IF EXISTS `vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote` (
  `vote_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '涓€娆℃姇绁ㄦ椿鍔ㄧ殑涓婚敭ID',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '闅跺睘鍥綋鐨処D',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '姝ゆ鎶曠エ鐨勬爣棰?,
  `support_num` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '鏈€缁堣祫鍔╃殑浜烘暟',
  `candidate_num` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '鍊欓€変汉鏁?,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '寮€濮嬫姇绁ㄦ椂闂存埑',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鑷姩缁撴潫鏃堕棿鎴?,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '瀹炴椂鐘舵€侊細1->鎶曠エ涓紝2->鎶曠エ宸茬粨鏉?,
  PRIMARY KEY (`vote_id`,`team_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote`
--

LOCK TABLES `vote` WRITE;
/*!40000 ALTER TABLE `vote` DISABLE KEYS */;
/*!40000 ALTER TABLE `vote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote_res`
--

DROP TABLE IF EXISTS `vote_res`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote_res` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '鎶曠エ浠讳綍鏃跺埢缁撴灉涓婚敭ID',
  `vote_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '闅跺睘鐨勬姇绁ㄦ椿鍔ㄧ殑ID',
  `username` varchar(255) NOT NULL DEFAULT '' COMMENT '鍊欓€夎€呭鐢熷鍚?,
  `wish_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍊欓€夎€呯殑蹇冩効ID',
  `amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鑾峰緱绁ㄦ暟',
  `result` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '缁撴灉锛?->鎶曠エ鏈粨鏉燂紝1->鑳滃嚭锛?->娣樻卑',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote_res`
--

LOCK TABLES `vote_res` WRITE;
/*!40000 ALTER TABLE `vote_res` DISABLE KEYS */;
/*!40000 ALTER TABLE `vote_res` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wish`
--

DROP TABLE IF EXISTS `wish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wish` (
  `wish_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '蹇冩効涓婚敭ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鎵€灞炵敤鎴风殑user_id',
  `token` char(100) NOT NULL DEFAULT '' COMMENT '瑙佽瘉浜轰骇鐢熷績鎰跨爜',
  `tokentime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浜х敓蹇冩効鐮佺殑鏃堕棿鎴?,
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '鏈熸湜鎬婚噾棰?,
  `transfered` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '宸茬粡杞处鐨勬湡鏁帮紝鍗曚綅鏈?,
  `month` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '璧勫姪鍛ㄦ湡锛屽崟浣嶏細30澶?,
  `label` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '鏍囩锛?->鍏跺畠锛?->鐏剧ジ锛?->鍗曚翰锛?->瀛ゅ効and so on....',
  `per` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '姣忎竴鏈熺殑鏈熸湜閲戦',
  `filepath` char(100) NOT NULL DEFAULT '' COMMENT '涓婁紶琛ュ厖鏂囦欢璺緞',
  `description` char(255) NOT NULL DEFAULT '' COMMENT '鎻忚堪/鍘熷洜',
  `verify_res` char(255) NOT NULL DEFAULT '' COMMENT '瀹℃牳鎵规敞',
  `verify_user_id` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '瀹℃牳鍛?瑙佽瘉浜?绀惧尯绠＄悊鍛?witness)鐨剈ser_id',
  `verify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '瀹℃牳鏃堕棿鎴?,
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鍚姩璧勫姪鍛ㄦ湡鏃堕棿鎴?,
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '瑙佽瘉浜哄惎鍔ㄨ祫鍔╁懆鏈熸椂闂存埑',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鏁翠釜蹇冩効瀹屾垚缁撴潫鏃堕棿鎴?,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0->蹇冩効鐮佸緟婵€娲伙紝1->寰呭搴旇璇佷汉瀹℃牳锛?->瀹℃牳閫氳繃鍦ㄥ績鎰挎睜寰呰祫鍔╄€呰祫鍔╋紝3->璧勫姪鑰呯粦瀹氬緟绾夸笅鍗忓晢锛?->璧勫姪浜哄崗鍟嗗畬鎴愯繘鍏ヨ祫鍔╁懆鏈熶簡锛?->鍥綋閿佸畾寰呭崗鍟嗕腑锛?->鍥綋鍗忓晢瀹屾垚杩涘叆璧勫姪鍛ㄦ湡浜嗭紝7->鍥綋閫夊彇鎶曠エ涓紝9->瀹℃牳椹冲洖锛?0->璧勫姪瀹屾垚',
  `locking_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '閿佸畾鏃堕棿鎴?,
  `locking_team_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '閿佸畾鐨勫璞d锛氬洟浣搃d',
  `locking_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '閿佸畾鐨勫璞d锛氳祫鍔╀汉id',
  `version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '鐗堟湰鍙凤紙涔愯閿侊級',
  PRIMARY KEY (`wish_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1006 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wish`
--

LOCK TABLES `wish` WRITE;
/*!40000 ALTER TABLE `wish` DISABLE KEYS */;
INSERT INTO `wish` VALUES (1001,25,'84dd18f16bfc871d64a732f71b569bea',1569567946,200.00,0,5,0,40,'./file/wish/1001.docx','鍟﹀暒鍟︼紒','澶熺┓锛侊紒',0000000023,1569849630,1569814267,1564765200,0,4,1569935183,0,24,16),(1002,24,'abe9a0c36dd6e45ea84605f50e20e5c8',1569568104,200.00,0,5,1,40,'','鍠傚杺鍠傚暒鍟︼紒','鍦熻豹锛屽亣瑁?,0000000023,1569849652,1569814678,1547485200,0,4,0,0,23,4),(1003,0,'7e5a5151d15343afe83a4a77ab498318',1569589893,0.00,0,0,0,0,'','','',0000000023,0,0,0,0,0,0,0,0,0),(1004,0,'8ee9e37fd03e3568fc196f7235deb111',1569589922,0.00,0,0,0,0,'','','',0000000023,0,0,0,0,0,0,0,0,0),(1005,0,'bf14443c45ae596d2046c70c0ae3ad7b',1569677154,0.00,0,0,0,0,'','','',0000000023,0,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `wish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wish_flows`
--

DROP TABLE IF EXISTS `wish_flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wish_flows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '蹇冩効-娴佹按鍏宠仈琛ㄤ富閿?,
  `wish_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '蹇冩効wish_id',
  `flows_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '娴佹按flows_id',
  PRIMARY KEY (`id`,`wish_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wish_flows`
--

LOCK TABLES `wish_flows` WRITE;
/*!40000 ALTER TABLE `wish_flows` DISABLE KEYS */;
/*!40000 ALTER TABLE `wish_flows` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-06 11:41:50
