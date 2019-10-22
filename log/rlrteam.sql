-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2019-10-22 10:19:05
-- 服务器版本： 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rlrteam`
--

-- --------------------------------------------------------

--
-- 表的结构 `community`
--

CREATE TABLE `community` (
  `community_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '社区主键ID',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '社区创建时间戳',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联的主见证人账号ID',
  `minpercent` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '资助者资助时对应心愿最小余额比',
  `community_name` varchar(255) NOT NULL DEFAULT '' COMMENT '社区名称：学院/学校',
  `remarks` varchar(255) NOT NULL DEFAULT '' COMMENT '社区备注/介绍/描述',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态：1->正常使用，0->冻结状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `community`
--

INSERT INTO `community` (`community_id`, `createtime`, `user_id`, `minpercent`, `community_name`, `remarks`, `status`) VALUES
(51400007, 1569932691, 23, 27, '梅州蕉岭中学', '啧啧啧', 1);

-- --------------------------------------------------------

--
-- 表的结构 `flows`
--

CREATE TABLE `flows` (
  `flows_id` int(10) UNSIGNED NOT NULL COMMENT '流水主键ID',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产生时间戳',
  `out_role` char(30) NOT NULL DEFAULT '' COMMENT '出账方角色：vip/team/admin',
  `out_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '出账方ID：个人id/团体id/平台',
  `in_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '入账方ID：个人id/团体id/平台',
  `in_role` char(30) NOT NULL DEFAULT '' COMMENT '入账方角色：vip/team/admin',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '金额，两位小数',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '流水类型：1->个人钱包充值，2->个人钱包提现，3->资助周期自动拨款，4->个人钱包至团体钱包，5->站内转账',
  `endtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '完成时间戳',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前状态：0->已完成，1->提现申请待完成'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `flows`
--

INSERT INTO `flows` (`flows_id`, `createtime`, `out_role`, `out_id`, `in_id`, `in_role`, `money`, `type`, `endtime`, `status`) VALUES
(37, 1571146535, 'vipAlipay', 24, 24, 'vipPurse', '30.00', 1, 1571146611, 0),
(38, 1571212544, 'vipPurse', 24, 1004, 'teamPurse', '29.00', 4, 0, 0),
(39, 1571215633, 'vipAlipay', 24, 24, 'vipPurse', '7.00', 1, 1571215678, 0),
(42, 1571216560, 'vipPurse', 24, 25, 'vipPurse', '4.00', 3, 1571216560, 0),
(43, 1571216560, 'vipPurse', 24, 25, 'vipPurse', '4.00', 3, 1571216560, 0),
(44, 1571458649, 'vipPurse', 24, 1004, 'teamPurse', '1.00', 4, 1571458649, 0),
(45, 1571465599, 'vipAlipay', 25, 25, 'vipPurse', '22.00', 1, 1571465690, 0),
(46, 1571465734, 'vipPurse', 25, 1004, 'teamPurse', '2.00', 4, 1571465734, 0);

-- --------------------------------------------------------

--
-- 表的结构 `guardian`
--

CREATE TABLE `guardian` (
  `guardian_id` int(10) UNSIGNED NOT NULL COMMENT '监护人信息主键ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联学生user_id',
  `truename` varchar(255) NOT NULL DEFAULT '' COMMENT '监护人真实姓名',
  `relation` varchar(255) NOT NULL DEFAULT '父子' COMMENT '学生与监护人关系',
  `idcard` varchar(255) NOT NULL DEFAULT '身份证号码未知' COMMENT '监护人身份证号码',
  `number` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '监护人手机号码',
  `address` varchar(255) NOT NULL DEFAULT '地址未知' COMMENT '监护人常居住地址'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `jointeam`
--

CREATE TABLE `jointeam` (
  `jointeam_id` int(10) UNSIGNED NOT NULL COMMENT '加入团体的邀请/申请',
  `sendtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发生时间戳',
  `from` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发起者的user_id',
  `to` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '接受者的user_id',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联的团体id',
  `message` varchar(255) NOT NULL DEFAULT '' COMMENT '发送者附上的留言',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '实时状态：1->待回复，2->同意，3->拒接'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE `message` (
  `message_id` int(10) UNSIGNED NOT NULL COMMENT '站内信主键ID',
  `sendtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送时间戳',
  `from` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送者user_id',
  `to` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '接受者user_id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '信息类型:0->普通信息，1->系统通知，2->团体加入邀请，3->团体通知，4->社区通知',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '正文内容',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态：1->发送成功，2->已读'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `message`
--

INSERT INTO `message` (`message_id`, `sendtime`, `from`, `to`, `title`, `type`, `content`, `status`) VALUES
(1, 1570437979, 24, 23, 'gakki', 0, 'yui\'s husband', 1),
(7, 1570684260, 25, 26, '', 2, '', 4),
(8, 1570698381, 25, 24, '', 2, '', 3),
(9, 1570698774, 25, 24, '', 2, '', 0),
(10, 1570700701, 24, 26, '', 2, '', 3),
(13, 1570770700, 26, 30, '来玩啊', 0, '耶梦加得', 1),
(14, 1570860850, 24, 25, '团体加入邀请', 2, '新垣结衣  用户邀请您加入 艾欧尼亚 团体！', 3),
(15, 1570861355, 24, 30, '团体加入邀请', 2, '新垣结衣  用户邀请您加入 艾欧尼亚 团体！', 3);

-- --------------------------------------------------------

--
-- 表的结构 `team`
--

CREATE TABLE `team` (
  `team_id` int(10) UNSIGNED NOT NULL COMMENT '团体主键ID',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '团体创建时间戳',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建者的user_id',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '团体状态：0->冻结，1->正常运行状态，2->创建审核中状态',
  `balance` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '团体实时余额，两位小数',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '团体名称：班级名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `team`
--

INSERT INTO `team` (`team_id`, `createtime`, `user_id`, `status`, `balance`, `name`) VALUES
(1003, 1570601465, 25, 1, '0.00', '恕瑞玛'),
(1004, 1570609933, 24, 1, '32.00', '艾欧尼亚'),
(1005, 1570635376, 25, 1, '0.00', '祖安');

-- --------------------------------------------------------

--
-- 表的结构 `team_message`
--

CREATE TABLE `team_message` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '主键ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '团体ID',
  `message_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '信息ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `team_message`
--

INSERT INTO `team_message` (`id`, `team_id`, `message_id`) VALUES
(6, 1003, 7),
(7, 1003, 8),
(8, 1005, 9),
(9, 1004, 10),
(10, 1004, 14),
(11, 1004, 15);

-- --------------------------------------------------------

--
-- 表的结构 `trade`
--

CREATE TABLE `trade` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '主键ID',
  `trade_no` char(30) NOT NULL DEFAULT '' COMMENT '支付宝反还的交易单号',
  `out_trade_no` bigint(17) UNSIGNED NOT NULL DEFAULT '0' COMMENT '平台此次交易号',
  `flows_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联的流水单号',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '交易类型：付款，退款',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前状态',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '金额大小'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `trade`
--

INSERT INTO `trade` (`id`, `trade_no`, `out_trade_no`, `flows_id`, `type`, `status`, `money`) VALUES
(37, '2019101522001448191000035944', 20191015213535866, 37, 0, 1, '30.00'),
(38, '2019101622001448191000038237', 20191016164713976, 39, 0, 1, '7.00'),
(39, '2019101922001448191000040569', 20191019141319128, 45, 0, 1, '22.00');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '主键ID',
  `number` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '手机号码',
  `email` char(100) NOT NULL DEFAULT '' COMMENT '电子邮箱地址',
  `username` char(32) NOT NULL DEFAULT '' COMMENT '用户名，昵称',
  `password` char(100) NOT NULL DEFAULT '' COMMENT '密码',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `logintime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后一次登录时间',
  `loginip` char(32) NOT NULL DEFAULT '' COMMENT '最后一次登录IP地址',
  `status` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '当前状态：0->冻结无法登入状态，1->正常使用状态，2->资料待完善状态，3->完善资料待审核状态，4->未激活阻止登录状态',
  `token` char(100) NOT NULL DEFAULT '' COMMENT '注册邮件链接激活参数token',
  `role` char(32) NOT NULL DEFAULT '' COMMENT '身份：超级管理员admin,资助者sponsor,在校学生student,见证人/社区管理者witness',
  `truename` char(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `image` char(100) NOT NULL DEFAULT './image/default.jpg' COMMENT '头像相对路径',
  `idcard` char(32) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `idcardfront` char(100) NOT NULL DEFAULT './image/idcardfront.jpg' COMMENT '身份证正面相对路径',
  `idcardback` char(100) NOT NULL DEFAULT './image/idcardback.jpg' COMMENT '身份证反面相对路径',
  `verification` char(32) NOT NULL DEFAULT '' COMMENT '邮箱/短信验证码',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `alipay` char(100) NOT NULL DEFAULT '' COMMENT '支付宝账号',
  `wechat` char(100) NOT NULL DEFAULT '' COMMENT '微信账号',
  `sex` char(30) NOT NULL DEFAULT '' COMMENT '性别',
  `birthday` char(30) NOT NULL DEFAULT '' COMMENT '生日',
  `address` char(100) NOT NULL DEFAULT '' COMMENT '常居住地址',
  `company` char(100) NOT NULL DEFAULT '' COMMENT '单位/公司',
  `remarks` char(250) NOT NULL DEFAULT '' COMMENT '备注',
  `version` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '版本号（乐观锁）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `number`, `email`, `username`, `password`, `createtime`, `logintime`, `loginip`, `status`, `token`, `role`, `truename`, `image`, `idcard`, `idcardfront`, `idcardback`, `verification`, `balance`, `alipay`, `wechat`, `sex`, `birthday`, `address`, `company`, `remarks`, `version`) VALUES
(1, 15989119518, '972436798@rlr.com', 'iamadmin', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 1569930202, '::1', 1, '', 'admin', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', '0.00', '', '', '', '', '', '', '', 8),
(23, 0, '972436798@qq.com', '一个见证人', '$2y$10$brbpuArBb1mJ7tHmQFGO8eCzSTBw6Rvzcj2ci2j96TXjoHBpQ8oKW', 0, 1571672756, '::1', 1, '$2y$10$sHxVwWsZghp2BOodNrPHZOTeiQ/Nh3YugAK3BPyi69JpeChS8wA/e', 'witness', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', '0.00', '', '', '', '', '', '', '', 27),
(24, 17875303902, 'liu972436798@163.com', '新垣结衣', '$2y$10$xjmDmhiWCo8/POOrGFWpOuZQKwkUZOiNS5vcRU8J8z7JY4gVPYX/a', 1569593875, 1571672765, '::1', 1, '$2y$10$wLdJijolr3gLIojWLQuK5eNLub/jRUIuDtzwadySukGxRljzjJFN.', 'vip', 'lsh', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', '0.00', '15989119518', '', '保密', '', '', '', 'yuiiiiiiiii', 80),
(25, 18379874835, 'rlrteam@163.com', '千反田', '$2y$10$xjmDmhiWCo8/POOrGFWpOuZQKwkUZOiNS5vcRU8J8z7JY4gVPYX/a', 0, 1571659746, '::1', 1, '', 'vip', '啊垣啊', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', '28.00', '15989119518', '', '女', '', '', '', '', 25),
(26, 18379873333, 'mqdqxaragaki@sina.com', '弥弥弥弥弥弥弥弥', '$2y$10$YeoL34dxicGU3iAlrh./4eXKzsOm3UvAJdYFfNZQkDMOF7ySqXw4.', 1570545613, 1571660095, '::1', 1, '$2y$10$RKJkM59FK.mM8zlaM4jVWuJVFOw3b7bmLh/vZKq6gtIwSwBl.wn4y', 'vip', '楚夏弥', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', '0.00', '15989119518', '', '保密', '', '', '', '', 21),
(30, 13000000000, '17875303902@sina.cn', '栗山未来', '$2y$10$//Ij5TsXuLguz5z6DHOS9Owr5bUliu9KEG21MvnBBL4LiLBheZoDm', 1570770699, 1571671772, '::1', 1, '$2y$10$JSws40WPUvOExsOFw5cvhu1xM9xwYHiNFSr78xiFVV8v2etw0rEhy', 'vip', '三笠阿卡曼', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', '0.00', '15989119518', '', '女', '', '', '', '', 7);

-- --------------------------------------------------------

--
-- 表的结构 `user_team`
--

CREATE TABLE `user_team` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '关联表主键ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '团体ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_team`
--

INSERT INTO `user_team` (`id`, `user_id`, `team_id`) VALUES
(1, 25, 1003),
(2, 24, 1004),
(3, 25, 1005),
(4, 26, 1004),
(5, 24, 1003),
(6, 25, 1004),
(7, 30, 1004);

-- --------------------------------------------------------

--
-- 表的结构 `user_vote`
--

CREATE TABLE `user_vote` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '主键ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '投票者user_id',
  `vote_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所参与此次投票活动的ID',
  `wish_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '投给某个心愿的ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_vote`
--

INSERT INTO `user_vote` (`id`, `user_id`, `vote_id`, `wish_id`) VALUES
(1, 24, 1, 1003),
(2, 25, 1, 1003),
(4, 26, 1, 1004),
(5, 30, 1, 1004);

-- --------------------------------------------------------

--
-- 表的结构 `vote`
--

CREATE TABLE `vote` (
  `vote_id` int(10) UNSIGNED NOT NULL COMMENT '一次投票活动的主键ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '隶属团体的ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '此次投票的标题',
  `support_num` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最终资助的人数',
  `candidate_num` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '候选人数',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `starttime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '开始投票时间戳',
  `endtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '自动结束时间戳',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '实时状态：0-待启动，1->投票中，2->投票已结束',
  `version` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '版本号(乐观锁)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `vote`
--

INSERT INTO `vote` (`vote_id`, `team_id`, `title`, `support_num`, `candidate_num`, `createtime`, `starttime`, `endtime`, `status`, `version`) VALUES
(1, 1004, '救死扶伤', 1, 2, 1571317276, 1571492016, 1571970804, 1, 14),
(2, 1004, '悬壶济世', 2, 3, 1571319913, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `vote_res`
--

CREATE TABLE `vote_res` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '投票任何时刻结果主键ID',
  `vote_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '隶属的投票活动的ID',
  `wish_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '候选者的心愿ID',
  `amount` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '获得票数',
  `result` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '结果：0->投票未结束，1->胜出，2->淘汰',
  `version` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '版本号(乐观锁)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `vote_res`
--

INSERT INTO `vote_res` (`id`, `vote_id`, `wish_id`, `amount`, `result`, `version`) VALUES
(23, 1, 1003, 2, 0, 2),
(24, 1, 1004, 2, 0, 3);

-- --------------------------------------------------------

--
-- 表的结构 `wish`
--

CREATE TABLE `wish` (
  `wish_id` int(10) UNSIGNED NOT NULL COMMENT '心愿主键ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属用户的user_id',
  `token` char(100) NOT NULL DEFAULT '' COMMENT '见证人产生心愿码',
  `tokentime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产生心愿码的时间戳',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '期望总金额',
  `transfered` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '已经转账的期数，单位月',
  `month` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '资助周期，单位：30天',
  `label` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '标签：0->其它，1->灾祸，2->单亲，3->孤儿and so on....',
  `per` decimal(10,0) UNSIGNED NOT NULL DEFAULT '0' COMMENT '每一期的期望金额',
  `filepath` char(100) NOT NULL DEFAULT '' COMMENT '上传补充文件路径',
  `description` char(255) NOT NULL DEFAULT '' COMMENT '描述/原因',
  `verify_res` char(255) NOT NULL DEFAULT '' COMMENT '审核批注',
  `verify_user_id` int(10) UNSIGNED ZEROFILL NOT NULL DEFAULT '0000000000' COMMENT '审核员/见证人/社区管理员(witness)的user_id',
  `verify_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核时间戳',
  `publish_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '启动资助周期时间戳',
  `start_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '见证人启动资助周期时间戳',
  `end_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '整个心愿完成结束时间戳',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0->心愿码待激活，1->待对应见证人审核，2->审核通过在心愿池待资助者资助，3->资助者绑定待线下协商，4->资助人协商完成进入资助周期了，5->团体锁定待协商中，6->团体协商完成进入资助周期了，7->团体选取投票中，9->审核驳回，10->资助完成',
  `locking_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '锁定时间戳',
  `locking_team_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '锁定的对象id：团体id',
  `locking_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '锁定的对象id：资助人id',
  `version` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '版本号（乐观锁）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `wish`
--

INSERT INTO `wish` (`wish_id`, `user_id`, `token`, `tokentime`, `money`, `transfered`, `month`, `label`, `per`, `filepath`, `description`, `verify_res`, `verify_user_id`, `verify_time`, `publish_time`, `start_time`, `end_time`, `status`, `locking_time`, `locking_team_id`, `locking_user_id`, `version`) VALUES
(1001, 25, '84dd18f16bfc871d64a732f71b569bea', 1569567946, '20.00', 2, 5, 0, '4', './file/wish/1001.docx', '啦啦啦！', '够穷！！', 0000000023, 1569849659, 1569814267, 1565888400, 1571216560, 4, 1569935183, 0, 24, 20),
(1002, 24, 'abe9a0c36dd6e45ea84605f50e20e5c8', 1569568104, '20.00', 0, 5, 1, '4', '', '喂喂喂啦啦！', '土豪，假装', 0000000023, 1569849652, 1569814678, 1547485200, 0, 4, 0, 0, 23, 4),
(1003, 30, '7e5a5151d15343afe83a4a77ab498318', 1569589893, '18.00', 0, 6, 3, '3', '', '一个原因吧测试', '谁的未来？', 0000000023, 1571290178, 1571288086, 0, 0, 7, 1571492016, 1004, 0, 15),
(1004, 26, '8ee9e37fd03e3568fc196f7235deb111', 1569589922, '30.00', 0, 3, 1, '10', '', '弥夏哇', '师兄楚', 0000000023, 1571290199, 1571289923, 0, 0, 7, 1571492016, 1004, 0, 15),
(1005, 1, 'bf14443c45ae596d2046c70c0ae3ad7b', 1569677154, '20.00', 0, 4, 0, '5', '', 'cxx', 'cxx\'s husband', 0000000023, 0, 0, 0, 0, 2, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `wish_flows`
--

CREATE TABLE `wish_flows` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '心愿-流水关联表主键',
  `wish_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '心愿wish_id',
  `flows_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '流水flows_id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `wish_flows`
--

INSERT INTO `wish_flows` (`id`, `wish_id`, `flows_id`) VALUES
(3, 1001, 42),
(4, 1001, 43);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `community`
--
ALTER TABLE `community`
  ADD PRIMARY KEY (`community_id`);

--
-- Indexes for table `flows`
--
ALTER TABLE `flows`
  ADD PRIMARY KEY (`flows_id`);

--
-- Indexes for table `guardian`
--
ALTER TABLE `guardian`
  ADD PRIMARY KEY (`guardian_id`,`user_id`) USING BTREE;

--
-- Indexes for table `jointeam`
--
ALTER TABLE `jointeam`
  ADD PRIMARY KEY (`jointeam_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `team_message`
--
ALTER TABLE `team_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trade`
--
ALTER TABLE `trade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`) USING BTREE;

--
-- Indexes for table `user_team`
--
ALTER TABLE `user_team`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `user_vote`
--
ALTER TABLE `user_vote`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `vote`
--
ALTER TABLE `vote`
  ADD PRIMARY KEY (`vote_id`) USING BTREE;

--
-- Indexes for table `vote_res`
--
ALTER TABLE `vote_res`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wish`
--
ALTER TABLE `wish`
  ADD PRIMARY KEY (`wish_id`);

--
-- Indexes for table `wish_flows`
--
ALTER TABLE `wish_flows`
  ADD PRIMARY KEY (`id`,`wish_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `flows`
--
ALTER TABLE `flows`
  MODIFY `flows_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '流水主键ID', AUTO_INCREMENT=47;
--
-- 使用表AUTO_INCREMENT `guardian`
--
ALTER TABLE `guardian`
  MODIFY `guardian_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '监护人信息主键ID';
--
-- 使用表AUTO_INCREMENT `jointeam`
--
ALTER TABLE `jointeam`
  MODIFY `jointeam_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '加入团体的邀请/申请';
--
-- 使用表AUTO_INCREMENT `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '站内信主键ID', AUTO_INCREMENT=16;
--
-- 使用表AUTO_INCREMENT `team`
--
ALTER TABLE `team`
  MODIFY `team_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '团体主键ID', AUTO_INCREMENT=1006;
--
-- 使用表AUTO_INCREMENT `team_message`
--
ALTER TABLE `team_message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=12;
--
-- 使用表AUTO_INCREMENT `trade`
--
ALTER TABLE `trade`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=40;
--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=31;
--
-- 使用表AUTO_INCREMENT `user_team`
--
ALTER TABLE `user_team`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '关联表主键ID', AUTO_INCREMENT=8;
--
-- 使用表AUTO_INCREMENT `user_vote`
--
ALTER TABLE `user_vote`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=6;
--
-- 使用表AUTO_INCREMENT `vote`
--
ALTER TABLE `vote`
  MODIFY `vote_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '一次投票活动的主键ID', AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `vote_res`
--
ALTER TABLE `vote_res`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '投票任何时刻结果主键ID', AUTO_INCREMENT=25;
--
-- 使用表AUTO_INCREMENT `wish`
--
ALTER TABLE `wish`
  MODIFY `wish_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '心愿主键ID', AUTO_INCREMENT=1006;
--
-- 使用表AUTO_INCREMENT `wish_flows`
--
ALTER TABLE `wish_flows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '心愿-流水关联表主键', AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
