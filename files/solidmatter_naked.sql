-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.1.34-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Struktur von Tabelle solidmatter_naked.global_system_cache
CREATE TABLE IF NOT EXISTS `global_system_cache` (
  `s_key` varchar(250) NOT NULL,
  `fk_subject` char(32) DEFAULT NULL,
  `fk_modifier` char(32) DEFAULT NULL,
  `t_value` longblob NOT NULL,
  PRIMARY KEY (`s_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.global_system_cache: ~0 rows (ungefähr)
DELETE FROM `global_system_cache`;
/*!40000 ALTER TABLE `global_system_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_system_cache` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.global_system_cache_flat
CREATE TABLE IF NOT EXISTS `global_system_cache_flat` (
  `s_key` varchar(250) NOT NULL,
  `t_value` mediumtext NOT NULL,
  PRIMARY KEY (`s_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.global_system_cache_flat: ~0 rows (ungefähr)
DELETE FROM `global_system_cache_flat`;
/*!40000 ALTER TABLE `global_system_cache_flat` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_system_cache_flat` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.global_system_sessions
CREATE TABLE IF NOT EXISTS `global_system_sessions` (
  `s_sessionid` char(32) CHARACTER SET ascii NOT NULL,
  `s_data` text NOT NULL,
  `ts_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `n_lifespan` int(10) unsigned NOT NULL,
  PRIMARY KEY (`s_sessionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.global_system_sessions: 0 rows
DELETE FROM `global_system_sessions`;
/*!40000 ALTER TABLE `global_system_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_system_sessions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_modules
CREATE TABLE IF NOT EXISTS `rep_modules` (
  `s_name` varchar(30) NOT NULL,
  `s_title` varchar(50) NOT NULL,
  `n_mainversion` int(11) NOT NULL,
  `n_subversion` int(11) NOT NULL,
  `n_bugfixversion` int(11) NOT NULL,
  `s_versioninfo` varchar(20) DEFAULT NULL,
  `dt_installed` datetime NOT NULL,
  `dt_updated` datetime NOT NULL,
  `b_uninstallable` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  `b_active` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`s_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_modules: ~1 rows (ungefähr)
DELETE FROM `rep_modules`;
/*!40000 ALTER TABLE `rep_modules` DISABLE KEYS */;
INSERT INTO `rep_modules` (`s_name`, `s_title`, `n_mainversion`, `n_subversion`, `n_bugfixversion`, `s_versioninfo`, `dt_installed`, `dt_updated`, `b_uninstallable`, `b_active`) VALUES
	('sb_system', 'sbSystem', 1, 0, 0, 'alpha', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FALSE', 'TRUE');
/*!40000 ALTER TABLE `rep_modules` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_namespaces
CREATE TABLE IF NOT EXISTS `rep_namespaces` (
  `s_prefix` varchar(30) CHARACTER SET ascii NOT NULL,
  `s_uri` varchar(200) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`s_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_namespaces: ~14 rows (ungefähr)
DELETE FROM `rep_namespaces`;
/*!40000 ALTER TABLE `rep_namespaces` DISABLE KEYS */;
INSERT INTO `rep_namespaces` (`s_prefix`, `s_uri`) VALUES
	('jcr', 'http://www.jcp.org/jcr/1.0'),
	('mix', 'http://www.jcp.org/jcr/mix/1.0'),
	('nt', 'http://www.jcp.org/jcr/nt/1.0'),
	('sb', 'http://www.solidbytes.de/sbcr/1.0'),
	('sbCMS', 'http://www.solidbytes.de/sm/sbCMS/1.0'),
	('sbForum', 'http://www.solidbytes.de/sm/sbForum/1.0'),
	('sbGuestbook', 'http://www.solidbytes.de/sm/sbGuestbook/1.0'),
	('sbJukebox', 'http://www.solidbytes.de/sm/sbJukebox/1.0'),
	('sbNews', 'http://www.solidbytes.de/sm/sbNews/1.0'),
	('sbProjects', 'http://www.solidbytes.de/sm/sbProjects/1.0'),
	('sbShop', 'http://www.solidbytes.de/sm/sbShop/1.0'),
	('sbSystem', 'http://www.solidbytes.de/sm/sbSystem/1.0'),
	('sbUtilities', 'http://www.solidbytes.de/sm/sbUtilities/1.0'),
	('xml', 'http://www.w3.org/XML/1998/namespace');
/*!40000 ALTER TABLE `rep_namespaces` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes
CREATE TABLE IF NOT EXISTS `rep_nodetypes` (
  `s_type` varchar(40) NOT NULL,
  `s_class` varchar(50) DEFAULT NULL,
  `s_classfile` varchar(100) DEFAULT NULL,
  `e_type` enum('PRIMARY','MIXIN','ABSTRACT') NOT NULL DEFAULT 'PRIMARY',
  PRIMARY KEY (`s_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes: ~30 rows (ungefähr)
DELETE FROM `rep_nodetypes`;
/*!40000 ALTER TABLE `rep_nodetypes` DISABLE KEYS */;
INSERT INTO `rep_nodetypes` (`s_type`, `s_class`, `s_classfile`, `e_type`) VALUES
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
	('sbSystem:Modules', 'sbNode_modules', 'sbSystem:sb.node.modules', 'PRIMARY'),
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
/*!40000 ALTER TABLE `rep_nodetypes` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_authorisations
CREATE TABLE IF NOT EXISTS `rep_nodetypes_authorisations` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_authorisation` varchar(30) NOT NULL,
  `fk_parentauthorisation` varchar(30) DEFAULT NULL,
  `b_default` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'FALSE',
  `n_order` int(10) unsigned NOT NULL,
  `b_onlyfrontend` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`fk_nodetype`,`s_authorisation`),
  CONSTRAINT `rep_nodetypes_authorisations_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_authorisations: ~0 rows (ungefähr)
DELETE FROM `rep_nodetypes_authorisations`;
/*!40000 ALTER TABLE `rep_nodetypes_authorisations` DISABLE KEYS */;
/*!40000 ALTER TABLE `rep_nodetypes_authorisations` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_dimensions
CREATE TABLE IF NOT EXISTS `rep_nodetypes_dimensions` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_dimension` varchar(50) NOT NULL,
  `n_steps` int(11) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_dimension`),
  CONSTRAINT `rep_nodetypes_dimensions_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_dimensions: ~4 rows (ungefähr)
DELETE FROM `rep_nodetypes_dimensions`;
/*!40000 ALTER TABLE `rep_nodetypes_dimensions` DISABLE KEYS */;
/*!40000 ALTER TABLE `rep_nodetypes_dimensions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_inheritance
CREATE TABLE IF NOT EXISTS `rep_nodetypes_inheritance` (
  `fk_parentnodetype` varchar(40) NOT NULL,
  `fk_childnodetype` varchar(40) NOT NULL,
  PRIMARY KEY (`fk_parentnodetype`,`fk_childnodetype`),
  KEY `fk_nti_2` (`fk_childnodetype`),
  CONSTRAINT `rep_nodetypes_inheritance_fk_cnt` FOREIGN KEY (`fk_parentnodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rep_nodetypes_inheritance_fk_pnt` FOREIGN KEY (`fk_childnodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_inheritance: ~13 rows (ungefähr)
DELETE FROM `rep_nodetypes_inheritance`;
/*!40000 ALTER TABLE `rep_nodetypes_inheritance` DISABLE KEYS */;
INSERT INTO `rep_nodetypes_inheritance` (`fk_parentnodetype`, `fk_childnodetype`) VALUES
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
/*!40000 ALTER TABLE `rep_nodetypes_inheritance` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_lifecycles
CREATE TABLE IF NOT EXISTS `rep_nodetypes_lifecycles` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_state` varchar(50) NOT NULL,
  `s_statetransition` varchar(50) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_state`,`s_statetransition`),
  CONSTRAINT `rep_nodetypes_lifecycles_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_lifecycles: ~0 rows (ungefähr)
DELETE FROM `rep_nodetypes_lifecycles`;
/*!40000 ALTER TABLE `rep_nodetypes_lifecycles` DISABLE KEYS */;
/*!40000 ALTER TABLE `rep_nodetypes_lifecycles` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_mimetypemapping
CREATE TABLE IF NOT EXISTS `rep_nodetypes_mimetypemapping` (
  `s_mimetype` varchar(50) NOT NULL,
  `fk_nodetype` varchar(40) NOT NULL,
  PRIMARY KEY (`s_mimetype`),
  KEY `rep_ntmime1` (`fk_nodetype`),
  CONSTRAINT `rep_nodetypes_mimetypemapping_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_mimetypemapping: ~3 rows (ungefähr)
DELETE FROM `rep_nodetypes_mimetypemapping`;
/*!40000 ALTER TABLE `rep_nodetypes_mimetypemapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `rep_nodetypes_mimetypemapping` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_modes
CREATE TABLE IF NOT EXISTS `rep_nodetypes_modes` (
  `s_mode` varchar(30) NOT NULL,
  `fk_parentnodetype` varchar(40) NOT NULL,
  `fk_nodetype` varchar(40) NOT NULL,
  `b_display` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  `b_choosable` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  PRIMARY KEY (`fk_nodetype`,`fk_parentnodetype`,`s_mode`),
  KEY `parentnodetype` (`fk_parentnodetype`),
  KEY `mode_parentnodetype` (`s_mode`,`fk_parentnodetype`),
  CONSTRAINT `rep_nodetypes_modes_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rep_nodetypes_modes_fk_pnt` FOREIGN KEY (`fk_parentnodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_modes: ~36 rows (ungefähr)
DELETE FROM `rep_nodetypes_modes`;
/*!40000 ALTER TABLE `rep_nodetypes_modes` DISABLE KEYS */;
INSERT INTO `rep_nodetypes_modes` (`s_mode`, `fk_parentnodetype`, `fk_nodetype`, `b_display`, `b_choosable`) VALUES
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
/*!40000 ALTER TABLE `rep_nodetypes_modes` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_ontology
CREATE TABLE IF NOT EXISTS `rep_nodetypes_ontology` (
  `s_relation` varchar(50) CHARACTER SET ascii NOT NULL,
  `fk_sourcenodetype` varchar(40) CHARACTER SET utf8 NOT NULL,
  `fk_targetnodetype` varchar(40) CHARACTER SET utf8 NOT NULL,
  `s_reverserelation` varchar(50) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`s_relation`,`fk_sourcenodetype`,`fk_targetnodetype`),
  KEY `rep_nto_source` (`fk_sourcenodetype`),
  KEY `rep_nto_target` (`fk_targetnodetype`),
  CONSTRAINT `rep_nodetypes_ontology_snt` FOREIGN KEY (`fk_sourcenodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rep_nodetypes_ontology_tnt` FOREIGN KEY (`fk_targetnodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_ontology: ~0 rows (ungefähr)
DELETE FROM `rep_nodetypes_ontology`;
/*!40000 ALTER TABLE `rep_nodetypes_ontology` DISABLE KEYS */;
/*!40000 ALTER TABLE `rep_nodetypes_ontology` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_properties
CREATE TABLE IF NOT EXISTS `rep_nodetypes_properties` (
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
  CONSTRAINT `rep_nodetypes_properties_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_properties: ~32 rows (ungefähr)
DELETE FROM `rep_nodetypes_properties`;
/*!40000 ALTER TABLE `rep_nodetypes_properties` DISABLE KEYS */;
INSERT INTO `rep_nodetypes_properties` (`fk_nodetype`, `s_attributename`, `e_type`, `s_internaltype`, `b_showinproperties`, `s_labelpath`, `e_storagetype`, `s_auxname`, `n_order`, `b_protected`, `b_protectedoncreation`, `b_multiple`, `s_defaultvalues`, `s_descriptionpath`) VALUES
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
/*!40000 ALTER TABLE `rep_nodetypes_properties` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_viewactions
CREATE TABLE IF NOT EXISTS `rep_nodetypes_viewactions` (
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
  CONSTRAINT `rep_nodetypes_viewactions_fk_ntv` FOREIGN KEY (`fk_nodetype`, `s_view`) REFERENCES `rep_nodetypes_views` (`fk_nodetype`, `s_view`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_viewactions: ~82 rows (ungefähr)
DELETE FROM `rep_nodetypes_viewactions`;
/*!40000 ALTER TABLE `rep_nodetypes_viewactions` DISABLE KEYS */;
INSERT INTO `rep_nodetypes_viewactions` (`fk_nodetype`, `s_view`, `s_action`, `b_default`, `s_classfile`, `s_class`, `e_outputtype`, `s_stylesheet`, `s_mimetype`, `b_uselocale`, `b_isrecallable`) VALUES
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
	('sbSystem:Module', 'installation', 'install', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:module.installation.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Module', 'installation', 'showStatus', 'TRUE', NULL, NULL, 'RENDERED', 'sb_system:module.installation.xsl', 'text/html', 'TRUE', 'TRUE'),
	('sbSystem:Module', 'installation', 'uninstall', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:module.installation.xsl', 'text/html', 'TRUE', 'FALSE'),
	('sbSystem:Module', 'installation', 'update', 'FALSE', NULL, NULL, 'RENDERED', 'sb_system:module.installation.xsl', 'text/html', 'TRUE', 'FALSE'),
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
/*!40000 ALTER TABLE `rep_nodetypes_viewactions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_viewauthorisations
CREATE TABLE IF NOT EXISTS `rep_nodetypes_viewauthorisations` (
  `fk_nodetype` varchar(40) NOT NULL,
  `fk_view` varchar(50) NOT NULL,
  `fk_action` varchar(50) NOT NULL,
  `fk_authorisation` varchar(30) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`fk_view`,`fk_action`),
  KEY `rep_ntva2` (`fk_nodetype`,`fk_authorisation`),
  CONSTRAINT `rep_nodetypes_viewauthorisations_fk_ntv` FOREIGN KEY (`fk_nodetype`, `fk_view`) REFERENCES `rep_nodetypes_views` (`fk_nodetype`, `s_view`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_viewauthorisations: ~39 rows (ungefähr)
DELETE FROM `rep_nodetypes_viewauthorisations`;
/*!40000 ALTER TABLE `rep_nodetypes_viewauthorisations` DISABLE KEYS */;
INSERT INTO `rep_nodetypes_viewauthorisations` (`fk_nodetype`, `fk_view`, `fk_action`, `fk_authorisation`) VALUES
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
/*!40000 ALTER TABLE `rep_nodetypes_viewauthorisations` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_nodetypes_views
CREATE TABLE IF NOT EXISTS `rep_nodetypes_views` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_view` varchar(50) NOT NULL,
  `b_display` enum('TRUE','FALSE') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'FALSE',
  `s_labelpath` varchar(200) DEFAULT NULL,
  `s_classfile` varchar(100) NOT NULL,
  `s_class` varchar(50) NOT NULL,
  `n_order` int(11) DEFAULT NULL,
  `n_priority` int(11) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_view`),
  CONSTRAINT `rep_nodetypes_views_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_nodetypes_views: ~36 rows (ungefähr)
DELETE FROM `rep_nodetypes_views`;
/*!40000 ALTER TABLE `rep_nodetypes_views` DISABLE KEYS */;
INSERT INTO `rep_nodetypes_views` (`fk_nodetype`, `s_view`, `b_display`, `s_labelpath`, `s_classfile`, `s_class`, `n_order`, `n_priority`) VALUES
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
	('sbSystem:Module', 'installation', 'TRUE', NULL, 'sbSystem:sb.node.module.view.installation', 'sbView_module_installation', 550, 0),
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
/*!40000 ALTER TABLE `rep_nodetypes_views` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_registry
CREATE TABLE IF NOT EXISTS `rep_registry` (
  `s_key` varchar(250) NOT NULL,
  `e_type` enum('string','boolean','integer') NOT NULL DEFAULT 'string',
  `s_internaltype` varchar(250) DEFAULT NULL,
  `b_userspecific` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `s_defaultvalue` varchar(250) NOT NULL,
  `s_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`s_key`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_registry: ~44 rows (ungefähr)
DELETE FROM `rep_registry`;
/*!40000 ALTER TABLE `rep_registry` DISABLE KEYS */;
INSERT INTO `rep_registry` (`s_key`, `e_type`, `s_internaltype`, `b_userspecific`, `s_defaultvalue`, `s_comment`) VALUES
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
	('sb.system.temp.dir', 'string', NULL, 'FALSE', '_temp', NULL);
/*!40000 ALTER TABLE `rep_registry` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.rep_workspaces
CREATE TABLE IF NOT EXISTS `rep_workspaces` (
  `s_workspacename` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `s_workspaceprefix` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `s_user` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `s_pass` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`s_workspacename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.rep_workspaces: ~0 rows (ungefähr)
DELETE FROM `rep_workspaces`;
/*!40000 ALTER TABLE `rep_workspaces` DISABLE KEYS */;
/*!40000 ALTER TABLE `rep_workspaces` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_cache_authorisations
CREATE TABLE IF NOT EXISTS `sb_system_cache_authorisations` (
  `fk_subject` char(32) NOT NULL,
  `fk_entity` char(32) NOT NULL,
  `e_authtype` enum('AGGREGATED','EFFECTIVE') NOT NULL,
  `fk_authorisation` varchar(30) NOT NULL,
  `e_granttype` enum('ALLOW','DENY') NOT NULL,
  PRIMARY KEY (`fk_subject`,`fk_entity`,`e_authtype`,`fk_authorisation`),
  KEY `sb_system_cache_authorisations_fk_en` (`fk_entity`),
  CONSTRAINT `sb_system_cache_authorisations_fk_en` FOREIGN KEY (`fk_entity`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_cache_authorisations_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_cache_authorisations: ~0 rows (ungefähr)
DELETE FROM `sb_system_cache_authorisations`;
/*!40000 ALTER TABLE `sb_system_cache_authorisations` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_cache_authorisations` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_cache_images
CREATE TABLE IF NOT EXISTS `sb_system_cache_images` (
  `fk_image` char(32) NOT NULL,
  `fk_filterstack` char(32) NOT NULL,
  `e_mode` enum('full','explorer','custom') NOT NULL,
  `m_content` longblob,
  PRIMARY KEY (`fk_image`,`fk_filterstack`,`e_mode`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_cache_images: 0 rows
DELETE FROM `sb_system_cache_images`;
/*!40000 ALTER TABLE `sb_system_cache_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_cache_images` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_commands
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

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_commands: ~0 rows (ungefähr)
DELETE FROM `sb_system_commands`;
/*!40000 ALTER TABLE `sb_system_commands` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_commands` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_eventlog
CREATE TABLE IF NOT EXISTS `sb_system_eventlog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_module` varchar(50) CHARACTER SET ascii NOT NULL,
  `s_loguid` varchar(100) NOT NULL,
  `fk_subject` char(32) CHARACTER SET ascii DEFAULT NULL,
  `t_log` text NOT NULL,
  `fk_user` char(32) CHARACTER SET ascii DEFAULT '0',
  `e_type` enum('MAINTENANCE','INFO','ERROR','DEBUG','SECURITY','WARNING') NOT NULL DEFAULT 'INFO',
  `dt_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_eventlog: ~0 rows (ungefähr)
DELETE FROM `sb_system_eventlog`;
/*!40000 ALTER TABLE `sb_system_eventlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_eventlog` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes
CREATE TABLE IF NOT EXISTS `sb_system_nodes` (
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
  KEY `sb_sn_moifiedby` (`fk_modifiedby`),
  KEY `sb_sn_label` (`s_label`),
  KEY `sb_sn_created` (`dt_created`),
  KEY `sb_sn_modified` (`dt_modified`),
  CONSTRAINT `sb_system_nodes_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes: ~144 rows (ungefähr)
DELETE FROM `sb_system_nodes`;
/*!40000 ALTER TABLE `sb_system_nodes` DISABLE KEYS */;
INSERT INTO `sb_system_nodes` (`uuid`, `s_uid`, `fk_nodetype`, `s_label`, `s_name`, `b_inheritrights`, `b_bequeathlocalrights`, `b_bequeathrights`, `fk_createdby`, `fk_modifiedby`, `dt_created`, `dt_modified`, `s_currentlifecyclestate`) VALUES
	('00000000000000000000000000000000', 'sbSystem:Root', 'sbSystem:Root', 'solidMatter', 'root', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', 'a6cdee339f11414b8fa732c7030aab85', '0000-00-00 00:00:00', '2007-11-25 13:34:44', NULL),
	('0080dc1373b84eb0b2096988254ef361', 'sbSystem:Preferences', 'sbSystem:Preferences', '$locale/sbSystem/nodes/preferences', 'jcr_system', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', 'a6cdee339f11414b8fa732c7030aab85', '0000-00-00 00:00:00', '2007-05-13 23:39:39', NULL),
	('0403708141058bcbd4f286eb45c70555', 'sbCMS', 'sbSystem:Module', 'sbCMS', 'sb_cms', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('0d063b3e151a4267852a9f5f5e2a991e', '', 'sbSystem:Reports_Structure', '$locale/sbSystem/nodes/reports_structure', 'structure', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-03-18 23:55:31', '2007-04-24 21:37:49', NULL),
	('1119b9fb4d034dd2870b72ffd769aee2', '', 'sbSystem:Tasks', '$locale/sbSystem/nodes/tasks', 'tasks', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2008-11-09 14:04:05', '2008-11-09 14:04:05', NULL),
	('15663e54221440f0a9d2a3e78d8273b2', '', 'sbSystem:Usergroup', 'Testgroup', 'testgroup', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', 'a6cdee339f11414b8fa732c7030aab85', '0000-00-00 00:00:00', '2009-02-19 00:10:28', NULL),
	('24630e84542b4e30b9c9cb7f3d2c2bc2', '', 'sbSystem:Favorites', '$locale/sbSystem/nodes/favorites', 'favorites', 'TRUE', 'TRUE', 'TRUE', 'f3d916daed12454c995cd156935a822e', 'f3d916daed12454c995cd156935a822e', '2018-08-08 19:54:41', '2018-08-08 19:54:41', NULL),
	('2b49b3e2d1a648a1a80a82c660693c40', 'sbSystem:Reports', 'sbSystem:Reports', '$locale/sbSystem/nodes/reports', 'reports', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('2b49b3e2d1a648a1a80a82c660693c41', 'sbSystem:Reports_DB', 'sbSystem:Reports_DB', '$locale/sbSystem/nodes/reports_database', 'reports_db', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('2ba02334a5e84f54917c30d77da84140', '', 'sbSystem:Debug', '$locale/sbSystem/nodes/debug', 'debug', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-09 23:05:13', '2007-05-09 23:05:13', NULL),
	('2d5ff219b6a74371984e5ce5a0ae2530', '', 'sbSystem:Inbox', '$locale/sbSystem/nodes/inbox', 'inbox', 'TRUE', 'TRUE', 'TRUE', '8ea185a64e394c7c83f098ba6f537073', '8ea185a64e394c7c83f098ba6f537073', '2008-12-20 23:39:38', '2008-12-20 23:39:38', NULL),
	('3ad7cf570897404291ebb391d64e2736', '', 'sbSystem:Favorites', '$locale/sbSystem/nodes/favorites', 'favorites', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2011-09-20 15:33:40', '2011-09-20 15:33:40', NULL),
	('48501470072f4be48f65a1e5ee1146ab', 'sbSystem:Registry', 'sbSystem:Registry', '$locale/sbSystem/nodes/registry', 'registry', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-03-18 13:24:23', '2007-03-18 13:24:23', NULL),
	('4f85c4a0db1244cd8937b6be022569df', '', 'sbSystem:TestNode', 'TestNode', 'testnode', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2009-03-01 10:24:59', '2009-03-01 10:24:59', NULL),
	('50b193ceffa1425d852839b3a34c7376', '', 'sbSystem:Usergroup', 'Users (Jukebox)', 'users_jukebox', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2008-03-07 01:16:11', '2008-03-07 01:16:11', NULL),
	('5172ae3fd7dc2c89766074ff53f7400a', 'sbForum', 'sbSystem:Module', 'sbForum', 'sb_forum', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-06 23:30:59', '2007-05-06 23:30:59', NULL),
	('521621bf529e4206b3c95110efcb3c80', '', 'sbSystem:Tasks', '$locale/sbSystem/nodes/tasks', 'tasks', 'TRUE', 'TRUE', 'TRUE', 'f3d916daed12454c995cd156935a822e', 'f3d916daed12454c995cd156935a822e', '2018-08-08 19:54:41', '2018-08-08 19:54:41', NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', 'sbSystem:Useraccounts', 'sbSystem:Useraccounts', '$locale/sbSystem/nodes/users_groups', 'users', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('86e22252bdea494bacf904674d3711ea', '', 'sbSystem:User', 'Tester', 'test', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', 'a6cdee339f11414b8fa732c7030aab85', '0000-00-00 00:00:00', '2018-07-15 15:38:37', NULL),
	('870606ec4b604af88c4d542f8dda61c6', '', 'sbSystem:Inbox', '$locale/sbSystem/nodes/inbox', 'inbox', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2008-11-09 14:01:58', '2008-11-09 14:01:58', NULL),
	('8ea185a64e394c7c83f098ba6f537073', '', 'sbSystem:User', 'Heiko', 'heiko', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', 'a6cdee339f11414b8fa732c7030aab85', '0000-00-00 00:00:00', '2009-02-18 19:52:34', NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', 'sbSystem:Modules', 'sbSystem:Modules', '$locale/sbSystem/nodes/modules', 'modules', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('969b3b1e5721c24ff5b48d3cedf52fe4', 'sbSystem', 'sbSystem:Module', 'sbSystem', 'sb_system', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-06 23:30:46', '2007-05-06 23:30:46', NULL),
	('96b3319f5b4086db49a4093565bc8d74', 'sbUtilities', 'sbSystem:Module', 'sbUtilities', 'sb_utilities', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-06 23:30:46', '2007-05-06 23:30:46', NULL),
	('9724c30b7f33877ab7c59df1f5af222d', 'sbShop', 'sbSystem:Module', 'sbShop', 'sb_shop', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-06 23:33:06', '2007-05-06 23:33:06', NULL),
	('a2d906a38e0448f1adeb01ef455bbb01', '', 'sbSystem:Logs', '$locale/sbSystem/nodes/logs', 'logs', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-12 15:19:05', '2007-05-12 15:19:05', NULL),
	('a6cdee339f11414b8fa732c7030aab85', '', 'sbSystem:User', '()((()', 'ollo', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', 'a6cdee339f11414b8fa732c7030aab85', '0000-00-00 00:00:00', '2009-02-18 19:52:54', NULL),
	('a8161f9b44144f2b8927e7f515fe4fdf', 'sbSystem:Guests', 'sbSystem:Usergroup', '$locale/sbSystem/nodes/guests', 'guests', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('a83b351baeff4c0c80c68945fb2266cf', 'sbSystem:Admins', 'sbSystem:Usergroup', '$locale/sbSystem/nodes/admins', 'admins', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-03-18 18:01:21', '2007-03-18 18:01:21', NULL),
	('ac8aed8179a4432db7f9850c4def87cc', 'sbPortal', 'sbSystem:Module', 'sbPortal', 'sb_portal', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-06 23:32:52', '2007-05-06 23:32:52', NULL),
	('af86df686cb2d9b8729200e5b6910631', 'sbJukebox', 'sbSystem:Module', 'sbJukebox', 'sb_jukebox', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-10-16 19:29:58', '2007-10-16 19:29:58', NULL),
	('bd14ff30f2b548fbb947a5e663cf1f18', '', 'sbSystem:Inbox', '$locale/sbSystem/nodes/inbox', 'inbox', 'TRUE', 'TRUE', 'TRUE', 'f3d916daed12454c995cd156935a822e', 'f3d916daed12454c995cd156935a822e', '2018-08-08 19:54:41', '2018-08-08 19:54:41', NULL),
	('d4341cc8a61c478d875b11007192e37d', '', 'sbSystem:Tags', '$locale/sbSystem/nodes/tags', 'tags', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2008-04-05 15:35:39', '2008-04-05 15:35:39', NULL),
	('d6c87690b0ee6f725ce1b620c3bed2b3', 'sbNews', 'sbSystem:Module', 'sbNews', 'sb_news', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-10-16 19:37:20', '2007-10-16 19:37:20', NULL),
	('de7762bc4e074f789ac9c1dda4ce40c5', 'sbSystem:Maintenance', 'sbSystem:Maintenance', '$locale/sbSystem/nodes/maintenance', 'maintenance', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('e598ce16e7b2b13af2f5f93c92a6f976', 'sbGuestbook', 'sbSystem:Module', 'sbGuestbook', 'sb_guestbook', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2007-05-06 23:32:52', '2007-05-06 23:32:52', NULL),
	('eaa56c620fab446d9690604303788b34', '', 'sbSystem:Tasks', '$locale/sbSystem/nodes/tasks', 'tasks', 'TRUE', 'TRUE', 'TRUE', '8ea185a64e394c7c83f098ba6f537073', '8ea185a64e394c7c83f098ba6f537073', '2008-12-20 23:39:38', '2008-12-20 23:39:38', NULL),
	('f3d916daed12454c995cd156935a822e', '', 'sbSystem:User', 'Admin', 'admin', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', 'f3d916daed12454c995cd156935a822e', '0000-00-00 00:00:00', '2018-08-08 19:55:02', NULL),
	('f81ddc601c604510a74e7c97f42353a6', 'sbSystem:Trashcan', 'sbSystem:Trashcan', '$locale/sbSystem/nodes/trashcan', 'trashcan', 'TRUE', 'TRUE', 'TRUE', '00000000000000000000000000000000', NULL, '0000-00-00 00:00:00', NULL, NULL),
	('f9a5b08a85c744a0b3fd2ebf14074509', 'sbSystem:Favorites', 'sbSystem:Favorites', '$locale/sbSystem/nodetypes/type[@id=\'sbSystem:Favorites\']', 'favorites', 'TRUE', 'TRUE', 'TRUE', 'a6cdee339f11414b8fa732c7030aab85', 'a6cdee339f11414b8fa732c7030aab85', '2010-09-20 21:37:24', '2010-09-20 21:37:24', NULL);
/*!40000 ALTER TABLE `sb_system_nodes` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_authorisation
CREATE TABLE IF NOT EXISTS `sb_system_nodes_authorisation` (
  `fk_subject` char(32) NOT NULL,
  `fk_authorisation` varchar(30) NOT NULL,
  `fk_userentity` char(32) NOT NULL DEFAULT '0',
  `e_granttype` enum('ALLOW','DENY') NOT NULL DEFAULT 'ALLOW',
  PRIMARY KEY (`fk_subject`,`fk_authorisation`,`fk_userentity`),
  KEY `fk_entity` (`fk_userentity`),
  CONSTRAINT `sb_system_nodes_authorisation_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_nodes_authorisation_fk_un` FOREIGN KEY (`fk_userentity`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_authorisation: ~16 rows (ungefähr)
DELETE FROM `sb_system_nodes_authorisation`;
/*!40000 ALTER TABLE `sb_system_nodes_authorisation` DISABLE KEYS */;
INSERT INTO `sb_system_nodes_authorisation` (`fk_subject`, `fk_authorisation`, `fk_userentity`, `e_granttype`) VALUES
	('00000000000000000000000000000000', 'read', '15663e54221440f0a9d2a3e78d8273b2', 'ALLOW'),
	('00000000000000000000000000000000', 'write', '15663e54221440f0a9d2a3e78d8273b2', 'ALLOW'),
	('2b49b3e2d1a648a1a80a82c660693c41', 'read', 'a83b351baeff4c0c80c68945fb2266cf', 'ALLOW'),
	('2b49b3e2d1a648a1a80a82c660693c41', 'write', 'a83b351baeff4c0c80c68945fb2266cf', 'ALLOW');
/*!40000 ALTER TABLE `sb_system_nodes_authorisation` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_locks
CREATE TABLE IF NOT EXISTS `sb_system_nodes_locks` (
  `fk_lockednode` char(32) NOT NULL,
  `fk_user` char(32) NOT NULL,
  `s_sessionid` char(32) NOT NULL,
  `b_deep` enum('TRUE','FALSE') DEFAULT 'FALSE',
  `dt_placed` datetime DEFAULT '0000-00-00 00:00:00',
  `n_timetolive` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_lockednode`),
  KEY `sb_snl_user` (`fk_user`),
  CONSTRAINT `sb_system_nodes_locks_fk_n` FOREIGN KEY (`fk_lockednode`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_nodes_locks_fk_u` FOREIGN KEY (`fk_user`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_locks: ~0 rows (ungefähr)
DELETE FROM `sb_system_nodes_locks`;
/*!40000 ALTER TABLE `sb_system_nodes_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_nodes_locks` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_parents
CREATE TABLE IF NOT EXISTS `sb_system_nodes_parents` (
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
  CONSTRAINT `sb_system_nodes_parents_fk_cn` FOREIGN KEY (`fk_child`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_nodes_parents_fk_pn` FOREIGN KEY (`fk_parent`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_parents: ~147 rows (ungefähr)
DELETE FROM `sb_system_nodes_parents`;
/*!40000 ALTER TABLE `sb_system_nodes_parents` DISABLE KEYS */;
INSERT INTO `sb_system_nodes_parents` (`fk_parent`, `fk_child`, `b_primary`, `n_order`, `n_level`, `s_mpath`, `fk_deletedby`, `dt_deleted`) VALUES
	('00000000000000000000000000000000', '00000000000000000000000000000000', 'TRUE', 1, 0, NULL, NULL, NULL),
	('00000000000000000000000000000000', '0080dc1373b84eb0b2096988254ef361', 'TRUE', 8, 1, '068a1', NULL, NULL),
	('00000000000000000000000000000000', 'f81ddc601c604510a74e7c97f42353a6', 'TRUE', 9, 1, '068a1', NULL, NULL),
	('00000000000000000000000000000000', 'f9a5b08a85c744a0b3fd2ebf14074509', 'TRUE', 0, 1, '068a1', NULL, NULL),
	('0080dc1373b84eb0b2096988254ef361', '2b49b3e2d1a648a1a80a82c660693c40', 'TRUE', 0, 2, '068a1e14e7', NULL, NULL),
	('0080dc1373b84eb0b2096988254ef361', '7a4579077e6b4dada6ee610ff589a8ce', 'TRUE', 1, 2, '068a1e14e7', NULL, NULL),
	('0080dc1373b84eb0b2096988254ef361', '8f1d4495a5b9461a9f09bd4e24410d34', 'TRUE', 2, 2, '068a1e14e7', NULL, NULL),
	('0080dc1373b84eb0b2096988254ef361', 'de7762bc4e074f789ac9c1dda4ce40c5', 'TRUE', 3, 2, '068a1e14e7', NULL, NULL),
	('15663e54221440f0a9d2a3e78d8273b2', '86e22252bdea494bacf904674d3711ea', 'FALSE', 0, 4, '068a1e14e7664e4cf35f', NULL, NULL),
	('2b49b3e2d1a648a1a80a82c660693c40', '0d063b3e151a4267852a9f5f5e2a991e', 'TRUE', 1, 3, '068a1e14e7a709b', NULL, NULL),
	('2b49b3e2d1a648a1a80a82c660693c40', '2b49b3e2d1a648a1a80a82c660693c41', 'TRUE', 0, 3, '068a1e14e7a709b', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', '15663e54221440f0a9d2a3e78d8273b2', 'TRUE', 0, 3, '068a1e14e7664e4', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', '50b193ceffa1425d852839b3a34c7376', 'TRUE', 5, 3, '068a1e14e7664e4', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', '86e22252bdea494bacf904674d3711ea', 'TRUE', 1, 3, '068a1e14e7664e4', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', '8ea185a64e394c7c83f098ba6f537073', 'TRUE', 2, 3, '068a1e14e7664e4', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', 'a6cdee339f11414b8fa732c7030aab85', 'TRUE', 3, 3, '068a1e14e7664e4', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', 'a8161f9b44144f2b8927e7f515fe4fdf', 'TRUE', 4, 3, '068a1e14e7664e4', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', 'a83b351baeff4c0c80c68945fb2266cf', 'TRUE', 7, 3, '068a1e14e7664e4', NULL, NULL),
	('7a4579077e6b4dada6ee610ff589a8ce', 'f3d916daed12454c995cd156935a822e', 'TRUE', 6, 3, '068a1e14e7664e4', NULL, NULL),
	('8ea185a64e394c7c83f098ba6f537073', '2d5ff219b6a74371984e5ce5a0ae2530', 'TRUE', 0, 5, '068a1e14e7664e4f30f712f5f', NULL, NULL),
	('8ea185a64e394c7c83f098ba6f537073', 'eaa56c620fab446d9690604303788b34', 'TRUE', 1, 5, '068a1e14e7664e4f30f712f5f', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', '0403708141058bcbd4f286eb45c70555', 'TRUE', 0, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', '5172ae3fd7dc2c89766074ff53f7400a', 'TRUE', 2, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', '969b3b1e5721c24ff5b48d3cedf52fe4', 'TRUE', 1, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', '96b3319f5b4086db49a4093565bc8d74', 'TRUE', 7, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', '9724c30b7f33877ab7c59df1f5af222d', 'TRUE', 4, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', 'ac8aed8179a4432db7f9850c4def87cc', 'TRUE', 8, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', 'af86df686cb2d9b8729200e5b6910631', 'TRUE', 5, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', 'd6c87690b0ee6f725ce1b620c3bed2b3', 'TRUE', 6, 3, '068a1e14e795ae5', NULL, NULL),
	('8f1d4495a5b9461a9f09bd4e24410d34', 'e598ce16e7b2b13af2f5f93c92a6f976', 'TRUE', 3, 3, '068a1e14e795ae5', NULL, NULL),
	('a6cdee339f11414b8fa732c7030aab85', '1119b9fb4d034dd2870b72ffd769aee2', 'TRUE', 1, 5, '068a1e14e7664e4f30f72f7b5', NULL, NULL),
	('a6cdee339f11414b8fa732c7030aab85', '3ad7cf570897404291ebb391d64e2736', 'TRUE', 3, 5, '068a1e14e7664e4f30f72f7b5', NULL, NULL),
	('a6cdee339f11414b8fa732c7030aab85', '870606ec4b604af88c4d542f8dda61c6', 'TRUE', 0, 5, '068a1e14e7664e4f30f72f7b5', NULL, NULL),
	('a83b351baeff4c0c80c68945fb2266cf', '8ea185a64e394c7c83f098ba6f537073', 'FALSE', 2, 4, '068a1e14e7664e4f30f7', NULL, NULL),
	('a83b351baeff4c0c80c68945fb2266cf', 'a6cdee339f11414b8fa732c7030aab85', 'FALSE', 0, 4, '068a1e14e7664e4f30f7', NULL, NULL),
	('a83b351baeff4c0c80c68945fb2266cf', 'f3d916daed12454c995cd156935a822e', 'FALSE', 1, 4, '068a1e14e7664e4f30f7', NULL, NULL),
	('de7762bc4e074f789ac9c1dda4ce40c5', '2ba02334a5e84f54917c30d77da84140', 'TRUE', 0, 3, '068a1e14e743a29', NULL, NULL),
	('de7762bc4e074f789ac9c1dda4ce40c5', '48501470072f4be48f65a1e5ee1146ab', 'TRUE', 2, 3, '068a1e14e743a29', NULL, NULL),
	('de7762bc4e074f789ac9c1dda4ce40c5', 'a2d906a38e0448f1adeb01ef455bbb01', 'TRUE', 1, 3, '068a1e14e743a29', NULL, NULL),
	('de7762bc4e074f789ac9c1dda4ce40c5', 'd4341cc8a61c478d875b11007192e37d', 'TRUE', 3, 3, '068a1e14e743a29', NULL, NULL),
	('f3d916daed12454c995cd156935a822e', '24630e84542b4e30b9c9cb7f3d2c2bc2', 'TRUE', 0, 5, '068a1e14e7664e4f30f7fec0c', NULL, NULL),
	('f3d916daed12454c995cd156935a822e', '521621bf529e4206b3c95110efcb3c80', 'TRUE', 2, 5, '068a1e14e7664e4f30f7fec0c', NULL, NULL),
	('f3d916daed12454c995cd156935a822e', 'bd14ff30f2b548fbb947a5e663cf1f18', 'TRUE', 1, 5, '068a1e14e7664e4f30f7fec0c', NULL, NULL);
/*!40000 ALTER TABLE `sb_system_nodes_parents` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_properties
CREATE TABLE IF NOT EXISTS `sb_system_nodes_properties` (
  `fk_node` char(32) NOT NULL DEFAULT '0',
  `fk_attributename` varchar(100) NOT NULL,
  `fk_version` char(32) NOT NULL DEFAULT '',
  `m_content` longblob,
  PRIMARY KEY (`fk_node`,`fk_attributename`,`fk_version`),
  KEY `sb_snpr_node` (`fk_node`),
  CONSTRAINT `sb_system_nodes_properties_fk_n` FOREIGN KEY (`fk_node`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_properties: ~407 rows (ungefähr)
DELETE FROM `sb_system_nodes_properties`;
/*!40000 ALTER TABLE `sb_system_nodes_properties` DISABLE KEYS */;
INSERT INTO `sb_system_nodes_properties` (`fk_node`, `fk_attributename`, `fk_version`, `m_content`) VALUES
	('15663e54221440f0a9d2a3e78d8273b2', 'config_default', '', _binary 0x46414C5345),
	('15663e54221440f0a9d2a3e78d8273b2', 'config_hidden', '', _binary 0x46414C5345),
	('15663e54221440f0a9d2a3e78d8273b2', 'properties_description', '', _binary ''),
	('50b193ceffa1425d852839b3a34c7376', 'config_default', '', _binary 0x46414C5345),
	('50b193ceffa1425d852839b3a34c7376', 'config_hidden', '', _binary 0x46414C5345),
	('50b193ceffa1425d852839b3a34c7376', 'properties_description', '', _binary '');
/*!40000 ALTER TABLE `sb_system_nodes_properties` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_properties_binary
CREATE TABLE IF NOT EXISTS `sb_system_nodes_properties_binary` (
  `fk_node` char(32) NOT NULL DEFAULT '0',
  `fk_attributename` varchar(100) NOT NULL,
  `fk_version` char(255) NOT NULL DEFAULT '',
  `m_content` longblob,
  PRIMARY KEY (`fk_node`,`fk_attributename`,`fk_version`),
  KEY `fk_node` (`fk_node`),
  CONSTRAINT `sb_system_nodes_properties_binary_fk_n` FOREIGN KEY (`fk_node`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_properties_binary: ~11 rows (ungefähr)
DELETE FROM `sb_system_nodes_properties_binary`;
/*!40000 ALTER TABLE `sb_system_nodes_properties_binary` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_nodes_properties_binary` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_relations
CREATE TABLE IF NOT EXISTS `sb_system_nodes_relations` (
  `fk_entity1` char(32) CHARACTER SET ascii NOT NULL,
  `s_relation` varchar(50) CHARACTER SET ascii NOT NULL,
  `fk_entity2` char(32) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`fk_entity1`,`s_relation`,`fk_entity2`),
  KEY `sb_srel_target` (`fk_entity2`),
  CONSTRAINT `sb_system_nodes_relations_fk_sn` FOREIGN KEY (`fk_entity1`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_nodes_relations_fk_tn` FOREIGN KEY (`fk_entity2`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_relations: ~0 rows (ungefähr)
DELETE FROM `sb_system_nodes_relations`;
/*!40000 ALTER TABLE `sb_system_nodes_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_nodes_relations` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_tags
CREATE TABLE IF NOT EXISTS `sb_system_nodes_tags` (
  `fk_subject` char(32) NOT NULL,
  `fk_tag` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`fk_subject`,`fk_tag`),
  KEY `sb_snt_tag` (`fk_tag`),
  CONSTRAINT `sb_system_nodes_tags_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_nodes_tags_fk_t` FOREIGN KEY (`fk_tag`) REFERENCES `sb_system_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_tags: ~0 rows (ungefähr)
DELETE FROM `sb_system_nodes_tags`;
/*!40000 ALTER TABLE `sb_system_nodes_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_nodes_tags` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_nodes_votes
CREATE TABLE IF NOT EXISTS `sb_system_nodes_votes` (
  `fk_subject` char(32) NOT NULL,
  `fk_user` char(32) NOT NULL,
  `n_vote` tinyint(4) NOT NULL,
  PRIMARY KEY (`fk_subject`,`fk_user`),
  KEY `sb_snv_user` (`fk_user`,`fk_subject`),
  CONSTRAINT `sb_system_nodes_votes_fk_sn` FOREIGN KEY (`fk_subject`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sb_system_nodes_votes_fk_u` FOREIGN KEY (`fk_user`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_nodes_votes: ~0 rows (ungefähr)
DELETE FROM `sb_system_nodes_votes`;
/*!40000 ALTER TABLE `sb_system_nodes_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_nodes_votes` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_progress
CREATE TABLE IF NOT EXISTS `sb_system_progress` (
  `fk_user` char(32) NOT NULL,
  `fk_subject` char(32) NOT NULL,
  `s_uid` varchar(30) NOT NULL,
  `s_status` varchar(250) DEFAULT NULL,
  `n_percentage` int(11) DEFAULT NULL,
  PRIMARY KEY (`fk_user`,`fk_subject`,`s_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_progress: ~0 rows (ungefähr)
DELETE FROM `sb_system_progress`;
/*!40000 ALTER TABLE `sb_system_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_progress` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_registry_values
CREATE TABLE IF NOT EXISTS `sb_system_registry_values` (
  `s_key` varchar(250) NOT NULL,
  `fk_user` char(32) NOT NULL,
  `s_value` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`s_key`,`fk_user`),
  CONSTRAINT `sb_system_registry_values_fk_rk` FOREIGN KEY (`s_key`) REFERENCES `rep_registry` (`s_key`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_registry_values: ~0 rows (ungefähr)
DELETE FROM `sb_system_registry_values`;
/*!40000 ALTER TABLE `sb_system_registry_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_registry_values` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_tags
CREATE TABLE IF NOT EXISTS `sb_system_tags` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `s_tag` varchar(100) NOT NULL,
  `n_popularity` int(10) unsigned NOT NULL,
  `n_customweight` smallint(10) unsigned NOT NULL,
  `e_visibility` enum('VISIBLE','HIDDEN') NOT NULL DEFAULT 'VISIBLE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_tags: ~53 rows (ungefähr)
DELETE FROM `sb_system_tags`;
/*!40000 ALTER TABLE `sb_system_tags` DISABLE KEYS */;
INSERT INTO `sb_system_tags` (`id`, `s_tag`, `n_popularity`, `n_customweight`, `e_visibility`) VALUES
	(1, 'Genre:Pop', 0, 0, 'VISIBLE'),
	(2, 'Encoding:320kbs', 0, 0, 'VISIBLE'),
	(3, 'Encoding:CBR', 0, 0, 'VISIBLE'),
	(4, 'Year:2009', 0, 0, 'VISIBLE'),
	(5, 'Genre:Deutsch', 0, 0, 'VISIBLE'),
	(6, 'Encoding:128kbs', 0, 0, 'VISIBLE'),
	(7, 'Year:1995', 0, 0, 'VISIBLE'),
	(8, 'Genre:Hip-Hop', 0, 0, 'VISIBLE'),
	(9, 'Encoding:192kbs', 0, 0, 'VISIBLE'),
	(10, 'Year:2003', 0, 0, 'VISIBLE'),
	(11, 'Encoding:VBR', 0, 0, 'VISIBLE'),
	(12, 'Year:2006', 0, 0, 'VISIBLE'),
	(13, 'Genre:Electronic', 0, 0, 'VISIBLE'),
	(14, 'Bootleg', 0, 0, 'VISIBLE'),
	(15, 'Genre:Rock', 0, 0, 'VISIBLE'),
	(16, 'Year:1970', 0, 0, 'VISIBLE'),
	(17, 'Encoding:160kbs', 0, 0, 'VISIBLE'),
	(18, 'Encoding:224kbs', 0, 0, 'VISIBLE'),
	(19, 'Year:2004', 0, 0, 'VISIBLE'),
	(20, 'Year:2005', 0, 0, 'VISIBLE'),
	(21, 'Encoding:256kbs', 0, 0, 'VISIBLE'),
	(22, 'Year:1996', 0, 0, 'VISIBLE'),
	(23, 'Genre:Electronica', 0, 0, 'VISIBLE'),
	(24, 'Year:2007', 0, 0, 'VISIBLE'),
	(25, 'Genre:Remix', 0, 0, 'VISIBLE'),
	(26, 'Genre:Drum & Bass', 0, 0, 'VISIBLE'),
	(27, 'DJ-Set', 0, 0, 'VISIBLE'),
	(28, 'Special Edition', 0, 0, 'VISIBLE'),
	(29, 'Year:1998', 0, 0, 'VISIBLE'),
	(30, 'Year:1999', 0, 0, 'VISIBLE'),
	(31, 'Year:2002', 0, 0, 'VISIBLE'),
	(32, 'Year:1979', 0, 0, 'VISIBLE'),
	(33, 'Year:2001', 0, 0, 'VISIBLE'),
	(34, 'Defects:incomplete', 0, 0, 'VISIBLE'),
	(35, 'Year:2000', 0, 0, 'VISIBLE'),
	(36, 'Genre:Metal', 0, 0, 'VISIBLE'),
	(37, 'Genre:Indie', 0, 0, 'VISIBLE'),
	(38, 'Deutsch', 0, 0, 'VISIBLE'),
	(39, 'Free', 0, 0, 'VISIBLE'),
	(40, 'Year:2008', 0, 0, 'VISIBLE'),
	(41, 'Year:1990', 0, 0, 'VISIBLE'),
	(42, 'Year:1997', 0, 0, 'VISIBLE'),
	(43, 'Maxi', 0, 0, 'VISIBLE'),
	(44, 'Remix', 0, 0, 'VISIBLE'),
	(45, 'Genre:Ska', 0, 0, 'VISIBLE'),
	(46, 'Bayrisch', 0, 0, 'VISIBLE'),
	(47, 'Mixtape', 0, 0, 'VISIBLE'),
	(48, 'Year:2010', 0, 0, 'VISIBLE'),
	(49, 'Genre:Dubstep', 0, 0, 'VISIBLE'),
	(50, 'Genre:Synthwave', 0, 0, 'VISIBLE'),
	(51, 'Year:2013', 0, 0, 'VISIBLE'),
	(52, 'Year:2011', 0, 0, 'VISIBLE'),
	(53, 'Year:2015', 0, 0, 'VISIBLE');
/*!40000 ALTER TABLE `sb_system_tags` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_useraccounts
CREATE TABLE IF NOT EXISTS `sb_system_useraccounts` (
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
  CONSTRAINT `sb_system_useraccounts_fk_n` FOREIGN KEY (`uuid`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_useraccounts: ~4 rows (ungefähr)
DELETE FROM `sb_system_useraccounts`;
/*!40000 ALTER TABLE `sb_system_useraccounts` DISABLE KEYS */;
INSERT INTO `sb_system_useraccounts` (`uuid`, `s_password`, `s_email`, `s_activationkey`, `t_comment`, `n_failedlogins`, `b_activated`, `b_stayloggedin`, `b_locked`, `b_emailsent`, `dt_activatedat`, `dt_currentlogin`, `dt_lastlogin`, `dt_failedlogin`, `n_totalfailedlogins`, `n_successfullogins`, `n_silentlogins`, `b_hidestatus`, `b_backendaccess`, `dt_expires`) VALUES
	('86e22252bdea494bacf904674d3711ea', 'sha1:41ea86dd98bd2e9c48ec9613b29ab7f47f2ce519:410ec470ce462ca74af4aea2994a0c0eda981466', 'tester@nodomain.test', NULL, 'Der Tester!', 0, 'TRUE', 'TRUE', 'FALSE', 'FALSE', NULL, '2007-05-17 14:14:28', '2007-05-17 14:13:08', NULL, 0, 9, 0, 'TRUE', 'TRUE', NULL),
	('8ea185a64e394c7c83f098ba6f537073', 'sha1:5bce71208c886cb2234707b2e55a6bd9bb86021d:1e241601aa2d317c546afeb88ead5008a8022bf8', 'hthiery@nodomain.test', NULL, NULL, 0, 'TRUE', 'TRUE', 'FALSE', 'TRUE', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 0, 0, 0, 'TRUE', 'TRUE', NULL),
	('a6cdee339f11414b8fa732c7030aab85', 'sha1:5bce71208c886cb2234707b2e55a6bd9bb86021d:1e241601aa2d317c546afeb88ead5008a8022bf8', 'om@nodomain.test', NULL, NULL, 0, 'TRUE', 'TRUE', 'FALSE', 'FALSE', '0000-00-00 00:00:00', '2018-08-08 21:07:10', '2018-08-08 20:15:34', NULL, 2, 1232, 142, 'FALSE', 'TRUE', NULL),
	('f3d916daed12454c995cd156935a822e', 'sha1:5bce71208c886cb2234707b2e55a6bd9bb86021d:1e241601aa2d317c546afeb88ead5008a8022bf8', 'admin@localhost.pc', NULL, 'This is the system administrator, he owns all permissions!\r\nDo not delete unless you have created another Superuser!!!', 0, 'TRUE', 'FALSE', 'FALSE', 'TRUE', NULL, '2018-08-08 21:07:10', '2018-08-08 20:15:34', NULL, 0, 48, 0, 'TRUE', 'TRUE', NULL);
/*!40000 ALTER TABLE `sb_system_useraccounts` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle solidmatter_naked.sb_system_whosonline
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

-- Exportiere Daten aus Tabelle solidmatter_naked.sb_system_whosonline: 0 rows
DELETE FROM `sb_system_whosonline`;
/*!40000 ALTER TABLE `sb_system_whosonline` DISABLE KEYS */;
/*!40000 ALTER TABLE `sb_system_whosonline` ENABLE KEYS */;

-- Exportiere Struktur von View solidmatter_naked.sb_view_nodes_in_trashcan
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `sb_view_nodes_in_trashcan` (
	`uuid` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`s_uid` VARCHAR(30) NULL COLLATE 'ascii_general_ci',
	`fk_nodetype` VARCHAR(40) NOT NULL COLLATE 'utf8_general_ci',
	`s_label` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci',
	`s_name` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`fk_child` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`fk_parent` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`b_primary` ENUM('TRUE','FALSE') NOT NULL COLLATE 'ascii_general_ci',
	`fk_deletedby` CHAR(32) NULL COLLATE 'ascii_general_ci',
	`dt_deleted` DATETIME NULL,
	`s_mpath` VARCHAR(255) NULL COLLATE 'ascii_general_ci'
) ENGINE=MyISAM;

-- Exportiere Struktur von View solidmatter_naked.sb_view_nodes_without_parents
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `sb_view_nodes_without_parents` (
	`uuid` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`s_label` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci',
	`s_name` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`fk_nodetype` VARCHAR(40) NOT NULL COLLATE 'utf8_general_ci',
	`num_children` BIGINT(21) NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View solidmatter_naked.sb_view_nodes_with_multiple_parents
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `sb_view_nodes_with_multiple_parents` (
	`fk_child` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`fk_parent` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`b_primary` ENUM('TRUE','FALSE') NOT NULL COLLATE 'ascii_general_ci',
	`n_numparents` BIGINT(21) NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View solidmatter_naked.sb_view_parents_children
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `sb_view_parents_children` (
	`parent_nt` VARCHAR(40) NOT NULL COLLATE 'utf8_general_ci',
	`parent_name` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`parent_label` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci',
	`fk_parent` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`fk_child` CHAR(32) NOT NULL COLLATE 'ascii_general_ci',
	`child_label` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci',
	`child_name` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`child_nt` VARCHAR(40) NOT NULL COLLATE 'utf8_general_ci',
	`b_primary` ENUM('TRUE','FALSE') NOT NULL COLLATE 'ascii_general_ci',
	`n_order` MEDIUMINT(10) UNSIGNED NULL,
	`n_level` SMALLINT(10) UNSIGNED NULL
) ENGINE=MyISAM;

-- Exportiere Struktur von View solidmatter_naked.sb_view_nodes_in_trashcan
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `sb_view_nodes_in_trashcan`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sb_view_nodes_in_trashcan` AS select `sb_system_nodes`.`uuid` AS `uuid`,`sb_system_nodes`.`s_uid` AS `s_uid`,`sb_system_nodes`.`fk_nodetype` AS `fk_nodetype`,`sb_system_nodes`.`s_label` AS `s_label`,`sb_system_nodes`.`s_name` AS `s_name`,`sb_system_nodes_parents`.`fk_child` AS `fk_child`,`sb_system_nodes_parents`.`fk_parent` AS `fk_parent`,`sb_system_nodes_parents`.`b_primary` AS `b_primary`,`sb_system_nodes_parents`.`fk_deletedby` AS `fk_deletedby`,`sb_system_nodes_parents`.`dt_deleted` AS `dt_deleted`,`sb_system_nodes_parents`.`s_mpath` AS `s_mpath` from (`sb_system_nodes` join `sb_system_nodes_parents` on((`sb_system_nodes_parents`.`fk_child` = `sb_system_nodes`.`uuid`))) where ((`sb_system_nodes_parents`.`fk_deletedby` is not null) or (substr(`sb_system_nodes_parents`.`s_mpath`,1,8) = _ascii'DELETED_')) order by `sb_system_nodes_parents`.`s_mpath` ;

-- Exportiere Struktur von View solidmatter_naked.sb_view_nodes_without_parents
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `sb_view_nodes_without_parents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sb_view_nodes_without_parents` AS SELECT n.uuid,
		n.s_label,
		n.s_name,
		n.fk_nodetype,
		(select count(*) from sb_system_nodes_parents np where np.fk_parent = n.uuid) as num_children		
FROM	sb_system_nodes n
WHERE (select count(*) from sb_system_nodes_parents np where np.fk_child = n.uuid) = 0 ;

-- Exportiere Struktur von View solidmatter_naked.sb_view_nodes_with_multiple_parents
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `sb_view_nodes_with_multiple_parents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sb_view_nodes_with_multiple_parents` AS select `np`.`fk_child` AS `fk_child`,`np`.`fk_parent` AS `fk_parent`,`np`.`b_primary` AS `b_primary`,(select count(0) AS `count(*)` from `sb_system_nodes_parents` where (`sb_system_nodes_parents`.`fk_child` = `np`.`fk_child`)) AS `n_numparents` from `sb_system_nodes_parents` `np` where ((select count(0) AS `count(*)` from `sb_system_nodes_parents` where (`sb_system_nodes_parents`.`fk_child` = `np`.`fk_child`)) > 1) ;

-- Exportiere Struktur von View solidmatter_naked.sb_view_parents_children
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `sb_view_parents_children`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sb_view_parents_children` AS select `parents`.`fk_nodetype` AS `parent_nt`,`parents`.`s_name` AS `parent_name`,`parents`.`s_label` AS `parent_label`,`sb_system_nodes_parents`.`fk_parent` AS `fk_parent`,`sb_system_nodes_parents`.`fk_child` AS `fk_child`,`children`.`s_label` AS `child_label`,`children`.`s_name` AS `child_name`,`children`.`fk_nodetype` AS `child_nt`,`sb_system_nodes_parents`.`b_primary` AS `b_primary`,`sb_system_nodes_parents`.`n_order` AS `n_order`,`sb_system_nodes_parents`.`n_level` AS `n_level` from ((`sb_system_nodes` `children` join `sb_system_nodes_parents` on((`children`.`uuid` = `sb_system_nodes_parents`.`fk_child`))) join `sb_system_nodes` `parents` on((`parents`.`uuid` = `sb_system_nodes_parents`.`fk_parent`))) ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
