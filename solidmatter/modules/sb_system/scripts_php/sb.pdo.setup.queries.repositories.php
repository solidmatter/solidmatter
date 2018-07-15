<?php

global $_QUERIES;

// repository administration ---------------------------------------------------

$_QUERIES['sbCR/repository/create'] = "

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
  PRIMARY KEY (`uuid`),
  -- CONSTRAINT `{TABLE_MODULES}_fk_module` FOREIGN KEY (`uuid`) REFERENCES `sb_system_nodes` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
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
  KEY `fk_nti_2` (`fk_childnodetype`),
  CONSTRAINT `{TABLE_NTHIERARCHY}_fk_cnt` FOREIGN KEY (`fk_parentnodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_NTHIERARCHY}_fk_pnt` FOREIGN KEY (`fk_childnodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rep_nodetypes_lifecycles` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_state` varchar(50) NOT NULL,
  `s_statetransition` varchar(50) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_state`,`s_statetransition`),
  CONSTRAINT `rep_nodetypes_lifecycles_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rep_nodetypes_mimetypemapping` (
  `s_mimetype` varchar(50) NOT NULL,
  `fk_nodetype` varchar(40) NOT NULL,
  PRIMARY KEY (`s_mimetype`),
  KEY `rep_ntmime1` (`fk_nodetype`),
  CONSTRAINT `rep_nodetypes_mimetypemapping_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_MODES}` (
  `s_mode` varchar(30) NOT NULL,
  `fk_parentnodetype` varchar(40) NOT NULL,
  `fk_nodetype` varchar(40) NOT NULL,
  `b_display` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  `b_choosable` enum('TRUE','FALSE') CHARACTER SET ascii NOT NULL DEFAULT 'TRUE',
  PRIMARY KEY (`fk_nodetype`,`fk_parentnodetype`,`s_mode`),
  KEY `parentnodetype` (`fk_parentnodetype`),
  KEY `mode_parentnodetype` (`s_mode`,`fk_parentnodetype`),
  CONSTRAINT `{TABLE_MODES}_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `{TABLE_MODES}_fk_pnt` FOREIGN KEY (`fk_parentnodetype`) REFERENCES `{TABLE_NODETYPES}` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{TABLE_ONTOLOGY}` (
  `s_relation` varchar(50) CHARACTER SET ascii NOT NULL,
  `fk_sourcenodetype` varchar(40) CHARACTER SET utf8 NOT NULL,
  `fk_targetnodetype` varchar(40) CHARACTER SET utf8 NOT NULL,
  `s_reverserelation` varchar(50) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`s_relation`,`fk_sourcenodetype`,`fk_targetnodetype`),
  KEY `rep_nto_source` (`fk_sourcenodetype`),
  KEY `rep_nto_target` (`fk_targetnodetype`),
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
  KEY `rep_ntva2` (`fk_nodetype`,`fk_authorisation`),
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

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

";

$_QUERIES['sbCR/inactive'] = "

CREATE TABLE IF NOT EXISTS `rep_nodetypes_dimensions` (
  `fk_nodetype` varchar(40) NOT NULL,
  `s_dimension` varchar(50) NOT NULL,
  `n_steps` int(11) NOT NULL,
  PRIMARY KEY (`fk_nodetype`,`s_dimension`),
  CONSTRAINT `rep_nodetypes_dimensions_fk_nt` FOREIGN KEY (`fk_nodetype`) REFERENCES `rep_nodetypes` (`s_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rep_workspaces` (
  `s_workspacename` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `s_workspaceprefix` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `s_user` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `s_pass` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`s_workspacename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

";



?>