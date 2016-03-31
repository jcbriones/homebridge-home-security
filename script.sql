--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(23) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `name` varchar(32) NOT NULL default '',
  `priviledge` enum('User','Admin') NOT NULL default 'User',
  `email` varchar(39) NOT NULL default '',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `login_count` mediumint(9) unsigned NOT NULL default '0',
  `last_ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`user_id`),
  KEY `name` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

--
-- Table structure for table `ip_ban`
--

CREATE TABLE IF NOT EXISTS `ip_ban` (
  `ip_address` varchar(15) NOT NULL default '',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `reason` varchar(255) NOT NULL default '',
  PRIMARY KEY (`ip_address`)
) ENGINE=MyISAM;

--
-- Table structure for table `accessories`
--

CREATE TABLE IF NOT EXISTS `accessories` (
  `acc_id` int(11) unsigned NOT NULL auto_increment,
  `type` int(10) unsigned NOT NULL default '0',
  `name` varchar(23) NOT NULL default '',
  `manufacturer` varchar(23) NOT NULL default '',
  `model` varchar(23) NOT NULL default '',
  `serial_no` varchar(23) NOT NULL default '',
  `comm_type` varchar(23) NOT NULL default '',
  `address` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`acc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `service_id` int(11) unsigned NOT NULL auto_increment,
  `acc_id` int(11) unsigned NOT NULL default '0',
  `name` varchar(23) NOT NULL default '',
  PRIMARY KEY  (`service_id`),
  KEY `name` (`acc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1; 

--
-- Table structure for table `characteristics`
--

CREATE TABLE IF NOT EXISTS `characteristics` (
  `char_id` int(11) unsigned NOT NULL auto_increment,
  `acc_id` int(11) unsigned NOT NULL default '0',
  `name` varchar(23) NOT NULL default '',
  `value` int(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`),
  KEY `name` (`acc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

--
-- Table structure for table `logs_user_login`
--

CREATE TABLE IF NOT EXISTS `logs_user_login` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL default '0',
  `username` varchar(23) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip_address` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `name` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1; 

--
-- Table structure for table `logs_user_activity`
--

CREATE TABLE IF NOT EXISTS `logs_user_activity` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL default '0',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `activity` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `name` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

--
-- Table structure for table `logs_user_activity`
--

CREATE TABLE IF NOT EXISTS `logs_system_activity` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `activity` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;