<?php

die('file will be deleted');

global $_QUERIES;

// repository administration ---------------------------------------------------

$locale = '$locale';

$_QUERIES['sbCR/repository/createTables'] = "

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE TABLE IF NOT EXISTS `{TABLE_MODULES}` (
  `uuid` char(32) CHARACTER SET ascii NOT NULL,
  `s_name` varchar(30) NOT NULL,
  `n_mainversion` int(11) NOT NULL,
  `n_subversion` int(11) NOT NULL,
  `n_bugfixversion` int(11) NOT NULL,
  `s_versioninfo` varchar(20) DEFAULT NULL,
  `dt_installed` datetime NOT NULL,
  `dt_updated` datetime NOT NULL,
  `b_uninstallable` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  `b_active` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_NAMESPACES}` (
  `s_prefix` varchar(30) CHARACTER SET ascii NOT NULL,
  `s_uri` varchar(200) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`s_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_NODETYPES}` (
  `s_type` varchar(40) NOT NULL,
  `s_class` varchar(50) DEFAULT NULL,
  `s_classfile` varchar(100) DEFAULT NULL,
  `e_type` enum('PRIMARY','MIXIN','ABSTRACT') NOT NULL DEFAULT 'PRIMARY',
  PRIMARY KEY (`s_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_AUTHDEF}` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_authorisation` varchar(30) NOT NULL,
  `fk_parentauthorisation` varchar(30) DEFAULT NULL,
  `b_default` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'FALSE',
  `n_order` int(10) unsigned NOT NULL,
  `b_onlyfrontend` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`fk_nodetype`,`s_authorisation`),
  CONSTRAINT `{TABLE_AUTHDEF}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_NTHIERARCHY}` (
  `fk_parentnodetype` varchar(40) NOT NULL,
  `fk_childnodetype` varchar(40) NOT NULL,
  PRIMARY KEY (`fk_parentnodetype`,`fk_childnodetype`),
  -- KEY `fk_nti_2` (`fk_childnodetype`),
  CONSTRAINT `{TABLE_NTHIERARCHY}_fk_cnt` FOREIGN KEY (`fk_parentnodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_NTHIERARCHY}_fk_pnt` FOREIGN KEY (`fk_childnodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_LIFECYCLE}` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_state` varchar(50) NOT NULL,
  `s_statetransition` varchar(50) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_state`,`s_statetransition`),
  CONSTRAINT `{TABLE_LIFECYCLE}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_MIMETYPES}` (
  `s_mimetype` varchar(50) NOT NULL,
  `fk_nodetype` varchar(40) NOT NULL,
  PRIMARY KEY (`s_mimetype`),
  -- KEY `rep_ntmime1` (`fk_nodetype`),
  CONSTRAINT `{TABLE_MIMETYPES}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_MODES}` (
  `s_mode` varchar(30) NOT NULL,
  `fk_parentnodetype` varchar(40) NOT NULL,
  `fk_nodetype` varchar(40) NOT NULL,
  `b_display` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  `b_choosable` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  PRIMARY KEY (`fk_nodetype`,`fk_parentnodetype`,`s_mode`),
  -- KEY `parentnodetype` (`fk_parentnodetype`),
  -- KEY `mode_parentnodetype` (`s_mode`,`fk_parentnodetype`),
  CONSTRAINT `{TABLE_MODES}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_MODES}_fk_pnt` FOREIGN KEY (`fk_parentnodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_ONTOLOGY}` (
  `s_relation` varchar(50) CHARACTER SET ascii NOT NULL,
  `fk_sourcenodetype` varchar(40) CHARACTER SET utf8 NOT NULL,
  `fk_targetnodetype` varchar(40) CHARACTER SET utf8 NOT NULL,
  `s_reverserelation` varchar(50) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`s_relation`,`fk_sourcenodetype`,`fk_targetnodetype`),
  -- KEY `rep_nto_source` (`fk_sourcenodetype`),
  -- KEY `rep_nto_target` (`fk_targetnodetype`),
  CONSTRAINT `{TABLE_ONTOLOGY}_fk_snt` FOREIGN KEY (`fk_sourcenodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_ONTOLOGY}_fk_tnt` FOREIGN KEY (`fk_targetnodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE IF NOT EXISTS `{TABLE_PROPERTYDEFS}` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_attributename` varchar(50) NOT NULL,
  `e_type` enum('STRING','DATE','WEAKREFERENCE','BINARY','DOUBLE','LONG','BOOLEAN','URI','REFERENCE','NAME','DECIMAL') NOT NULL DEFAULT 'STRING',
  `s_internaltype` varchar(250) DEFAULT NULL,
  `b_showinproperties` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `s_labelpath` varchar(100) NOT NULL,
  `e_storagetype` enum('EXTENDED','AUXILIARY','PRIMARY','EXTERNAL') NOT NULL DEFAULT 'EXTERNAL',
  `s_auxname` varchar(50) DEFAULT NULL,
  `n_order` int(11) DEFAULT NULL,
  `b_protected` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `b_protectedoncreation` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `b_multiple` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `s_defaultvalues` varchar(255) DEFAULT NULL,
  `s_descriptionpath` varchar(250) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_attributename`),
  CONSTRAINT `{TABLE_PROPERTYDEFS}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_ACTIONS}` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_view` varchar(30) NOT NULL,
  `s_action` varchar(30) NOT NULL,
  `b_default` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `s_classfile` varchar(50) DEFAULT NULL,
  `s_class` varchar(50) DEFAULT NULL,
  `e_outputtype` enum('RENDERED','XML','STREAM','HEADERS','OTHER','DEBUG') NOT NULL DEFAULT 'RENDERED',
  `s_stylesheet` varchar(50) DEFAULT NULL,
  `s_mimetype` varchar(50) DEFAULT NULL,
  `b_uselocale` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `b_isrecallable` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`fk_nodetype`,`s_view`,`s_action`),
  CONSTRAINT `{TABLE_ACTIONS}_fk_ntv` FOREIGN KEY (`fk_nodetype`, `s_view`) REFERENCES `{TABLE_VIEWS}` (`fk_nodetype`, `s_view`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_VIEWAUTH}` (
  `fk_nodetype` varchar(40) NOT NULL,
  `fk_view` varchar(50) NOT NULL,
  `fk_action` varchar(50) NOT NULL,
  `fk_authorisation` varchar(30) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`fk_view`,`fk_action`),
  CONSTRAINT `{TABLE_VIEWAUTH}_fk_ntv` FOREIGN KEY (`fk_nodetype`, `fk_view`) REFERENCES `{TABLE_VIEWS}` (`fk_nodetype`, `s_view`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_VIEWS}` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_view` varchar(50) NOT NULL,
  `b_display` enum('TRUE','FALSE') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'FALSE',
  `s_labelpath` varchar(200) DEFAULT NULL,
  `s_classfile` varchar(100) NOT NULL,
  `s_class` varchar(50) NOT NULL,
  `n_order` int(11) DEFAULT NULL,
  `n_priority` int(11) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_view`),
  CONSTRAINT `{TABLE_VIEWS}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_REGISTRY}` (
  `s_key` varchar(250) NOT NULL,
  `e_type` enum('string','boolean','integer') NOT NULL DEFAULT 'string',
  `s_internaltype` varchar(250) DEFAULT NULL,
  `b_userspecific` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `s_defaultvalue` varchar(250) NOT NULL,
  `s_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`s_key`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

";

// $_QUERIES['sbCR/repository/createTables/inactive'] = "
	
// CREATE TABLE IF NOT EXISTS `rep_nodetypes_dimensions` (
//   `fk_nodetype` varchar(40) NOT NULL,
//   `s_dimension` varchar(50) NOT NULL,
//   `n_steps` int(11) NOT NULL,
//   PRIMARY KEY (`fk_nodetype`,`s_dimension`),
//   CONSTRAINT `{TABLE_NODETPES}_dimensions_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
// CREATE TABLE IF NOT EXISTS `rep_workspaces` (
//   `s_workspacename` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
//   `s_workspaceprefix` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
//   `s_user` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
//   `s_pass` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
//   PRIMARY KEY (`s_workspacename`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
// ";

$_QUERIES['sbCR/repository/createEntries'] = "

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*!40000 ALTER TABLE `{TABLE_MODULES}` DISABLE KEYS */;
INSERT INTO `{TABLE_MODULES}` (`uuid`, `s_name`, `n_mainversion`, `n_subversion`, `n_bugfixversion`, `s_versioninfo`, `dt_installed`, `dt_updated`, `b_uninstallable`, `b_active`) VALUES
	('969b3b1e5721c24ff5b48d3cedf52fe4', 'sbSystem', 0, 0, 0, 'alpha', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FALSE', 'TRUE');
/*!40000 ALTER TABLE `{TABLE_MODULES}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_NAMESPACES}` DISABLE KEYS */;
INSERT INTO `{TABLE_NAMESPACES}` (`s_prefix`, `s_uri`) VALUES
	('jcr', 'http://www.jcp.org/jcr/1.0'),
	('mix', 'http://www.jcp.org/jcr/mix/1.0'),
	('nt', 'http://www.jcp.org/jcr/nt/1.0'),
	('sb', 'http://www.solidbytes.de/sbcr/1.0'),
	('sbSystem', 'http://www.solidbytes.de/sm/sbSystem/1.0'),
	('xml', 'http://www.w3.org/XML/1998/namespace');
/*!40000 ALTER TABLE `{TABLE_NAMESPACES}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_NODETYPES}` DISABLE KEYS */;
INSERT INTO `{TABLE_NODETYPES}` (`s_type`, `s_class`, `s_classfile`, `e_type`) VALUES
	('sbSystem:Comment', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Debug', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Favorite', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:FavoriteFolder', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Favorites', 'sbNode_favorites', 'sbSystem:sb.node.favorites', 'PRIMARY'),
	('sbSystem:Inbox', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:ListView', NULL, NULL, 'ABSTRACT'),
	('sbSystem:Logs', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Maintenance', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Module', 'sbNode_module', 'sbSystem:sb.node.module', 'PRIMARY'),
	('sbSystem:Modules', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Preferences', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:PropertiesView', NULL, NULL, 'ABSTRACT'),
	('sbSystem:Registry', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:RelationsView', NULL, NULL, 'ABSTRACT'),
	('sbSystem:Reports', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Reports_DB', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Reports_Structure', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Root', 'sbNode_root', 'sbSystem:sb.node.root', 'PRIMARY'),
	('sbSystem:StructureView', NULL, NULL, 'ABSTRACT'),
	('sbSystem:Taggable', NULL, NULL, 'ABSTRACT'),
	('sbSystem:Tags', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Task', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Tasks', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:TestNode', 'sbNode', 'sbSystem:sb.node', 'PRIMARY'),
	('sbSystem:Trashcan', 'sbNode_trashcan', 'sbSystem:sb.node.trashcan', 'PRIMARY'),
	('sbSystem:User', 'sbNode_user', 'sbSystem:sb.node.user', 'PRIMARY'),
	('sbSystem:Useraccounts', 'sbNode_useraccounts', 'sbSystem:sb.node.useraccounts', 'PRIMARY'),
	('sbSystem:Usergroup', 'sbNode_usergroup', 'sbSystem:sb.node.usergroup', 'PRIMARY'),
	('sbSystem:Voteable', NULL, NULL, 'ABSTRACT');
/*!40000 ALTER TABLE `{TABLE_NODETYPES}` ENABLE KEYS */;

-- Exportiere Daten aus Tabelle solidmatter.rep_nodetypes_inheritance: ~112 rows (ungefähr)
/*!40000 ALTER TABLE `{TABLE_NTHIERARCHY}` DISABLE KEYS */;
INSERT INTO `{TABLE_NTHIERARCHY}` (`fk_parentnodetype`, `fk_childnodetype`) VALUES
	('sbSystem:ListView', 'sbSystem:FavoriteFolder'),
	('sbSystem:ListView', 'sbSystem:Favorites'),
	('sbSystem:ListView', 'sbSystem:Root'),
	('sbSystem:ListView', 'sbSystem:TestNode'),
	('sbSystem:ListView', 'sbSystem:Useraccounts'),
	('sbSystem:ListView', 'sbSystem:Usergroup'),
	('sbSystem:PropertiesView', 'sbSystem:Comment'),
	('sbSystem:PropertiesView', 'sbSystem:FavoriteFolder'),
	('sbSystem:PropertiesView', 'sbSystem:TestNode'),
	('sbSystem:PropertiesView', 'sbSystem:User'),
	('sbSystem:PropertiesView', 'sbSystem:Usergroup'),
	('sbSystem:Taggable', 'sbSystem:TestNode'),
	('sbSystem:Taggable', 'sbSystem:User');
/*!40000 ALTER TABLE `{TABLE_NTHIERARCHY}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_MODES}` DISABLE KEYS */;
INSERT INTO `{TABLE_MODES}` (`s_mode`, `fk_parentnodetype`, `fk_nodetype`, `b_display`, `b_choosable`) VALUES
	('create', 'sbSystem:Comment', 'sbSystem:Comment', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Comment', 'sbSystem:Comment', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Maintenance', 'sbSystem:Debug', 'TRUE', 'TRUE'),
	('list', 'sbSystem:FavoriteFolder', 'sbSystem:Favorite', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:FavoriteFolder', 'sbSystem:Favorite', 'TRUE', 'TRUE'),
	('list', 'sbSystem:Favorites', 'sbSystem:Favorite', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Favorites', 'sbSystem:Favorite', 'TRUE', 'TRUE'),
	('create', 'sbSystem:Favorites', 'sbSystem:FavoriteFolder', 'TRUE', 'TRUE'),
	('list', 'sbSystem:Favorites', 'sbSystem:FavoriteFolder', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Favorites', 'sbSystem:FavoriteFolder', 'TRUE', 'TRUE'),
	('list', 'sbSystem:Root', 'sbSystem:Favorites', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Root', 'sbSystem:Favorites', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Maintenance', 'sbSystem:Logs', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Preferences', 'sbSystem:Maintenance', 'TRUE', 'TRUE'),
	('create', 'sbSystem:Modules', 'sbSystem:Module', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Modules', 'sbSystem:Module', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Preferences', 'sbSystem:Modules', 'TRUE', 'TRUE'),
	('list', 'sbSystem:Root', 'sbSystem:Preferences', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Root', 'sbSystem:Preferences', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Maintenance', 'sbSystem:Registry', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Preferences', 'sbSystem:Reports', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Reports', 'sbSystem:Reports_DB', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Reports', 'sbSystem:Reports_Structure', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Maintenance', 'sbSystem:Tags', 'TRUE', 'TRUE'),
	('create', 'sbSystem:Root', 'sbSystem:TestNode', 'TRUE', 'TRUE'),
	('list', 'sbSystem:Root', 'sbSystem:Trashcan', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Root', 'sbSystem:Trashcan', 'TRUE', 'TRUE'),
	('create', 'sbSystem:Useraccounts', 'sbSystem:User', 'TRUE', 'TRUE'),
	('list', 'sbSystem:Useraccounts', 'sbSystem:User', 'TRUE', 'TRUE'),
	('loadusers', 'sbSystem:Useraccounts', 'sbSystem:User', 'TRUE', 'TRUE'),
	('list', 'sbSystem:Usergroup', 'sbSystem:User', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Preferences', 'sbSystem:Useraccounts', 'TRUE', 'TRUE'),
	('create', 'sbSystem:Useraccounts', 'sbSystem:Usergroup', 'TRUE', 'TRUE'),
	('groups', 'sbSystem:Useraccounts', 'sbSystem:Usergroup', 'TRUE', 'TRUE'),
	('loadgroups', 'sbSystem:Useraccounts', 'sbSystem:Usergroup', 'TRUE', 'TRUE'),
	('tree', 'sbSystem:Useraccounts', 'sbSystem:Usergroup', 'TRUE', 'TRUE');
/*!40000 ALTER TABLE `{TABLE_MODES}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_PROPERTYDEFS}` DISABLE KEYS */;
INSERT INTO `{TABLE_PROPERTYDEFS}` (`fk_nodetype`, `s_attributename`, `e_type`, `s_internaltype`, `b_showinproperties`, `s_labelpath`, `e_storagetype`, `s_auxname`, `n_order`, `b_protected`, `b_protectedoncreation`, `b_multiple`, `s_defaultvalues`, `s_descriptionpath`) VALUES
	('sbSystem:Comment', 'comment', 'STRING', 'text', 'TRUE', '$locale/sbSystem/labels/comment', 'EXTERNAL', NULL, 0, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'config_active', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/active', 'AUXILIARY', 'b_active', NULL, 'TRUE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'info_installedon', 'DATE', 'datetime', 'FALSE', '', 'AUXILIARY', 'dt_installed', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'info_lastupdate', 'DATE', 'datetime', 'FALSE', '', 'AUXILIARY', 'dt_updated', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'info_name', 'STRING', 'string', 'FALSE', '', 'AUXILIARY', 's_name', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'info_uninstallable', 'BOOLEAN', 'checkbox', 'FALSE', '', 'AUXILIARY', 'b_uninstallable', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'version_bugfix', 'LONG', 'integer', 'FALSE', '', 'AUXILIARY', 'n_bugfixversion', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'version_main', 'LONG', 'integer', 'FALSE', '', 'AUXILIARY', 'n_mainversion', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'version_sub', 'LONG', 'integer', 'FALSE', '', 'AUXILIARY', 'n_subversion', NULL, 'FALSE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Module', 'version_suffix', 'STRING', 'string', 'FALSE', '', 'AUXILIARY', 's_versioninfo', NULL, 'TRUE', 'TRUE', 'FALSE', NULL, ''),
	('sbSystem:Trashcan', 'info_lastemptied', 'DATE', 'datetime', 'TRUE', '', 'EXTERNAL', NULL, NULL, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'config_hidestatus', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/User/config_hidestatus/@label', 'AUXILIARY', 'b_hidestatus', 5, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'info_activatedat', 'DATE', 'datetime', 'FALSE', '$locale/sbSystem/User/info_activatedat/@label', 'AUXILIARY', 'dt_activatedat', 13, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'info_currentlogin', 'DATE', 'datetime', 'FALSE', '$locale/sbSystem/User/info_currentlogin/@label', 'AUXILIARY', 'dt_currentlogin', 10, 'TRUE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'info_emailsent', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/User/info_emailsent/@label', 'AUXILIARY', 'b_emailsent', 3, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'info_lastlogin', 'DATE', 'datetime', 'FALSE', '$locale/sbSystem/User/info_lastlogin/@label', 'AUXILIARY', 'dt_lastlogin', 11, 'TRUE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'info_silentlogins', 'LONG', 'integer', 'FALSE', '$locale/sbSystem/User/info_silentlogins/@label', 'AUXILIARY', 'n_silentlogins', 9, 'TRUE', 'FALSE', 'FALSE', '0', ''),
	('sbSystem:User', 'info_successfullogins', 'LONG', 'integer', 'FALSE', '$locale/sbSystem/User/info_successfullogins/@label', 'AUXILIARY', 'n_successfullogins', 8, 'TRUE', 'FALSE', 'FALSE', '0', ''),
	('sbSystem:User', 'info_totalfailedlogins', 'LONG', 'integer', 'FALSE', '$locale/sbSystem/User/info_totalfailedlogins/@label', 'AUXILIARY', 'n_totalfailedlogins', 12, 'TRUE', 'FALSE', 'FALSE', '0', ''),
	('sbSystem:User', 'properties_comment', 'STRING', 'text', 'TRUE', '$locale/sbSystem/User/properties_comment/@label', 'AUXILIARY', 't_comment', 6, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'properties_email', 'STRING', 'email;maxlength=100;', 'TRUE', '$locale/sbSystem/User/properties_email/@label', 'AUXILIARY', 's_email', 0, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'security_activated', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/User/security_activated/@label', 'AUXILIARY', 'b_activated', 2, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'security_activationkey', 'STRING', 'string;maxlength=32;', 'FALSE', '$locale/sbSystem/User/security_activationkey/@label', 'AUXILIARY', 's_activationkey', 14, 'TRUE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'security_backendaccess', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/User/security_backendaccess/@label', 'AUXILIARY', 'b_backendaccess', 16, 'FALSE', 'FALSE', 'FALSE', 'FALSE', ''),
	('sbSystem:User', 'security_expires', 'DATE', 'datetime', 'TRUE', '$locale/sbSystem/User/security_expires/@label', 'AUXILIARY', 'dt_expires', 17, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'security_failedlogins', 'LONG', 'integer', 'FALSE', '$locale/sbSystem/User/security_failedlogins/@label', 'AUXILIARY', 'n_failedlogins', 7, 'TRUE', 'FALSE', 'FALSE', '0', ''),
	('sbSystem:User', 'security_locked', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/User/security_locked/@label', 'AUXILIARY', 'b_locked', 4, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'security_password', 'STRING', 'password;maxlength=100;minlength=4;', 'TRUE', '$locale/sbSystem/User/security_password/@label', 'AUXILIARY', 's_password', 1, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:User', 'security_stayloggedin', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/User/security_stayloggedin/@label', 'AUXILIARY', 'b_stayloggedin', 15, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:Usergroup', 'config_default', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/Usergroup/config_default/@label', 'EXTERNAL', NULL, NULL, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:Usergroup', 'config_hidden', 'BOOLEAN', 'checkbox', 'TRUE', '$locale/sbSystem/Usergroup/config_hidden/@label', 'EXTERNAL', NULL, NULL, 'FALSE', 'FALSE', 'FALSE', NULL, ''),
	('sbSystem:Usergroup', 'properties_description', 'STRING', 'text', 'TRUE', '$locale/sbSystem/Usergroup/properties_description/@label', 'EXTERNAL', NULL, NULL, 'FALSE', 'FALSE', 'FALSE', NULL, '');
/*!40000 ALTER TABLE `{TABLE_PROPERTYDEFS}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_ACTIONS}` DISABLE KEYS */;
INSERT INTO `{TABLE_ACTIONS}` (`fk_nodetype`, `s_view`, `s_action`, `b_default`, `s_classfile`, `s_class`, `e_outputtype`, `s_stylesheet`, `s_mimetype`, `b_uselocale`, `b_isrecallable`) VALUES
	('sbSystem:Debug', 'caches', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:debug.caches.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Debug', 'session', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:debug.session.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Debug', 'tests', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:debug.tests.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Debug', 'tests', 'init_progressbar', 'FALSE', NULL, NULL, 'XML', NULL, NULL, 'TRUE', 'FALSE'),
	('sbSystem:Debug', 'tests', 'test_progressbar', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:debug.tests.test_progressbar.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Debug', 'tree', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:debug.tree.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:ListView', 'list', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:node.list.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Logs', 'events', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:logs.events.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Logs', 'events', 'filter', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:logs.events.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Logs', 'system', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:logs.system.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Logs', 'system', 'show_log', 'FALSE', NULL, NULL, 'STREAM', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Maintenance', 'cache', 'clearCache', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.cache.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'cache', 'showOptions', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.cache.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Maintenance', 'repair', 'gatherAbandonedNodes', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'repair', 'optimizeUUIDs', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'repair', 'rebuildMaterializedPaths', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'repair', 'rebuildNestedSets', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'repair', 'rebuildNestedSetsMemory', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'repair', 'removeAbandonedNodes', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'repair', 'removeAbandonedProperties', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Maintenance', 'repair', 'showOptions', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:maintenance.repair.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Module', 'documentation', 'list', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:module.documentation.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Module', 'documentation', 'render', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:module.documentation.render.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Module', 'information', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:module.information.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Module', 'scripts', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:module.scripts.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Modules', 'overview', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:modules.overview.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:PropertiesView', 'properties', 'edit', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:node.properties.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:PropertiesView', 'properties', 'save', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:node.properties.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Registry', 'edit', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:registry.edit.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Registry', 'edit', 'save', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:registry.edit.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:RelationsView', 'relations', 'add', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:node.relations.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:RelationsView', 'relations', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:node.relations.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:RelationsView', 'relations', 'getTargets', 'FALSE', NULL, NULL, 'STREAM', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:RelationsView', 'relations', 'remove', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:node.relations.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Reports_DB', 'status', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:reports_db.status.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Reports_DB', 'tables', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:reports_db.tables.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Reports_Structure', 'nodetypes', 'overview', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:reports_structure.nodetypes.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Reports_Structure', 'repository', 'overview', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:reports_structure.repository.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Root', 'backend', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:root.backend.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'contextmenu', 'generate', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:root.contextmenu.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'login', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:root.login.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'login', 'getCaptcha', 'FALSE', NULL, NULL, 'STREAM', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'login', 'login', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:root.login.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'login', 'logout', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:root.login.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'menu', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:root.menu.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'structure', 'addToFavorites', 'FALSE', NULL, NULL, 'XML', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'structure', 'copy', 'FALSE', NULL, NULL, 'XML', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'structure', 'createChild', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:root.structure.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'structure', 'createLink', 'FALSE', NULL, NULL, 'XML', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'structure', 'cut', 'FALSE', NULL, NULL, 'XML', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'structure', 'deleteChild', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:global.confirm.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'structure', 'moveNode', 'FALSE', NULL, NULL, 'RENDERED', NULL, NULL, 'TRUE', 'FALSE'),
	('sbSystem:Root', 'structure', 'orderBefore', 'FALSE', NULL, NULL, 'RENDERED', NULL, NULL, 'TRUE', 'FALSE'),
	('sbSystem:Root', 'structure', 'paste', 'FALSE', NULL, NULL, 'XML', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'structure', 'saveChild', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:root.structure.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'structure', 'setPrimary', 'FALSE', NULL, NULL, 'XML', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'utilities', 'export_branch', 'FALSE', NULL, NULL, 'STREAM', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Root', 'utilities', 'show_progress', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:root.utilities.show_progress.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Root', 'welcome', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:root.welcome.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Tags', 'manage', 'clearUnused', 'FALSE', NULL, NULL, 'HEADERS', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Tags', 'manage', 'edit', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:tags.edit.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Tags', 'manage', 'list', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:tags.manage.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Tags', 'manage', 'save', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:tags.edit.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Trashcan', 'content', 'list', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:trashcan.list.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Trashcan', 'content', 'purge', 'FALSE', NULL, NULL, 'RENDERED', NULL, NULL, 'TRUE', 'FALSE'),
	('sbSystem:Trashcan', 'content', 'recover', 'FALSE', NULL, NULL, 'HEADERS', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Trashcan', 'content', 'recoverAll', 'FALSE', NULL, NULL, 'HEADERS', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:User', 'authorisations', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:userentity.authorisations.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:User', 'groups', 'add', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:user.groups.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:User', 'groups', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:user.groups.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:User', 'groups', 'remove', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:user.groups.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:User', 'password', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:user.password.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:User', 'password', 'save', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:user.password.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:User', 'registry', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:registry.edit.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:User', 'registry', 'save', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:registry.edit.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Useraccounts', 'gatherdata', 'groups', 'FALSE', NULL, NULL, 'OTHER', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Useraccounts', 'gatherdata', 'users', 'FALSE', NULL, NULL, 'OTHER', NULL, NULL, 'FALSE', 'FALSE'),
	('sbSystem:Usergroup', 'authorisations', 'display', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:userentity.authorisations.xsl', 'text/html', 'TRUE', 'TRUE');
/*!40000 ALTER TABLE `{TABLE_ACTIONS}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_VIEWAUTH}` DISABLE KEYS */;
INSERT INTO `{TABLE_VIEWAUTH}` (`fk_nodetype`, `fk_view`, `fk_action`, `fk_authorisation`) VALUES
	('sbSystem:Debug', 'session', '', 'read'),
	('sbSystem:Debug', 'tree', '', 'read'),
	('sbSystem:Debug', 'tests', '', 'special'),
	('sbSystem:Logs', 'events', '', 'read'),
	('sbSystem:Logs', 'system', '', 'read'),
	('sbSystem:Maintenance', 'cache', '', 'special'),
	('sbSystem:Maintenance', 'repair', '', 'special'),
	('sbSystem:Module', 'information', '', 'read'),
	('sbSystem:Module', 'scripts', '', 'read'),
	('sbSystem:Modules', 'overview', '', 'read'),
	('sbSystem:Registry', 'edit', '', 'write'),
	('sbSystem:Reports_DB', 'status', '', 'read'),
	('sbSystem:Reports_DB', 'tables', '', 'read'),
	('sbSystem:Reports_Structure', 'nodetypes', '', 'read'),
	('sbSystem:Reports_Structure', 'repository', '', 'read');
/*!40000 ALTER TABLE `{TABLE_VIEWAUTH}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_VIEWS}` DISABLE KEYS */;
INSERT INTO `{TABLE_VIEWS}` (`fk_nodetype`, `s_view`, `b_display`, `s_labelpath`, `s_classfile`, `s_class`, `n_order`, `n_priority`) VALUES
	('sbSystem:Debug', 'caches', 'TRUE', NULL, 'sbSystem:sb.node.debug.view.caches', 'sbView_debug_caches', 1, 800),
	('sbSystem:Debug', 'session', 'TRUE', NULL, 'sbSystem:sb.node.debug.view.session', 'sbView_debug_session', 0, 1000),
	('sbSystem:Debug', 'tests', 'TRUE', NULL, 'sbSystem:sb.node.debug.view.tests', 'sbView_debug_tests', 3, 700),
	('sbSystem:Debug', 'tree', 'TRUE', NULL, 'sbSystem:sb.node.debug.view.tree', 'sbView_debug_tree', 2, 900),
	('sbSystem:ListView', 'list', 'TRUE', NULL, 'sbSystem:sb.node.view.list', 'sbView_list', 200, 550),
	('sbSystem:Logs', 'events', 'TRUE', NULL, 'sbSystem:sb.node.logs.view.events', 'sbView_logs_events', 40, 1000),
	('sbSystem:Logs', 'system', 'TRUE', NULL, 'sbSystem:sb.node.logs.view.system', 'sbView_logs_system', 50, 900),
	('sbSystem:Maintenance', 'cache', 'TRUE', NULL, 'sbSystem:sb.node.maintenance.view.cache', 'sbView_maintenance_cache', 40, 1000),
	('sbSystem:Maintenance', 'repair', 'TRUE', NULL, 'sbSystem:sb.node.maintenance.view.repair', 'sbView_maintenance_repair', 50, 900),
	('sbSystem:Module', 'documentation', 'TRUE', NULL, 'sbSystem:sb.node.module.view.documentation', 'sbView_module_documentation', 700, 0),
	('sbSystem:Module', 'information', 'TRUE', NULL, 'sbSystem:sb.node.module.view.information', 'sbView_module_information', 500, 1000),
	('sbSystem:Module', 'scripts', 'TRUE', NULL, 'sbSystem:sb.node.module.view.scripts', 'sbView_module_scripts', 600, 0),
	('sbSystem:Modules', 'overview', 'TRUE', NULL, 'sbSystem:sb.node.modules.view.overview', 'sbView_modules_overview', 100, 1000),
	('sbSystem:PropertiesView', 'properties', 'TRUE', NULL, 'sbSystem:sb.node.view.properties', 'sbView_properties', 100, 500),
	('sbSystem:Registry', 'edit', 'TRUE', NULL, 'sbSystem:sb.node.registry.view.edit', 'sbView_registry_edit', 50, 1000),
	('sbSystem:RelationsView', 'relations', 'TRUE', NULL, 'sbSystem:sb.node.view.relations', 'sbView_relations', 300, 300),
	('sbSystem:Reports_DB', 'status', 'TRUE', NULL, 'sbSystem:sb.node.reports_db.view.status', 'sbView_reports_db_status', 200, 900),
	('sbSystem:Reports_DB', 'tables', 'TRUE', NULL, 'sbSystem:sb.node.reports_db.view.tables', 'sbView_reports_db_tables', 100, 1000),
	('sbSystem:Reports_Structure', 'nodetypes', 'TRUE', NULL, 'sbSystem:sb.node.reports_structure.view.nodetypes', 'sbView_reports_structure_nodetypes', 100, 1000),
	('sbSystem:Reports_Structure', 'repository', 'TRUE', NULL, 'sbSystem:sb.node.reports_structure.view.repository', 'sbView_reports_structure_repository', 200, 900),
	('sbSystem:Root', 'backend', 'FALSE', NULL, 'sbSystem:sb.node.root.view.backend', 'sbView_root_backend', NULL, 0),
	('sbSystem:Root', 'contextmenu', 'FALSE', NULL, 'sbSystem:sb.node.root.view.contextmenu', 'sbView_root_contextmenu', NULL, 0),
	('sbSystem:Root', 'login', 'FALSE', NULL, 'sbSystem:sb.node.root.view.login', 'sbView_root_login', NULL, 0),
	('sbSystem:Root', 'menu', 'FALSE', NULL, 'sbSystem:sb.node.root.view.menu', 'sbView_root_menu', NULL, 0),
	('sbSystem:Root', 'structure', 'FALSE', NULL, 'sbSystem:sb.node.view.structure', 'sbView_structure', NULL, 0),
	('sbSystem:Root', 'utilities', 'FALSE', NULL, 'sbSystem:sb.node.root.view.utilities', 'sbView_root_utilities', NULL, 0),
	('sbSystem:Root', 'welcome', 'TRUE', NULL, 'sbSystem:sb.node.root.view.welcome', 'sbView_root_welcome', 100, 1000),
	('sbSystem:Tags', 'manage', 'TRUE', NULL, 'sbSystem:sb.node.tags.view.manage', 'sbView_tags_manage', 100, 1000),
	('sbSystem:Trashcan', 'content', 'TRUE', NULL, 'sbSystem:sb.node.trashcan.view.content', 'sbView_trashcan_content', 100, 1000),
	('sbSystem:User', 'authorisations', 'TRUE', NULL, 'sbSystem:sb.node.userentity.view.authorisations', 'sbView_userentity_authorisations', 600, 0),
	('sbSystem:User', 'groups', 'TRUE', NULL, 'sbSystem:sb.node.user.view.groups', 'sbView_user_groups', 500, 0),
	('sbSystem:User', 'password', 'TRUE', NULL, 'sbSystem:sb.node.user.view.password', 'sbView_user_password', 100, 0),
	('sbSystem:User', 'registry', 'TRUE', NULL, 'sbSystem:sb.node.registry.view.edit', 'sbView_registry_edit', 700, 0),
	('sbSystem:Useraccounts', 'gatherdata', 'FALSE', NULL, 'sbSystem:sb.node.useraccounts.view.gatherdata', 'sbView_useraccounts_gatherdata', NULL, 0),
	('sbSystem:Usergroup', 'authorisations', 'TRUE', NULL, 'sbSystem:sb.node.userentity.view.authorisations', 'sbView_userentity_authorisations', 600, 0);
/*!40000 ALTER TABLE `{TABLE_VIEWS}` ENABLE KEYS */;

/*!40000 ALTER TABLE `{TABLE_REGISTRY}` DISABLE KEYS */;
INSERT INTO `{TABLE_REGISTRY}` (`s_key`, `e_type`, `s_internaltype`, `b_userspecific`, `s_defaultvalue`, `s_comment`) VALUES
	('sb.system.backend.ui.form.labels.width', 'string', NULL, 'FALSE', '25%', 'unused'),
	('sb.system.backend.ui.path.enabled', 'boolean', NULL, 'FALSE', 'TRUE', 'unused'),
	('sb.system.backend.ui.path.steps', 'integer', NULL, 'FALSE', '0', 'unused'),
	('sb.system.backend.ui.tree.width', 'string', NULL, 'FALSE', '200', 'unused'),
	('sb.system.cache.authorisations.enabled', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.cache.images.enabled', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.cache.images.strategy', 'string', 'select;options=DATABASE|FILES', 'FALSE', 'DATABASE', 'TODO: FILES needs to be implemented'),
	('sb.system.cache.nodes.enabled', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.cache.nodetypes.enabled', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.cache.paths.enabled', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.cache.registry.changedetection', 'string', NULL, 'FALSE', '', 'is updated automatically'),
	('sb.system.cache.registry.enabled', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.debug.menu.debugmode', 'boolean', NULL, 'TRUE', 'FALSE', NULL),
	('sb.system.debug.tab.enabled', 'boolean', NULL, 'TRUE', 'FALSE', NULL),
	('sb.system.debug.warnings.enabled', 'boolean', NULL, 'TRUE', 'FALSE', 'unused'),
	('sb.system.language', 'string', 'select;options=de|en', 'TRUE', 'en', 'TODO: should not be fixed'),
	('sb.system.locks.enabled', 'boolean', NULL, 'FALSE', 'TRUE', 'unused'),
	('sb.system.locks.timetolive.default', 'integer', NULL, 'FALSE', '300', 'unused'),
	('sb.system.locks.timetolive.override', 'boolean', NULL, 'FALSE', 'FALSE', 'unused'),
	('sb.system.log.access.enabled', 'boolean', NULL, 'FALSE', 'FALSE', 'needs update'),
	('sb.system.log.access.file', 'string', NULL, 'FALSE', '_logs/log_access.txt', 'needs update'),
	('sb.system.log.access.request', 'boolean', NULL, 'FALSE', 'FALSE', 'needs update'),
	('sb.system.log.access.server', 'boolean', NULL, 'FALSE', 'FALSE', 'needs update'),
	('sb.system.log.exceptions.enabled', 'boolean', NULL, 'FALSE', 'TRUE', 'unused'),
	('sb.system.log.exceptions.file', 'string', NULL, 'FALSE', '_logs/log_exceptions.txt', 'unused'),
	('sb.system.privacy.events', 'boolean', NULL, 'TRUE', 'TRUE', 'does not log the user id when events are logged'),
	('sb.system.privacy.login', 'boolean', NULL, 'TRUE', 'TRUE', 'TODO: expand on IP - does not log user name on login events'),
	('sb.system.repository.mode.dependable', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.security.ipranges.allowed', 'string', 'text', 'FALSE', '', NULL),
	('sb.system.security.ipranges.denied', 'string', 'text', 'FALSE', '', NULL),
	('sb.system.security.login.authentication.method', 'string', 'select;options=default', 'FALSE', 'default', NULL),
	('sb.system.security.login.captcha.enabled', 'boolean', NULL, 'TRUE', 'FALSE', NULL),
	('sb.system.security.login.captcha.noisetype', 'string', 'select;options=CIRCLES|LINES|STRIPES', 'FALSE', 'STRIPES', NULL),
	('sb.system.security.login.captcha.sequencetype', 'string', 'select;options=ALPHA|ALPHANUMERIC|NUMERIC|EXTENDED|REDUCED', 'FALSE', 'REDUCED', NULL),
	('sb.system.security.login.failed.locktime', 'integer', NULL, 'FALSE', '5', NULL),
	('sb.system.security.login.failed.numallowed', 'integer', NULL, 'FALSE', '5', NULL),
	('sb.system.security.login.fingerprint.enabled', 'boolean', NULL, 'FALSE', 'FALSE', 'immediately log out user if browse or ip changes?'),
	('sb.system.security.users.email.change.allowed', 'boolean', NULL, 'FALSE', 'FALSE', NULL),
	('sb.system.security.users.password.change.allowed', 'boolean', NULL, 'FALSE', 'TRUE', NULL),
	('sb.system.security.users.password.expire.days', 'integer', NULL, 'FALSE', '365', 'unused'),
	('sb.system.security.users.password.expire.enabled', 'boolean', NULL, 'FALSE', 'FALSE', 'unused'),
	('sb.system.security.users.password.recovery.allowed', 'boolean', NULL, 'FALSE', 'FALSE', 'unused'),
	('sb.system.session.timeout', 'integer', NULL, 'FALSE', '3600', 'seconds'),
	('sb.system.temp.dir', 'string', NULL, 'FALSE', '_temp', NULL),
	('_sb.system.backend.output.prettyprint', 'boolean', NULL, 'FALSE', 'TRUE', 'unused'),
	('_sb.system.debug.bounce.enabled', 'boolean', NULL, 'FALSE', 'FALSE', 'unused');
/*!40000 ALTER TABLE `{TABLE_REGISTRY}` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

";

// $_QUERIES['sbCR/repository/createEntries/inactive'] = "
	
// /*!40000 ALTER TABLE `rep_nodetypes_lifecycles` DISABLE KEYS */;
// INSERT INTO `rep_nodetypes_lifecycles` (`fk_nodetype`, `s_state`, `s_statetransition`) VALUES
// 	('sbCMS:Page', 'approved', 'published'),
// 	('sbCMS:Page', 'approved', 'wip'),
// 	('sbCMS:Page', 'default', 'wip'),
// 	('sbCMS:Page', 'published', 'wip'),
// 	('sbCMS:Page', 'review', 'approved'),
// 	('sbCMS:Page', 'review', 'wip'),
// 	('sbCMS:Page', 'wip', 'review'),
// 	('sbJukebox:Album', 'default', 'review'),
// 	('sbJukebox:Album', 'review', 'default'),
// 	('sbProjects:Project', 'default', 'approved'),
// 	('sbProjects:RfC', 'default', 'approved');
// /*!40000 ALTER TABLE `rep_nodetypes_lifecycles` ENABLE KEYS */;

// -- Exportiere Daten aus Tabelle solidmatter.rep_nodetypes_mimetypemapping: ~3 rows (ungefähr)
// /*!40000 ALTER TABLE `rep_nodetypes_mimetypemapping` DISABLE KEYS */;
// INSERT INTO `rep_nodetypes_mimetypemapping` (`s_mimetype`, `fk_nodetype`) VALUES
// 	('image/gif', 'sbFiles:Image'),
// 	('image/jpeg', 'sbFiles:Image'),
// 	('image/x-png', 'sbFiles:Image');
// /*!40000 ALTER TABLE `rep_nodetypes_mimetypemapping` ENABLE KEYS */;

// -- Exportiere Daten aus Tabelle solidmatter.rep_nodetypes_ontology: ~19 rows (ungefähr)
// /*!40000 ALTER TABLE `rep_nodetypes_ontology` DISABLE KEYS */;
// INSERT INTO `rep_nodetypes_ontology` (`s_relation`, `fk_sourcenodetype`, `fk_targetnodetype`, `s_reverserelation`) VALUES
// 	('AlsoRequires', 'sbIdM:TechRole', 'sbIdM:TechRole', 'IsRequiredBy'),
// 	('CouldBeFrom', 'sbJukebox:Album', 'sbJukebox:Artist', 'CouldHaveDone'),
// 	('HasDataOwner', 'sbIdM:DataCategory', 'sbIdM:OrgRole', 'IsDataOwner'),
// 	('HasMixed', 'sbJukebox:Artist', 'sbJukebox:Album', 'MixedBy'),
// 	('HasRemixed', 'sbJukebox:Artist', 'sbJukebox:Track', 'RemixedBy'),
// 	('HasRoleOwner', 'sbIdM:TechRole', 'sbIdM:OrgRole', 'IsRoleOwner'),
// 	('IsCoverOf', 'sbJukebox:Track', 'sbJukebox:Track', 'IsReferenceTo'),
// 	('IsMemberOf', 'sbJukebox:Artist', 'sbJukebox:Artist', 'HasMember'),
// 	('IsPseudonymOf', 'sbJukebox:Artist', 'sbJukebox:Artist', 'IsRealNameOf'),
// 	('IsRemixOf', 'sbJukebox:Album', 'sbJukebox:Album', 'IsOriginalTo'),
// 	('IsRemixOf', 'sbJukebox:Track', 'sbJukebox:Track', 'IsOriginalTo'),
// 	('IsSuccessorOf', 'sbJukebox:Artist', 'sbJukebox:Artist', 'IsPredecessorTo'),
// 	('IsSynonymOf', 'sbJukebox:Artist', 'sbJukebox:Artist', 'IsSynonymOf'),
// 	('MayBeAssignedBy', 'sbIdM:TechRole', 'sbIdM:OrgRole', 'MayAssign'),
// 	('SharesMembersWith', 'sbJukebox:Artist', 'sbJukebox:Artist', 'SharesMembersWith'),
// 	('SoundsLike', 'sbJukebox:Artist', 'sbJukebox:Artist', 'SoundsLike'),
// 	('YouMightAlsoLike', 'sbJukebox:Album', 'sbJukebox:Album', 'YouMightAlsoLike'),
// 	('YouMightAlsoLike', 'sbJukebox:Artist', 'sbJukebox:Album', 'YouMightAlsoLike'),
// 	('YouMightAlsoLike', 'sbJukebox:Artist', 'sbJukebox:Artist', 'YouMightAlsoLike');
// /*!40000 ALTER TABLE `rep_nodetypes_ontology` ENABLE KEYS */;
	
// -- Exportiere Daten aus Tabelle solidmatter.rep_nodetypes_authorisations: ~57 rows (ungefähr)
// /*!40000 ALTER TABLE `rep_nodetypes_authorisations` DISABLE KEYS */;
// INSERT INTO `rep_nodetypes_authorisations` (`fk_nodetype`, `s_authorisation`, `fk_parentauthorisation`, `b_default`, `n_order`, `b_onlyfrontend`) VALUES
// 	('sbCMS:Page', 'delete_page', 'write', 'FALSE', 5, 'FALSE'),
// 	('sbCMS:Page', 'edit_page', 'write', 'FALSE', 4, 'FALSE'),
// 	('sbCMS:Page', 'list', 'read', 'FALSE', 6, 'FALSE'),
// 	('sbCMS:Site', 'delete_page', 'write', 'FALSE', 6, 'FALSE'),
// 	('sbCMS:Site', 'edit_page', 'write', 'FALSE', 5, 'FALSE'),
// 	('sbCMS:Site', 'list', 'read', 'FALSE', 4, 'FALSE'),
// 	('sbCMS:Site', 'publish', 'special', 'FALSE', 7, 'FALSE'),
// 	('sbFiles:Filemanager', 'list', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbFiles:Folder', 'upload_files', 'write', 'FALSE', 3, 'FALSE'),
// 	('sbForum:Forum', 'access_attachments', 'read', 'FALSE', 1, 'TRUE'),
// 	('sbForum:Forum', 'access_forum', 'read', 'TRUE', 0, 'FALSE'),
// 	('sbForum:Forum', 'add_posts', 'write', 'TRUE', 3, 'TRUE'),
// 	('sbForum:Forum', 'add_topics', 'write', 'FALSE', 7, 'TRUE'),
// 	('sbForum:Forum', 'delete_own_posts', 'write', 'FALSE', 6, 'FALSE'),
// 	('sbForum:Forum', 'delete_posts', 'write', 'FALSE', 5, 'FALSE'),
// 	('sbForum:Forum', 'delete_topics', 'write', 'FALSE', 10, 'FALSE'),
// 	('sbForum:Forum', 'edit_config', 'write', 'FALSE', 1, 'FALSE'),
// 	('sbForum:Forum', 'edit_own_posts', 'write', 'FALSE', 0, 'TRUE'),
// 	('sbForum:Forum', 'edit_posts', 'write', 'FALSE', 4, 'FALSE'),
// 	('sbForum:Forum', 'edit_topics', 'write', 'FALSE', 8, 'FALSE'),
// 	('sbForum:Forum', 'manage_forums', 'special', 'FALSE', 2, 'FALSE'),
// 	('sbForum:Forum', 'move_topics', 'special', 'FALSE', 9, 'FALSE'),
// 	('sbForum:Forum', 'upload_attachments', 'write', 'FALSE', 11, 'FALSE'),
// 	('sbForum:Forum', 'view_urls', 'read', 'FALSE', 2, 'FALSE'),
// 	('sbJukebox:Album', 'comment', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Album', 'download', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Album', 'play', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Album', 'rate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Album', 'relate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Album', 'tag', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Artist', 'comment', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Artist', 'download', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Artist', 'rate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Artist', 'relate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Artist', 'tag', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'add_playlists', 'write', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'clear_library', 'write', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'comment', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'download', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'edit_lyrics', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'play', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'rate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'relate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'start_import', 'write', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Jukebox', 'tag', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Playlist', 'add_titles', 'write', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Playlist', 'comment', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Playlist', 'download', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Playlist', 'play', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Playlist', 'rate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Playlist', 'tag', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Track', 'comment', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Track', 'edit_lyrics', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Track', 'play', 'read', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Track', 'rate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Track', 'relate', 'special', 'FALSE', 0, 'FALSE'),
// 	('sbJukebox:Track', 'tag', 'special', 'FALSE', 0, 'FALSE');
// /*!40000 ALTER TABLE `rep_nodetypes_authorisations` ENABLE KEYS */;

// -- Exportiere Daten aus Tabelle solidmatter.rep_nodetypes_dimensions: ~4 rows (ungefähr)
// /*!40000 ALTER TABLE `rep_nodetypes_dimensions` DISABLE KEYS */;
// INSERT INTO `rep_nodetypes_dimensions` (`fk_nodetype`, `s_dimension`, `n_steps`) VALUES
// 	('sbJukebox:MusicDimensions', 'dancable', 10),
// 	('sbJukebox:MusicDimensions', 'hardness', 10),
// 	('sbJukebox:MusicDimensions', 'mood', 10),
// 	('sbJukebox:MusicDimensions', 'speed', 10);
// /*!40000 ALTER TABLE `rep_nodetypes_dimensions` ENABLE KEYS */;

// /*!40000 ALTER TABLE `rep_workspaces` DISABLE KEYS */;
// /*!40000 ALTER TABLE `rep_workspaces` ENABLE KEYS */;

// ";

?>