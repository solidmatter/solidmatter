<?php

global $_QUERIES;

// repository administration ---------------------------------------------------

$_QUERIES['sbCR/workspace/create'] = "

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE TABLE IF NOT EXISTS `{TABLE_AUTHCACHE}` (
  `fk_subject` char(32) NOT NULL,
  `fk_entity` char(32) NOT NULL,
  `e_authtype` enum('AGGREGATED','EFFECTIVE') NOT NULL,
  `fk_authorisation` varchar(30) NOT NULL,
  `e_granttype` enum('ALLOW','DENY') NOT NULL,
  PRIMARY KEY (`fk_subject`,`fk_entity`,`e_authtype`,`fk_authorisation`),
  KEY `sb_system_cache_authorisations_fk_en` (`fk_entity`),
  CONSTRAINT `{TABLE_AUTHCACHE}_fk_en` FOREIGN KEY (`fk_entity`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_AUTHCACHE}_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_IMAGECACHE}` (
  `fk_image` char(32) NOT NULL,
  `fk_filterstack` char(32) NOT NULL,
  `e_mode` enum('full','explorer','custom') NOT NULL,
  `m_content` longblob,
  PRIMARY KEY (`fk_image`,`fk_filterstack`,`e_mode`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_EVENTLOG}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_module` varchar(50) CHARACTER SET ascii NOT NULL,
  `s_loguid` varchar(100) NOT NULL,
  `fk_subject` char(32) CHARACTER SET ascii DEFAULT NULL,
  `t_log` text NOT NULL,
  `fk_user` char(32) CHARACTER SET ascii DEFAULT '0',
  `e_type` enum('MAINTENANCE','INFO','ERROR','DEBUG','SECURITY','WARNING') NOT NULL DEFAULT 'INFO',
  `dt_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15803 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_NODES}` (
  `uuid` char(32) CHARACTER SET ascii NOT NULL,
  `s_uid` varchar(30) CHARACTER SET ascii DEFAULT NULL,
  `fk_nodetype` varchar(40) NOT NULL DEFAULT 'node',
  `s_label` varchar(200) NOT NULL,
  `s_name` varchar(100) NOT NULL,
  `b_inheritrights` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `b_bequeathlocalrights` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `b_bequeathrights` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `fk_createdby` char(32) CHARACTER SET ascii DEFAULT NULL,
  `fk_modifiedby` char(32) CHARACTER SET ascii DEFAULT NULL,
  `dt_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dt_modified` datetime DEFAULT NULL,
  `s_currentlifecyclestate` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `sb_sn_nodetype` (`fk_nodetype`),
  KEY `sb_sn_createdby` (`fk_createdby`),
  KEY `sb_sn_modifiedby` (`fk_modifiedby`),
  KEY `sb_sn_label` (`s_label`),
  KEY `sb_sn_created` (`dt_created`),
  KEY `sb_sn_modified` (`dt_modified`),
  CONSTRAINT `{TABLE_NODES}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_AUTH}` (
  `fk_subject` char(32) NOT NULL,
  `fk_authorisation` varchar(30) NOT NULL,
  `fk_userentity` char(32) NOT NULL DEFAULT '0',
  `e_granttype` enum('ALLOW','DENY') NOT NULL DEFAULT 'ALLOW',
  PRIMARY KEY (`fk_subject`,`fk_authorisation`,`fk_userentity`),
  KEY `fk_entity` (`fk_userentity`),
  CONSTRAINT `{TABLE_AUTH}_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_AUTH}_fk_un` FOREIGN KEY (`fk_userentity`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_LOCKS}` (
  `fk_lockednode` char(32) NOT NULL,
  `fk_user` char(32) NOT NULL,
  `s_sessionid` char(32) NOT NULL,
  `b_deep` enum('TRUE','FALSE') DEFAULT 'FALSE',
  `dt_placed` datetime DEFAULT '0000-00-00 00:00:00',
  `n_timetolive` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_lockednode`),
  KEY `sb_snl_user` (`fk_user`),
  CONSTRAINT `{TABLE_LOCKS}_fk_n` FOREIGN KEY (`fk_lockednode`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_LOCKS}_fk_u` FOREIGN KEY (`fk_user`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_HIERARCHY}` (
  `fk_parent` char(32) NOT NULL,
  `fk_child` char(32) NOT NULL,
  `b_primary` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `n_order` mediumint(10) unsigned DEFAULT NULL,
  `n_level` smallint(10) unsigned DEFAULT NULL,
  `s_mpath` varchar(255) DEFAULT NULL,
  `fk_deletedby` char(32) DEFAULT NULL,
  `dt_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`fk_parent`,`fk_child`),
  KEY `fk_child` (`fk_child`,`b_primary`),
  CONSTRAINT `{TABLE_HIERARCHY}_fk_cn` FOREIGN KEY (`fk_child`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_HIERARCHY}_fk_pn` FOREIGN KEY (`fk_parent`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_PROPERTIES}` (
  `fk_node` char(32) NOT NULL DEFAULT '0',
  `fk_attributename` varchar(100) NOT NULL,
  `fk_version` char(32) NOT NULL DEFAULT '',
  `m_content` longblob,
  PRIMARY KEY (`fk_node`,`fk_attributename`,`fk_version`),
  KEY `sb_snpr_node` (`fk_node`),
  CONSTRAINT `{TABLE_PROPERTIES}_fk_n` FOREIGN KEY (`fk_node`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_BINPROPERTIES}` (
  `fk_node` char(32) NOT NULL DEFAULT '0',
  `fk_attributename` varchar(100) NOT NULL,
  `fk_version` char(255) NOT NULL DEFAULT '',
  `m_content` longblob,
  PRIMARY KEY (`fk_node`,`fk_attributename`,`fk_version`),
  KEY `fk_node` (`fk_node`),
  CONSTRAINT `{TABLE_BINPROPERTIES}_fk_n` FOREIGN KEY (`fk_node`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_RELATIONS}` (
  `fk_entity1` char(32) CHARACTER SET ascii NOT NULL,
  `s_relation` varchar(50) CHARACTER SET ascii NOT NULL,
  `fk_entity2` char(32) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`fk_entity1`,`s_relation`,`fk_entity2`),
  KEY `sb_srel_target` (`fk_entity2`),
  CONSTRAINT `{TABLE_RELATIONS}_fk_sn` FOREIGN KEY (`fk_entity1`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_RELATIONS}_fk_tn` FOREIGN KEY (`fk_entity2`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE IF NOT EXISTS `{TABLE_NODETAGS}` (
  `fk_subject` char(32) NOT NULL,
  `fk_tag` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`fk_subject`,`fk_tag`),
  KEY `sb_snt_tag` (`fk_tag`),
  CONSTRAINT `{TABLE_NODETAGS}_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_NODETAGS}_fk_t` FOREIGN KEY (`fk_tag`) REFERENCES `{TABLE_TAGS}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_VOTES}` (
  `fk_subject` char(32) NOT NULL,
  `fk_user` char(32) NOT NULL,
  `n_vote` tinyint(4) NOT NULL,
  PRIMARY KEY (`fk_subject`,`fk_user`),
  KEY `sb_snv_user` (`fk_user`,`fk_subject`),
  CONSTRAINT `{TABLE_VOTES}_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES ``{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_VOTES}_fk_u` FOREIGN KEY (`fk_user`) REFERENCES ``{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_REGVALUES}` (
  `s_key` varchar(250) NOT NULL,
  `fk_user` char(32) NOT NULL,
  `s_value` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`s_key`,`fk_user`),
  CONSTRAINT `{TABLE_REGVALUES}_fk_rk` FOREIGN KEY (`s_key`) REFERENCES `{TABLE_REGISTRY}` (`s_key`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `{TABLE_TAGS}` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `s_tag` varchar(100) NOT NULL,
  `n_popularity` int(10) unsigned NOT NULL,
  `n_customweight` smallint(10) unsigned NOT NULL,
  `e_visibility` enum('VISIBLE','HIDDEN') NOT NULL DEFAULT 'VISIBLE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=414 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_USERS}` (
  `uuid` char(32) CHARACTER SET ascii NOT NULL,
  `s_password` varchar(200) NOT NULL,
  `s_email` varchar(100) CHARACTER SET ascii DEFAULT NULL,
  `s_activationkey` varchar(32) CHARACTER SET ascii DEFAULT NULL,
  `t_comment` text,
  `n_failedlogins` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `b_activated` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `b_stayloggedin` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `b_locked` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `b_emailsent` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `dt_activatedat` datetime DEFAULT NULL,
  `dt_currentlogin` datetime DEFAULT '0000-00-00 00:00:00',
  `dt_lastlogin` datetime DEFAULT '0000-00-00 00:00:00',
  `dt_failedlogin` datetime DEFAULT NULL,
  `n_totalfailedlogins` bigint(20) unsigned NOT NULL,
  `n_successfullogins` bigint(20) unsigned NOT NULL DEFAULT '0',
  `n_silentlogins` bigint(20) unsigned NOT NULL DEFAULT '0',
  `b_hidestatus` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `b_backendaccess` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `dt_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `id` (`uuid`),
  CONSTRAINT `{TABLE_USERS}_fk_n` FOREIGN KEY (`uuid`) REFERENCES `{TABLE_NODES}` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

";

$_QUERIES['sbCR/inactive'] = "

CREATE TABLE IF NOT EXISTS `sb_system_commands` (
  `fk_user` char(32) NOT NULL DEFAULT '',
  `fk_subject` char(32) NOT NULL DEFAULT '',
  `s_uid` varchar(50) NOT NULL DEFAULT '',
  `s_command` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`fk_user`,`fk_subject`,`s_uid`),
  KEY `sb_system_commands_fk_sn` (`fk_subject`),
  CONSTRAINT `sb_system_commands_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_commands_fk_un` FOREIGN KEY (`fk_user`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `sb_system_progress` (
  `fk_user` char(32) NOT NULL,
  `fk_subject` char(32) NOT NULL,
  `s_uid` varchar(30) NOT NULL,
  `s_status` varchar(250) DEFAULT NULL,
  `n_percentage` int(11) DEFAULT NULL,
  PRIMARY KEY (`fk_user`,`fk_subject`,`s_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS `sb_system_whosonline` (
  `s_sessionid` varchar(32) NOT NULL DEFAULT '',
  `fk_user` char(32) NOT NULL DEFAULT '0',
  `dt_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `b_anonymous` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `s_module` varchar(50) NOT NULL DEFAULT '',
  `s_action` varchar(50) NOT NULL DEFAULT '',
  `s_additionalinfo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`s_sessionid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

";



?>