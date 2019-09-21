-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2019-09-21 15:39:40
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
  `community_name` varchar(255) NOT NULL DEFAULT '' COMMENT '社区名称：学院/学校',
  `remarks` varchar(255) NOT NULL DEFAULT '' COMMENT '社区备注/介绍/描述',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态：1->正常使用，0->冻结状态'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `community`
--

INSERT INTO `community` (`community_id`, `createtime`, `user_id`, `community_name`, `remarks`, `status`) VALUES
(51400001, 1569038433, 2, '嘉应学院化学院', '无备注', 1);

-- --------------------------------------------------------

--
-- 表的结构 `flows`
--

CREATE TABLE `flows` (
  `flows_id` int(10) UNSIGNED NOT NULL COMMENT '流水主键ID',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产生时间戳',
  `out_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '出账方ID：个人id/团体id/平台',
  `in_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '入账方ID：个人id/团体id/平台',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '金额，两位小数',
  `category` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '流水类型：1->个人账户充值，2->体现，3->资助周期自动拨款',
  `endtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '完成时间戳，0->表示人工位尚未核对/操作'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `to` int(10) UNSIGNED NOT NULL COMMENT '接受者user',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '正文内容',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态：1->发送成功，2->已读'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `team`
--

CREATE TABLE `team` (
  `team_id` int(10) UNSIGNED NOT NULL COMMENT '团体主键ID',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '团体创建时间戳',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建者的user_id',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '团体状态：0->冻结，1->正常运行状态，2->创建审核中状态',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '团体实时余额，两位小数',
  `name` varchar(255) NOT NULL DEFAULT '某学院某班级' COMMENT '团体名称：班级名称',
  `community_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '隶属社区id'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `team`
--

INSERT INTO `team` (`team_id`, `createtime`, `user_id`, `status`, `money`, `name`, `community_id`) VALUES
(514000014, 1569045882, 4, 1, '0.00', '化学1603班', 51400001);

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
  `status` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '当前状态：0->冻结无法登入状态，1->正常使用状态，2->资料待完善状态，3->完善资料待审核状态',
  `role` char(32) NOT NULL DEFAULT '' COMMENT '身份：超级管理员admin,资助者sponsor,在校学生student,见证人/社区管理者witness',
  `truename` char(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `image` char(100) NOT NULL DEFAULT './image/default.jpg' COMMENT '头像相对路径',
  `idcard` char(32) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `idcardfront` char(100) NOT NULL DEFAULT './image/idcardfront.jpg' COMMENT '身份证正面相对路径',
  `idcardback` char(100) NOT NULL DEFAULT './image/idcardback.jpg' COMMENT '身份证反面相对路径',
  `verification` char(32) NOT NULL DEFAULT '' COMMENT '邮箱/短信验证码',
  `community_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属社区的社区ID',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `alipay` char(100) NOT NULL DEFAULT '支付宝账号未知' COMMENT '支付宝账号',
  `wechat` char(100) NOT NULL DEFAULT '微信账号未知' COMMENT '微信账号',
  `sex` char(30) NOT NULL DEFAULT '未知' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '2016-01-01' COMMENT '生日',
  `address` char(100) NOT NULL DEFAULT '地址未知' COMMENT '常居住地址',
  `company` char(100) NOT NULL DEFAULT '公司未知' COMMENT '单位/公司',
  `remarks` char(250) NOT NULL DEFAULT '很懒，什么都没有留下' COMMENT '备注',
  `version` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '版本号（乐观锁）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `number`, `email`, `username`, `password`, `createtime`, `logintime`, `loginip`, `status`, `role`, `truename`, `image`, `idcard`, `idcardfront`, `idcardback`, `verification`, `community_id`, `balance`, `alipay`, `wechat`, `sex`, `birthday`, `address`, `company`, `remarks`, `version`) VALUES
(1, 15989119518, '972436798@qq.com', 'i am admin', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'admin', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(2, 10000000100, '10000000100@rlr.com', 'one witness', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'witness', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(3, 10000000200, '10000000200@rlr.com', 'two witness', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'witness', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(4, 10000000001, '10000000001@rlr.com', 'one sponsor', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'sponsor', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(5, 10000000002, '10000000002@rlr.com', 'two sponsor', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'sponsor', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(6, 10000000003, '10000000003@rlr.com', 'three sponsor', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'sponsor', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '很懒，什么都没有留下', 0),
(7, 10000000004, '10000000004@rlr.com', 'four sponsor', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'sponsor', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '很懒，什么都没有留下', 0),
(8, 10000000005, '10000000005@rlr.com', 'five sponsor', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'sponsor', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(9, 10000000006, '10000000006@rlr.com', 'six sponsor', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'sponsor', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(10, 10000000010, '10000000010@rlr.com', 'one student', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'student', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(11, 10000000020, '10000000020@rlr.com', 'two student', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'student', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(12, 10000000030, '10000000030@rlr.com', 'three student', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'student', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(13, 10000000040, '10000000040@rlr.com', 'four student', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'student', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0),
(14, 10000000050, '10000000050@rlr.com', 'five student', '$2y$10$v1VPFJDMRoOE7SuetyHlQuB9Wyx9kdizLWOOurW8LXHDwV.USynty', 0, 0, '', 1, 'student', '', './image/default.jpg', '', './image/idcardfront.jpg', './image/idcardback.jpg', '', 0, '0.00', '支付宝账号未知', '微信账号未知', '未知', '2016-01-01', '地址未知', '公司未知', '这个人很懒，什么都没有留下', 0);

-- --------------------------------------------------------

--
-- 表的结构 `user_team`
--

CREATE TABLE `user_team` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '团体ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_vote`
--

CREATE TABLE `user_vote` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '投票者user_id',
  `vote_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所参与此次投票活动的ID',
  `vote_res_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '投给某个候选者的ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `starttime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '开始投票时间戳',
  `endtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '自动结束时间戳',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '实时状态：1->投票中，2->投票已结束'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vote_res`
--

CREATE TABLE `vote_res` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '投票任何时刻结果主键ID',
  `vote_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '隶属的投票活动的ID',
  `username` varchar(255) NOT NULL DEFAULT '' COMMENT '候选者学生姓名',
  `wish_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '候选者的心愿ID',
  `amount` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '获得票数',
  `result` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '结果：0->投票未结束，1->胜出，2->淘汰'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `wish`
--

CREATE TABLE `wish` (
  `wish_id` int(10) UNSIGNED NOT NULL COMMENT '心愿主键ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属用户的user_id',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '期望金额，两位小数',
  `month` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '资助周期，单位：月',
  `label` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '标签：0->无，1->灾祸，2->单亲，3->孤儿and so on....',
  `range` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '可见范围社区：0->隶属社区，1->所以社区',
  `file` char(100) NOT NULL DEFAULT '' COMMENT '上传补充文件路径',
  `description` char(255) NOT NULL DEFAULT '' COMMENT '描述/原因',
  `verify` tinyint(1) NOT NULL COMMENT '是否审核/审核结果，0->未被审核，1->审核通过，2-审核拒绝',
  `verify_res` char(255) NOT NULL DEFAULT '' COMMENT '审核批注',
  `verify_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核员/见证人/社区管理员(witness)的user_id',
  `verify_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核时间戳',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核完状态：0->对应心愿池中，1->资助人锁定待线下协商中，2->资助人协商完成进入资助周期了，5->团体锁定待协商中，6->团体协商完成进入资助周期了，7->团体选取投票中，10->资助完成',
  `locking_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '锁定的对象id：资助人id/团体id',
  `vision` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '版本号（乐观锁）'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`,`number`,`email`) USING BTREE;

--
-- Indexes for table `user_team`
--
ALTER TABLE `user_team`
  ADD PRIMARY KEY (`user_id`,`team_id`) USING BTREE;

--
-- Indexes for table `user_vote`
--
ALTER TABLE `user_vote`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `vote`
--
ALTER TABLE `vote`
  ADD PRIMARY KEY (`vote_id`,`team_id`) USING BTREE;

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
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `flows`
--
ALTER TABLE `flows`
  MODIFY `flows_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '流水主键ID';
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
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '站内信主键ID';
--
-- 使用表AUTO_INCREMENT `team`
--
ALTER TABLE `team`
  MODIFY `team_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '团体主键ID', AUTO_INCREMENT=514000015;
--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=15;
--
-- 使用表AUTO_INCREMENT `vote`
--
ALTER TABLE `vote`
  MODIFY `vote_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '一次投票活动的主键ID';
--
-- 使用表AUTO_INCREMENT `vote_res`
--
ALTER TABLE `vote_res`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '投票任何时刻结果主键ID';
--
-- 使用表AUTO_INCREMENT `wish`
--
ALTER TABLE `wish`
  MODIFY `wish_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '心愿主键ID';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
