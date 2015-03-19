

DROP TABLE IF EXISTS `matinee_advert`;
CREATE TABLE IF NOT EXISTS `matinee_advert` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(11) DEFAULT NULL,
  `documentid` int(11) DEFAULT NULL,
  `imageid` int(11) DEFAULT NULL,
  `memberid` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `roleid` varchar(20) NOT NULL,
  `createddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expirydate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` varchar(1) DEFAULT NULL,
  `publisheddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cancelleddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reasonforcancellation` text,
  `url` varchar(140) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_advert: 0 rows
DELETE FROM `matinee_advert`;
/*!40000 ALTER TABLE `matinee_advert` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_advert` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_advertgroup
DROP TABLE IF EXISTS `matinee_advertgroup`;
CREATE TABLE IF NOT EXISTS `matinee_advertgroup` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_advertgroup: 0 rows
DELETE FROM `matinee_advertgroup`;
/*!40000 ALTER TABLE `matinee_advertgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_advertgroup` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_applicationactionroles
DROP TABLE IF EXISTS `matinee_applicationactionroles`;
CREATE TABLE IF NOT EXISTS `matinee_applicationactionroles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `actionid` int(11) DEFAULT NULL,
  `roleid` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=621 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_applicationactionroles: 102 rows
DELETE FROM `matinee_applicationactionroles`;
/*!40000 ALTER TABLE `matinee_applicationactionroles` DISABLE KEYS */;
INSERT INTO `matinee_applicationactionroles` (`id`, `actionid`, `roleid`) VALUES
	(614, 425, 'PUBLIC'),
	(613, 424, 'PUBLIC'),
	(612, 423, 'PUBLIC'),
	(611, 422, 'PUBLIC'),
	(610, 421, 'PUBLIC'),
	(609, 420, 'PUBLIC'),
	(608, 419, 'PUBLIC'),
	(607, 418, 'PUBLIC'),
	(606, 417, 'PUBLIC'),
	(605, 416, 'PUBLIC'),
	(604, 415, 'PUBLIC'),
	(603, 414, 'PUBLIC'),
	(602, 413, 'PUBLIC'),
	(601, 412, 'PUBLIC'),
	(600, 411, 'PUBLIC'),
	(599, 410, 'PUBLIC'),
	(598, 409, 'PUBLIC'),
	(597, 408, 'PUBLIC'),
	(596, 407, 'PUBLIC'),
	(595, 406, 'PUBLIC'),
	(594, 405, 'PUBLIC'),
	(593, 404, 'PUBLIC'),
	(592, 403, 'PUBLIC'),
	(591, 402, 'PUBLIC'),
	(590, 401, 'PUBLIC'),
	(589, 400, 'PUBLIC'),
	(588, 399, 'PUBLIC'),
	(587, 398, 'PUBLIC'),
	(586, 397, 'PUBLIC'),
	(585, 396, 'PUBLIC'),
	(584, 395, 'PUBLIC'),
	(583, 394, 'PUBLIC'),
	(582, 393, 'PUBLIC'),
	(581, 392, 'PUBLIC'),
	(580, 391, 'PUBLIC'),
	(579, 390, 'PUBLIC'),
	(578, 389, 'PUBLIC'),
	(577, 388, 'PUBLIC'),
	(576, 387, 'PUBLIC'),
	(575, 386, 'PUBLIC'),
	(574, 385, 'PUBLIC'),
	(573, 384, 'PUBLIC'),
	(572, 383, 'PUBLIC'),
	(571, 382, 'PUBLIC'),
	(570, 381, 'PUBLIC'),
	(569, 380, 'PUBLIC'),
	(568, 379, 'PUBLIC'),
	(567, 378, 'PUBLIC'),
	(566, 377, 'PUBLIC'),
	(565, 376, 'PUBLIC'),
	(564, 375, 'PUBLIC'),
	(563, 374, 'PUBLIC'),
	(562, 373, 'PUBLIC'),
	(561, 372, 'PUBLIC'),
	(560, 371, 'PUBLIC'),
	(559, 370, 'PUBLIC'),
	(558, 369, 'PUBLIC'),
	(557, 368, 'PUBLIC'),
	(556, 367, 'PUBLIC'),
	(555, 366, 'PUBLIC'),
	(554, 365, 'PUBLIC'),
	(553, 364, 'PUBLIC'),
	(552, 363, 'PUBLIC'),
	(551, 362, 'PUBLIC'),
	(550, 361, 'PUBLIC'),
	(549, 360, 'PUBLIC'),
	(548, 359, 'PUBLIC'),
	(547, 358, 'PUBLIC'),
	(546, 357, 'PUBLIC'),
	(545, 356, 'PUBLIC'),
	(544, 355, 'PUBLIC'),
	(543, 354, 'PUBLIC'),
	(542, 353, 'PUBLIC'),
	(541, 352, 'PUBLIC'),
	(540, 351, 'PUBLIC'),
	(539, 350, 'PUBLIC'),
	(538, 349, 'PUBLIC'),
	(537, 348, 'PUBLIC'),
	(536, 347, 'PUBLIC'),
	(535, 346, 'PUBLIC'),
	(534, 345, 'PUBLIC'),
	(533, 344, 'PUBLIC'),
	(532, 343, 'PUBLIC'),
	(531, 342, 'PUBLIC'),
	(530, 341, 'PUBLIC'),
	(529, 340, 'PUBLIC'),
	(528, 339, 'PUBLIC'),
	(527, 338, 'PUBLIC'),
	(526, 337, 'PUBLIC'),
	(525, 336, 'PUBLIC'),
	(524, 335, 'PUBLIC'),
	(523, 334, 'PUBLIC'),
	(522, 333, 'PUBLIC'),
	(521, 332, 'PUBLIC'),
	(520, 331, 'PUBLIC'),
	(519, 330, 'PUBLIC'),
	(615, 426, 'PUBLIC'),
	(616, 427, 'PUBLIC'),
	(617, 428, 'PUBLIC'),
	(618, 429, 'PUBLIC'),
	(619, 430, 'PUBLIC'),
	(620, 431, 'PUBLIC');
/*!40000 ALTER TABLE `matinee_applicationactionroles` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_applicationactions
DROP TABLE IF EXISTS `matinee_applicationactions`;
CREATE TABLE IF NOT EXISTS `matinee_applicationactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `description` varchar(40) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=432 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_applicationactions: 102 rows
DELETE FROM `matinee_applicationactions`;
/*!40000 ALTER TABLE `matinee_applicationactions` DISABLE KEYS */;
INSERT INTO `matinee_applicationactions` (`id`, `pageid`, `description`, `code`) VALUES
	(425, 7201, 'Leave', 'leaveGroup'),
	(424, 7201, 'Join', 'joinGroup'),
	(423, 7201, 'Remove item', 'RemoveItem'),
	(422, 7201, 'Edit item', 'EditItem'),
	(421, 7201, 'View item', 'ViewItem'),
	(420, 7201, 'Add item', 'AddItem'),
	(419, 7201, 'Filter', 'Filter'),
	(418, 7199, 'View item', 'ViewItem'),
	(417, 7199, 'Filter', 'Filter'),
	(416, 7197, 'View item', 'ViewItem'),
	(415, 7197, 'Filter', 'Filter'),
	(414, 7195, 'Remove item', 'RemoveItem'),
	(413, 7195, 'Edit item', 'EditItem'),
	(412, 7195, 'View item', 'ViewItem'),
	(411, 7195, 'Add item', 'AddItem'),
	(410, 7195, 'Filter', 'Filter'),
	(409, 7182, 'View item', 'ViewItem'),
	(408, 7182, 'Filter', 'Filter'),
	(407, 7181, 'View item', 'ViewItem'),
	(406, 7181, 'Filter', 'Filter'),
	(405, 7177, 'View item', 'ViewItem'),
	(404, 7177, 'Filter', 'Filter'),
	(403, 7176, 'Remove item', 'RemoveItem'),
	(402, 7176, 'Edit item', 'EditItem'),
	(401, 7176, 'View item', 'ViewItem'),
	(400, 7176, 'Filter', 'Filter'),
	(399, 7125, 'Edit item', 'EditItem'),
	(398, 7178, 'Reject', 'reject'),
	(397, 7178, 'Approve', 'approve'),
	(396, 7178, 'Users', 'userRoles'),
	(395, 7178, 'Remove item', 'RemoveItem'),
	(394, 7178, 'Edit item', 'EditItem'),
	(393, 7178, 'View item', 'ViewItem'),
	(392, 7178, 'Add item', 'AddItem'),
	(391, 7178, 'Filter', 'Filter'),
	(390, 11, 'User Roles', 'userRoles'),
	(389, 11, 'Remove item', 'RemoveItem'),
	(388, 11, 'Edit item', 'EditItem'),
	(387, 11, 'View item', 'ViewItem'),
	(386, 11, 'Add item', 'AddItem'),
	(385, 11, 'Filter', 'Filter'),
	(384, 7168, 'Remove item', 'RemoveItem'),
	(383, 7168, 'Edit item', 'EditItem'),
	(382, 7168, 'View item', 'ViewItem'),
	(381, 7168, 'Add item', 'AddItem'),
	(380, 7168, 'Filter', 'Filter'),
	(379, 7167, 'Remove item', 'RemoveItem'),
	(378, 7167, 'Edit item', 'EditItem'),
	(377, 7167, 'View item', 'ViewItem'),
	(376, 7167, 'Add item', 'AddItem'),
	(375, 7167, 'Filter', 'Filter'),
	(374, 7158, 'Users', 'userRoles'),
	(373, 7163, 'Remove item', 'RemoveItem'),
	(372, 7163, 'Edit item', 'EditItem'),
	(371, 7163, 'View item', 'ViewItem'),
	(370, 7163, 'Add item', 'AddItem'),
	(369, 7163, 'Filter', 'Filter'),
	(368, 7162, 'Remove item', 'RemoveItem'),
	(367, 7162, 'Edit item', 'EditItem'),
	(366, 7162, 'View item', 'ViewItem'),
	(365, 7162, 'Add item', 'AddItem'),
	(364, 7162, 'Filter', 'Filter'),
	(363, 7158, 'Remove item', 'RemoveItem'),
	(362, 7158, 'Edit item', 'EditItem'),
	(361, 7158, 'View item', 'ViewItem'),
	(360, 7158, 'Add item', 'AddItem'),
	(359, 7158, 'Filter', 'Filter'),
	(358, 7160, 'Edit item', 'EditItem'),
	(357, 7160, 'Add item', 'AddItem'),
	(356, 7160, 'Remove item', 'RemoveItem'),
	(355, 7160, 'View item', 'ViewItem'),
	(354, 7160, 'Filter', 'Filter'),
	(353, 7105, 'Move Down', 'sequenceDown'),
	(352, 7105, 'Move Up', 'sequenceUp'),
	(351, 7105, 'Content', 'editContent'),
	(350, 7105, 'Roles', 'pageRoles'),
	(349, 7105, 'Remove item', 'RemoveItem'),
	(348, 7105, 'Edit item', 'EditItem'),
	(347, 7105, 'View item', 'ViewItem'),
	(346, 7105, 'Add item', 'AddItem'),
	(345, 7105, 'Filter', 'Filter'),
	(344, 7125, 'Reply', 'reply'),
	(343, 7125, 'View', 'viewMessages'),
	(342, 7125, 'Remove item', 'RemoveItem'),
	(341, 7125, 'Add item', 'AddItem'),
	(340, 7125, 'Filter', 'Filter'),
	(339, 4, 'Remove item', 'RemoveItem'),
	(338, 4, 'View item', 'ViewItem'),
	(337, 4, 'Filter', 'Filter'),
	(336, 130, 'Live', 'live'),
	(335, 130, 'Expire', 'expire'),
	(334, 130, 'User Roles', 'userRoles'),
	(333, 130, 'Remove item', 'RemoveItem'),
	(332, 130, 'Edit item', 'EditItem'),
	(331, 130, 'View item', 'ViewItem'),
	(330, 130, 'Filter', 'Filter'),
	(426, 7176, 'Add item', 'AddItem'),
	(427, 7202, 'Filter', 'Filter'),
	(428, 7202, 'Add item', 'AddItem'),
	(429, 7202, 'View item', 'ViewItem'),
	(430, 7202, 'Edit item', 'EditItem'),
	(431, 7202, 'Remove item', 'RemoveItem');
/*!40000 ALTER TABLE `matinee_applicationactions` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_applicationtablecolumns
DROP TABLE IF EXISTS `matinee_applicationtablecolumns`;
CREATE TABLE IF NOT EXISTS `matinee_applicationtablecolumns` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `headerid` int(10) NOT NULL,
  `columnindex` int(10) NOT NULL,
  `width` int(10) NOT NULL,
  `hidecolumn` int(10) NOT NULL DEFAULT '0',
  `label` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `headerid_column` (`headerid`,`columnindex`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_applicationtablecolumns: 1 rows
DELETE FROM `matinee_applicationtablecolumns`;
/*!40000 ALTER TABLE `matinee_applicationtablecolumns` DISABLE KEYS */;
INSERT INTO `matinee_applicationtablecolumns` (`id`, `headerid`, `columnindex`, `width`, `hidecolumn`, `label`) VALUES
	(138, 43, 1, 124, 0, 'Created Date');
/*!40000 ALTER TABLE `matinee_applicationtablecolumns` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_applicationtables
DROP TABLE IF EXISTS `matinee_applicationtables`;
CREATE TABLE IF NOT EXISTS `matinee_applicationtables` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pageid` int(10) NOT NULL,
  `memberid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_applicationtables: 1 rows
DELETE FROM `matinee_applicationtables`;
/*!40000 ALTER TABLE `matinee_applicationtables` DISABLE KEYS */;
INSERT INTO `matinee_applicationtables` (`id`, `pageid`, `memberid`) VALUES
	(43, 4, 1);
/*!40000 ALTER TABLE `matinee_applicationtables` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_auditlog
DROP TABLE IF EXISTS `matinee_auditlog`;
CREATE TABLE IF NOT EXISTS `matinee_auditlog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `createdby` int(10) DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `originalmemberid` int(10) DEFAULT NULL,
  `originalstudioid` int(10) DEFAULT NULL,
  `originalstartdate` datetime DEFAULT NULL,
  `originalenddate` datetime DEFAULT NULL,
  `modifiedmemberid` int(11) DEFAULT NULL,
  `modifiedstudioid` int(11) DEFAULT NULL,
  `modifiedstartdate` datetime DEFAULT NULL,
  `modifiedenddate` datetime DEFAULT NULL,
  `mode` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=367 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_auditlog: 354 rows
DELETE FROM `matinee_auditlog`;
/*!40000 ALTER TABLE `matinee_auditlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_auditlog` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_booking
DROP TABLE IF EXISTS `matinee_booking`;
CREATE TABLE IF NOT EXISTS `matinee_booking` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `createdby` int(10) NOT NULL,
  `createddate` datetime NOT NULL,
  `bookingstart` datetime NOT NULL,
  `bookingend` datetime NOT NULL,
  `summary` varchar(100) NOT NULL,
  `unclink` varchar(100) NOT NULL,
  `allday` varchar(1) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `memberid` (`createdby`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Dumping data for table matinee.matinee_booking: 0 rows
DELETE FROM `matinee_booking`;
/*!40000 ALTER TABLE `matinee_booking` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_booking` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_documents
DROP TABLE IF EXISTS `matinee_documents`;
CREATE TABLE IF NOT EXISTS `matinee_documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `mimetype` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `image` longblob,
  `createdby` int(11) DEFAULT NULL,
  `createddate` timestamp NULL DEFAULT NULL,
  `lastmodifiedby` int(11) DEFAULT NULL,
  `lastmodifieddate` timestamp NULL DEFAULT NULL,
  `sessionid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_documents: 0 rows
DELETE FROM `matinee_documents`;
/*!40000 ALTER TABLE `matinee_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_documents` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_engineercalendar
DROP TABLE IF EXISTS `matinee_engineercalendar`;
CREATE TABLE IF NOT EXISTS `matinee_engineercalendar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `memberid` int(10) NOT NULL,
  `studioid` int(10) NOT NULL,
  `bookingid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `memberid` (`memberid`),
  KEY `studioid` (`studioid`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_engineercalendar: 0 rows
DELETE FROM `matinee_engineercalendar`;
/*!40000 ALTER TABLE `matinee_engineercalendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_engineercalendar` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_errors
DROP TABLE IF EXISTS `matinee_errors`;
CREATE TABLE IF NOT EXISTS `matinee_errors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) DEFAULT NULL,
  `memberid` int(11) DEFAULT NULL,
  `description` text,
  `createddate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20985 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_errors: 0 rows
DELETE FROM `matinee_errors`;
/*!40000 ALTER TABLE `matinee_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_errors` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_filter
DROP TABLE IF EXISTS `matinee_filter`;
CREATE TABLE IF NOT EXISTS `matinee_filter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `memberid` int(11) NOT NULL,
  `pageid` int(11) NOT NULL,
  `description` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_filter: 0 rows
DELETE FROM `matinee_filter`;
/*!40000 ALTER TABLE `matinee_filter` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_filter` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_filterdata
DROP TABLE IF EXISTS `matinee_filterdata`;
CREATE TABLE IF NOT EXISTS `matinee_filterdata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filterid` int(11) NOT NULL,
  `columnname` varchar(60) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_filterdata: 0 rows
DELETE FROM `matinee_filterdata`;
/*!40000 ALTER TABLE `matinee_filterdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_filterdata` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_images
DROP TABLE IF EXISTS `matinee_images`;
CREATE TABLE IF NOT EXISTS `matinee_images` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `path` char(255) DEFAULT '',
  `mimetype` char(50) DEFAULT '',
  `name` char(255) DEFAULT '',
  `imgwidth` smallint(4) DEFAULT '0',
  `imgheight` smallint(4) DEFAULT '0',
  `tag` char(255) DEFAULT '',
  `description` char(255) DEFAULT '',
  `fullpath` char(255) DEFAULT '',
  `image` longblob,
  `createddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ID` (`id`),
  FULLTEXT KEY `search_index` (`name`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=146 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_images: 0 rows
DELETE FROM `matinee_images`;
/*!40000 ALTER TABLE `matinee_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_images` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_loginaudit
DROP TABLE IF EXISTS `matinee_loginaudit`;
CREATE TABLE IF NOT EXISTS `matinee_loginaudit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memberid` int(10) unsigned NOT NULL,
  `timeon` datetime DEFAULT NULL,
  `timeoff` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=550 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_loginaudit: 0 rows
DELETE FROM `matinee_loginaudit`;
/*!40000 ALTER TABLE `matinee_loginaudit` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_loginaudit` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_members
DROP TABLE IF EXISTS `matinee_members`;
CREATE TABLE IF NOT EXISTS `matinee_members` (
  `member_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `login` varchar(100) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(60) DEFAULT NULL,
  `title` varchar(60) DEFAULT NULL,
  `companyid` int(60) DEFAULT NULL,
  `schoolid` int(60) NOT NULL,
  `imageid` int(11) DEFAULT NULL,
  `description` text,
  `lastaccessdate` datetime DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `postcode` varchar(8) DEFAULT NULL,
  `systemuser` varchar(1) DEFAULT NULL,
  `accepted` varchar(1) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `guid` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `dateofbirth` datetime NOT NULL,
  `notes` text NOT NULL,
  `loginauditid` int(11) NOT NULL,
  `postcode_lat` float NOT NULL,
  `postcode_lng` float NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=232 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_members: 12 rows
DELETE FROM `matinee_members`;
/*!40000 ALTER TABLE `matinee_members` DISABLE KEYS */;
INSERT INTO `matinee_members` (`member_id`, `firstname`, `lastname`, `login`, `passwd`, `email`, `title`, `companyid`, `schoolid`, `imageid`, `description`, `lastaccessdate`, `status`, `postcode`, `systemuser`, `accepted`, `website`, `guid`, `mobile`, `dateofbirth`, `notes`, `loginauditid`, `postcode_lat`, `postcode_lng`) VALUES
	(225, 'Kevin', 'Hiltoni', 'kevin.hilton@prodigyworks.co.uk', 'fbd4f454dd34b5884db9970059910561', 'kevin.hilton1@prodigyworks.co.uk', NULL, 0, 0, 0, NULL, '2013-05-06 14:16:21', 'Y', '', NULL, 'Y', NULL, '5187ab7c93f9e', '232222', '0000-00-00 00:00:00', '', 384, 0, 0),
	(220, 'Kevin', 'Hilton', 'kevin.hilton', '6b2d6d9a723e1aeb577a0925dd88bf34', 'kevin.hilto1n@prodigyworks.co.uk', NULL, 1, 1, 140, NULL, '2013-05-25 19:06:15', 'Y', '', NULL, 'Y', NULL, '51754c861ed6d', '23', '0000-00-00 00:00:00', '', 493, 0, 0),
	(1, 'System', 'Manager', 'admin', '6b2d6d9a723e1aeb577a0925dd88bf34', 'kevin.hilton@prodigyworks.co.uk', 'Mr', 1, 0, 0, NULL, '2013-07-30 14:12:16', 'Y', 'DE75 7YX', NULL, 'Y', NULL, '502d388d61d5b', '0111 222222', '0000-00-00 00:00:00', '', 549, 0, 0),
	(216, 'Test', 'User 11', 'test.user11', '6b2d6d9a723e1aeb577a0925dd88bf34', 'kevin_hilton69@hotmail.com', 'Mr', 1, 1, 0, '', '2013-04-17 14:50:31', 'Y', 'DE75 7YX', NULL, 'Y', NULL, '516c058435f1b', '20392032', '0000-00-00 00:00:00', '', 215, 0, 0),
	(221, 'Info', 'Messanger', 'info@prodigyworks.co.uk', '6b2d6d9a723e1aeb577a0925dd88bf34', 'info@prodigyworks.co.uk', NULL, 7, 0, 110, NULL, '2013-04-30 13:39:59', 'Y', '', NULL, 'Y', NULL, '517fbafb7be65', '2232', '0000-00-00 00:00:00', '', 313, 0, 0),
	(224, 'Kevin', 'GA', 'face.book@prodigyworks.co.uk', '6b2d6d9a723e1aeb577a0925dd88bf34', 'face.book@prodigyworks.co.uk', NULL, 0, 2, 0, NULL, '2013-05-06 14:20:25', 'Y', '', NULL, 'Y', NULL, '5186df399b550', '223232', '0000-00-00 00:00:00', '', 385, 0, 0),
	(226, 'Sandra', 'Hilton', 'sandra.hilton@prodigyworks.co.uk', '6b2d6d9a723e1aeb577a0925dd88bf34', 'sandra.hilton@prodigyworks.co.uk', NULL, 0, 2, 135, NULL, '2013-05-06 16:45:31', 'Y', '', NULL, 'Y', NULL, '5187ba8c43f5f', '01773 9293822', '0000-00-00 00:00:00', '', 392, 0, 0),
	(227, 'Some', 'Body', 'some.body', '6b2d6d9a723e1aeb577a0925dd88bf34', 'sandrahilton@hotmail.co.uk', NULL, 10, 0, 136, NULL, NULL, 'Y', '', NULL, 'Y', NULL, '5187f4510e788', '32', '0000-00-00 00:00:00', '', 0, 0, 0),
	(228, 'ss', 'ss', 'ss', '4664fc85141f48d1c4d516fc0981e62a', 'ss', NULL, 5, 0, 0, NULL, NULL, 'Y', '', NULL, 'Y', NULL, '518a19df84b26', '2', '0000-00-00 00:00:00', '', 0, 0, 0),
	(229, 'Kevin', 'Kevin', 'alans1', '6b2d6d9a723e1aeb577a0925dd88bf34', 'kevin.hilton@prodigyworks.co.uk', NULL, 0, 0, 0, NULL, NULL, 'Y', 'dd', NULL, 'Y', NULL, '51c1a7560d7e3', '2', '0000-00-00 00:00:00', '', 0, 0, 0),
	(230, 'Member', 'Test1', 'member1', '6b2d6d9a723e1aeb577a0925dd88bf34', 'info@prodigyworks.co.uk', NULL, NULL, 0, 0, NULL, '2013-06-19 13:50:02', 'Y', NULL, NULL, 'Y', NULL, '51c1a8b15b2ef', NULL, '0000-00-00 00:00:00', '', 536, 0, 0),
	(231, 'Testing', 'User', 'test1', 'fbd4f454dd34b5884db9970059910561', 'test@dd.com', NULL, NULL, 0, 0, NULL, NULL, 'Y', NULL, NULL, 'Y', NULL, '51d57fe6c53a4', NULL, '0000-00-00 00:00:00', '', 0, 0, 0);
/*!40000 ALTER TABLE `matinee_members` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_membersession
DROP TABLE IF EXISTS `matinee_membersession`;
CREATE TABLE IF NOT EXISTS `matinee_membersession` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `memberid` int(10) NOT NULL,
  `calendarid` varchar(40) NOT NULL,
  `checked` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `memberid_calendarid` (`memberid`,`calendarid`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- Dumping data for table matinee.matinee_membersession: 0 rows
DELETE FROM `matinee_membersession`;
/*!40000 ALTER TABLE `matinee_membersession` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_membersession` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_messages
DROP TABLE IF EXISTS `matinee_messages`;
CREATE TABLE IF NOT EXISTS `matinee_messages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `from_member_id` int(11) DEFAULT NULL,
  `to_member_id` int(11) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `message` text,
  `status` varchar(1) DEFAULT NULL,
  `action` text,
  `createddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `to_member_id` (`to_member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1361 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_messages: 0 rows
DELETE FROM `matinee_messages`;
/*!40000 ALTER TABLE `matinee_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `matinee_messages` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_pagenavigation
DROP TABLE IF EXISTS `matinee_pagenavigation`;
CREATE TABLE IF NOT EXISTS `matinee_pagenavigation` (
  `pagenavigationid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL,
  `childpageid` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `pagetype` varchar(1) DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `divider` int(11) DEFAULT NULL,
  `target` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pagenavigationid`),
  UNIQUE KEY `ix_pagenav` (`pageid`,`childpageid`,`sequence`)
) ENGINE=MyISAM AUTO_INCREMENT=153 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_pagenavigation: 53 rows
DELETE FROM `matinee_pagenavigation`;
/*!40000 ALTER TABLE `matinee_pagenavigation` DISABLE KEYS */;
INSERT INTO `matinee_pagenavigation` (`pagenavigationid`, `pageid`, `childpageid`, `sequence`, `pagetype`, `title`, `divider`, `target`) VALUES
	(1, 1, 1, 1, 'P', NULL, NULL, NULL),
	(2, 1, 8, 2, 'M', 'Administration', NULL, NULL),
	(3, 1, 11, 500, 'M', NULL, NULL, NULL),
	(118, 1, 7167, 2700, 'M', NULL, NULL, NULL),
	(9, 1, 1271, 500, 'M', NULL, NULL, NULL),
	(151, 7195, 7201, 300, 'M', '', NULL, NULL),
	(119, 1, 7168, 2500, 'M', NULL, NULL, NULL),
	(19, 1, 3000, 11, 'P', NULL, NULL, NULL),
	(150, 7196, 7200, 200, 'M', '', NULL, NULL),
	(149, 7196, 7199, 100, 'M', '', NULL, NULL),
	(22, 1, 130, 2000, 'M', 'Data Management', NULL, NULL),
	(148, 7195, 7198, 200, 'M', '', NULL, NULL),
	(147, 7195, 7197, 100, 'M', '', NULL, NULL),
	(132, 7180, 7182, 200, 'M', '', NULL, NULL),
	(133, 3000, 7183, 1300, 'M', '', NULL, NULL),
	(134, 3000, 7184, 400, 'M', '', NULL, NULL),
	(135, 7174, 7185, 300, 'M', 'Graphs', NULL, NULL),
	(136, 7174, 7186, 500, 'M', '', NULL, NULL),
	(137, 7174, 7187, 400, 'M', '', NULL, NULL),
	(138, 7174, 7188, 600, 'M', '', NULL, NULL),
	(139, 3000, 7189, 800, 'M', '', NULL, NULL),
	(140, 3000, 7190, 1400, 'L', '', NULL, NULL),
	(141, 1, 7191, 3400, 'L', '', NULL, NULL),
	(142, 1, 7192, 3500, 'L', '', NULL, NULL),
	(143, 1, 7193, 200, 'M', '', NULL, NULL),
	(144, 1, 7194, 3600, 'L', '', NULL, NULL),
	(145, 1, 7195, 3700, 'P', '', NULL, NULL),
	(146, 1, 7196, 3800, 'P', '', NULL, NULL),
	(61, 1, 4, 600, 'M', NULL, NULL, NULL),
	(62, 1, 7105, 2100, 'M', NULL, NULL, NULL),
	(63, 1, 7107, 321, 'H', NULL, NULL, NULL),
	(131, 7180, 7181, 100, 'M', 'Members', NULL, NULL),
	(130, 1, 7180, 3300, 'P', 'Members', NULL, NULL),
	(129, 1, 7179, 3100, 'L', '', NULL, NULL),
	(128, 3000, 7178, 1200, 'M', '', NULL, NULL),
	(127, 7174, 7177, 200, 'M', '', NULL, NULL),
	(110, 3000, 7159, 500, 'M', '', NULL, NULL),
	(111, 3000, 7160, 200, 'M', '', NULL, NULL),
	(112, 3000, 7161, 600, 'L', NULL, NULL, NULL),
	(113, 1, 7162, 2200, 'M', NULL, NULL, NULL),
	(114, 1, 7163, 2300, 'M', NULL, NULL, NULL),
	(115, 1, 7164, 2400, 'L', NULL, NULL, NULL),
	(116, 3000, 7165, 300, 'M', 'Projects', NULL, NULL),
	(117, 3000, 7166, 700, 'L', NULL, NULL, NULL),
	(109, 3000, 7158, 100, 'M', 'Data Management', NULL, NULL),
	(125, 1, 7174, 3200, 'P', NULL, NULL, NULL),
	(126, 7174, 7176, 100, 'M', 'Reports', NULL, NULL),
	(124, 1, 7173, 3000, 'P', NULL, NULL, NULL),
	(123, 1, 7172, 2600, 'P', NULL, NULL, NULL),
	(122, 3000, 7171, 1100, 'L', NULL, NULL, NULL),
	(121, 3000, 7170, 1000, 'L', NULL, NULL, NULL),
	(120, 3000, 7169, 900, 'M', NULL, NULL, NULL),
	(152, 7174, 7202, 700, 'M', NULL, NULL, NULL);
/*!40000 ALTER TABLE `matinee_pagenavigation` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_pageroles
DROP TABLE IF EXISTS `matinee_pageroles`;
CREATE TABLE IF NOT EXISTS `matinee_pageroles` (
  `pageroleid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(11) NOT NULL,
  `roleid` varchar(20) NOT NULL,
  PRIMARY KEY (`pageroleid`),
  UNIQUE KEY `ix_pageroles` (`pageid`,`roleid`)
) ENGINE=MyISAM AUTO_INCREMENT=1111 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_pageroles: 75 rows
DELETE FROM `matinee_pageroles`;
/*!40000 ALTER TABLE `matinee_pageroles` DISABLE KEYS */;
INSERT INTO `matinee_pageroles` (`pageroleid`, `pageid`, `roleid`) VALUES
	(3, 3, 'ADMIN'),
	(665, 11, 'ADMIN'),
	(9, 13, 'ADMIN'),
	(667, 100, 'ADMIN'),
	(1095, 7183, 'USER'),
	(1082, 7182, 'PUBLIC'),
	(21, 1509, 'USER'),
	(1080, 7180, 'PUBLIC'),
	(174, 7106, 'ADMIN'),
	(161, 2000, 'PUBLIC'),
	(160, 14, 'PUBLIC'),
	(159, 10, 'PUBLIC'),
	(1063, 7172, 'PUBLIC'),
	(157, 6, 'PUBLIC'),
	(156, 5, 'PUBLIC'),
	(155, 2, 'PUBLIC'),
	(1079, 7179, 'PUBLIC'),
	(1064, 7173, 'PUBLIC'),
	(1062, 7171, 'PUBLIC'),
	(1061, 7170, 'PUBLIC'),
	(663, 8, 'ADMIN'),
	(1070, 7167, 'ADMIN'),
	(1071, 7168, 'ADMIN'),
	(1073, 7169, 'INSTRUCTOR'),
	(1076, 7158, 'INSTRUCTOR'),
	(1072, 7169, 'ADMIN'),
	(1109, 7174, 'ADMIN'),
	(661, 8, 'PUBLIC'),
	(1110, 7174, 'PM'),
	(1067, 7175, 'PUBLIC'),
	(1078, 7178, 'ADMIN'),
	(1068, 7176, 'PUBLIC'),
	(1069, 7177, 'PUBLIC'),
	(963, 4, 'ADMIN'),
	(654, 7107, 'ADMIN'),
	(653, 1, 'PUBLIC'),
	(651, 1, 'ADMIN'),
	(669, 100, 'USER'),
	(1084, 7184, 'PUBLIC'),
	(670, 130, 'ADMIN'),
	(676, 1271, 'ADMIN'),
	(678, 7105, 'ADMIN'),
	(656, 7107, 'PUBLIC'),
	(1081, 7181, 'PUBLIC'),
	(1074, 7160, 'ADMIN'),
	(1050, 7161, 'PUBLIC'),
	(1053, 7162, 'ADMIN'),
	(1054, 7163, 'ADMIN'),
	(1055, 7164, 'PUBLIC'),
	(1097, 7165, 'USER'),
	(1057, 7166, 'PUBLIC'),
	(1085, 7185, 'PUBLIC'),
	(982, 7125, 'PUBLIC'),
	(1107, 7201, 'PUBLIC'),
	(1106, 7200, 'PUBLIC'),
	(1105, 7199, 'PUBLIC'),
	(1104, 7198, 'PUBLIC'),
	(1029, 7143, 'ADMIN'),
	(1103, 7197, 'PUBLIC'),
	(1102, 7196, 'SCHOOL'),
	(1101, 7195, 'COMPANY'),
	(1033, 7147, 'PUBLIC'),
	(1094, 7194, 'PUBLIC'),
	(1096, 7193, 'USER'),
	(1092, 7192, 'PUBLIC'),
	(1091, 7191, 'PUBLIC'),
	(1090, 7190, 'PUBLIC'),
	(1089, 7189, 'PUBLIC'),
	(1088, 7188, 'PUBLIC'),
	(1087, 7187, 'PUBLIC'),
	(1086, 7186, 'PUBLIC'),
	(1048, 7159, 'PUBLIC'),
	(1075, 7158, 'ADMIN'),
	(1046, 3000, 'PUBLIC'),
	(1108, 7202, 'PUBLIC');
/*!40000 ALTER TABLE `matinee_pageroles` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_pages
DROP TABLE IF EXISTS `matinee_pages`;
CREATE TABLE IF NOT EXISTS `matinee_pages` (
  `pageid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pagename` varchar(50) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`pageid`),
  UNIQUE KEY `ix_page` (`pagename`)
) ENGINE=MyISAM AUTO_INCREMENT=7203 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_pages: 40 rows
DELETE FROM `matinee_pages`;
/*!40000 ALTER TABLE `matinee_pages` DISABLE KEYS */;
INSERT INTO `matinee_pages` (`pageid`, `pagename`, `label`, `type`, `content`) VALUES
	(1, 'index.php', 'Home', 'P', NULL),
	(2, 'system-access-denied.php', 'Access Denied', 'P', NULL),
	(3, 'system-admin.php', 'Admin', 'P', NULL),
	(5, 'system-login-timeout.php', 'Session Timeout', 'P', NULL),
	(6, 'system-login-failed.php', 'Login Failed', 'P', NULL),
	(8, 'system-register.php', 'Register', 'P', NULL),
	(10, 'system-register-success.php', 'Register Success', 'P', NULL),
	(11, 'system-admin-roles.php', 'Roles', 'P', NULL),
	(13, 'system-register-exec.php', 'Register Save', 'P', NULL),
	(14, 'system-imageviewer.php', 'Image Viewer', 'P', NULL),
	(100, 'profile.php', 'Edit Profile', 'P', NULL),
	(130, 'users.php', 'Manage Users', 'P', NULL),
	(2000, 'system-login.php', 'Account log in', 'P', NULL),
	(1271, 'siteconfig.php', 'Site Configuration', 'P', NULL),
	(1508, 'passwordchanged.php', 'Changed Password', 'P', NULL),
	(1509, 'system-register-amend.php', 'User Amendment', 'P', NULL),
	(7105, 'managepages.php', 'Manage Pages', 'P', NULL),
	(3010, 'documents.php', 'Documents', 'P', NULL),
	(7106, 'manageactions.php', 'Manage Actions', 'P', NULL),
	(4, 'manageerrors.php', 'System Errors', 'P', NULL),
	(7107, 'welcome.php', 'Welcome', 'P', NULL),
	(7125, 'messages.php', 'Messages', 'P', NULL),
	(7143, 'managegrids.php', 'Manage Grids', 'P', NULL),
	(7147, 'runalerts.php', 'Alert Schedule', 'P', NULL),
	(7163, 'managevideostudios.php', 'Manage Video Studios', 'P', NULL),
	(7162, 'manageaudiostudios.php', 'Manage Audio Studios', 'P', NULL),
	(3000, 'test.php', 'Calendar Test', 'P', NULL),
	(7181, 'instructors.php', 'Instructors', 'P', NULL),
	(7182, 'students.php', 'Students', 'P', NULL),
	(7202, 'calendarevents.php', 'Calendar Events', 'P', NULL),
	(7191, 'accountconfirmed.php', 'Account Confirmed', 'P', NULL),
	(7192, 'activateaccount.php', 'Activate Account', 'P', NULL),
	(7194, 'system-register-activated.php', 'Account Activated', 'P', NULL),
	(7197, 'companymembers.php', 'Company Members', 'P', NULL),
	(7198, 'companysubgroups.php', 'Company Sub Groups', 'P', NULL),
	(7199, 'schoolmembers.php', 'School Members', 'P', NULL),
	(7200, 'schoolsubgroups.php', 'School Sub Groups', 'P', NULL),
	(7201, 'managecompanysubgroups.php', 'Manage Sub Groups', 'P', NULL),
	(7174, 'reports.php', 'Reports', 'P', NULL),
	(7176, 'auditlogs.php', 'Audit Logs', 'P', NULL);
/*!40000 ALTER TABLE `matinee_pages` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_roles
DROP TABLE IF EXISTS `matinee_roles`;
CREATE TABLE IF NOT EXISTS `matinee_roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `roleid` varchar(20) DEFAULT '',
  `systemrole` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_roles: 6 rows
DELETE FROM `matinee_roles`;
/*!40000 ALTER TABLE `matinee_roles` DISABLE KEYS */;
INSERT INTO `matinee_roles` (`id`, `roleid`, `systemrole`) VALUES
	(1, 'PUBLIC', 'Y'),
	(2, 'ADMIN', 'N'),
	(3, 'USER', 'Y'),
	(50, 'ADMINISTRATOR', 'N'),
	(49, 'PM', 'N'),
	(48, 'ENGINEER', 'N');
/*!40000 ALTER TABLE `matinee_roles` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_siteconfig
DROP TABLE IF EXISTS `matinee_siteconfig`;
CREATE TABLE IF NOT EXISTS `matinee_siteconfig` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `domainurl` varchar(60) DEFAULT NULL,
  `emailfooter` text,
  `lastschedulerun` date DEFAULT NULL,
  `runscheduledays` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_siteconfig: 1 rows
DELETE FROM `matinee_siteconfig`;
/*!40000 ALTER TABLE `matinee_siteconfig` DISABLE KEYS */;
INSERT INTO `matinee_siteconfig` (`id`, `domainurl`, `emailfooter`, `lastschedulerun`, `runscheduledays`) VALUES
	(1, 'http://localhost/matinee', '<p>Regards,<br />Kevin. Hilton</p>', '2013-07-30', 1);
/*!40000 ALTER TABLE `matinee_siteconfig` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_studio
DROP TABLE IF EXISTS `matinee_studio`;
CREATE TABLE IF NOT EXISTS `matinee_studio` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `type` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_type` (`name`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Dumping data for table matinee.matinee_studio: 7 rows
DELETE FROM `matinee_studio`;
/*!40000 ALTER TABLE `matinee_studio` DISABLE KEYS */;
INSERT INTO `matinee_studio` (`id`, `name`, `type`) VALUES
	(1, 'Avid 1', 'V'),
	(2, 'Avid 2', 'V'),
	(3, 'Daisy', 'V'),
	(4, 'Glyn', 'V'),
	(5, 'Audio Studio 1', 'A'),
	(6, 'Audio Studio 2', 'A'),
	(7, 'Audio Studio 3', 'A');
/*!40000 ALTER TABLE `matinee_studio` ENABLE KEYS */;


-- Dumping structure for table matinee.matinee_userroles
DROP TABLE IF EXISTS `matinee_userroles`;
CREATE TABLE IF NOT EXISTS `matinee_userroles` (
  `userroleid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `roleid` varchar(20) DEFAULT NULL,
  `memberid` int(11) DEFAULT NULL,
  PRIMARY KEY (`userroleid`),
  UNIQUE KEY `ix_userroles` (`roleid`,`memberid`)
) ENGINE=MyISAM AUTO_INCREMENT=1648 DEFAULT CHARSET=latin1;

-- Dumping data for table matinee.matinee_userroles: 50 rows
DELETE FROM `matinee_userroles`;
/*!40000 ALTER TABLE `matinee_userroles` DISABLE KEYS */;
INSERT INTO `matinee_userroles` (`userroleid`, `roleid`, `memberid`) VALUES
	(1, 'PUBLIC', 1),
	(1623, 'USER', 228),
	(1624, 'INSTRUCTOR', 228),
	(1622, 'PUBLIC', 228),
	(1594, 'USER', 219),
	(1619, 'PUBLIC', 227),
	(1620, 'USER', 227),
	(1642, 'PUBLIC', 230),
	(1641, 'USER', 229),
	(1601, 'INSTRUCTOR', 216),
	(1602, 'INSTRUCTOR', 1),
	(1603, 'STUDENT', 216),
	(1604, 'PUBLIC', 221),
	(1605, 'USER', 221),
	(1606, 'INSTRUCTOR', 221),
	(1607, 'PUBLIC', 222),
	(1608, 'USER', 222),
	(1609, 'INSTRUCTOR', 222),
	(1610, 'PUBLIC', 223),
	(1611, 'USER', 223),
	(1612, 'INSTRUCTOR', 223),
	(1613, 'PUBLIC', 224),
	(1614, 'USER', 224),
	(1615, 'PUBLIC', 225),
	(1616, 'USER', 225),
	(1617, 'PUBLIC', 226),
	(1618, 'USER', 226),
	(345, 'ADMIN', 1),
	(1640, 'PUBLIC', 229),
	(1639, 'USER', 220),
	(1628, 'COMPANY', 1),
	(1592, 'PUBLIC', 216),
	(1593, 'PUBLIC', 219),
	(1638, 'PUBLIC', 220),
	(1621, 'INSTRUCTOR', 227),
	(1480, 'USER', 1),
	(1590, 'USER', 216),
	(1637, 'ENGINEER', 220),
	(1630, 'ENGINEER', 225),
	(1631, 'ENGINEER', 226),
	(1632, 'PM', 1),
	(1633, 'PM', 227),
	(1634, 'PM', 216),
	(1635, 'ADMINISTRATOR', 1),
	(1636, 'ADMINISTRATOR', 224),
	(1643, 'USER', 230),
	(1644, 'ENGINEER', 230),
	(1645, 'PUBLIC', 231),
	(1646, 'USER', 231),
	(1647, 'ENGINEER', 231);
/*!40000 ALTER TABLE `matinee_userroles` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
