<?php

//------------------------------------------------------------------------------
/**
 *	@package solidMatter
 *	@author	()((() [Oliver M�ller]
 *	@version 1.00.00
 */
//------------------------------------------------------------------------------

if (!defined('TIMEZONE'))			define('TIMEZONE', 'Europe/Berlin');
if (!defined('ERROR_REPORTING'))	define('ERROR_REPORTING', TRUE);
if (!defined('USE_SSL'))			define('USE_SSL', FALSE);

// here you can add or change various configuration steps that should be applicable for all requests that are routed through solidMatter.
ini_set('opcache.enable', 0);
ERROR_REPORTING ? error_reporting(E_ALL) : error_reporting(0);
mb_internal_encoding('UTF-8');
date_default_timezone_set(TIMEZONE);

//-----------------------------------------------------------------------------
/**
 *
 *
 *
 */
class CONFIG {
	
	// storage information for solidMatter config XML
	const DIR = '_config/';
	const FILE = 'configuration.xml';
	
	// directory for temporary files (e.g. uploads)
	const TEMPDIR = '_temp/';
	
	// directory for various logs (relative to solidMatter root or absolute path, see LOGDIR_ABS)
	const LOGDIR = '_logs/';
	// true if LOGDIR is an absolute path (ToDo: find more elegant solution, error_log expects absolute path)
	const LOGDIR_ABS = FALSE;
	
	// debugging flags
	const DEBUG = array(
		'ENABLED' 		=> FALSE,
		'LOG_ALL' 		=> FALSE,
		'BASIC'			=> TRUE,
		'CLIENT'		=> FALSE,
		'IMPORT'		=> FALSE,
		'SESSION'		=> FALSE,
		'REQUEST'		=> TRUE,
		'HANDLER'		=> FALSE,
		'NODE'			=> FALSE,
		'REDIRECT'		=> FALSE,
		'EXCEPTIONS'	=> TRUE,
		'PDO'			=> FALSE,
	);
	
	// output prettyprinted XML
	const PRETTYPRINT = TRUE;
	
	// sbSystem Registry Cache, activates necessary change detection
	const USE_REGISTRYCACHE = TRUE;
	
	// API key for the Songkick webservice
	const KEY_SONGKICK = 'jhDU4U3JHdui256Fs';
	
	// file/class used for configuration reading
	static $CONFIGURATION_READER_LIBRARY = 'modules/sb_system/scripts_php/sb.system.configuration.reader.default.php';
	
	//-------------------------------------------------------------------------
	/**
	 * Initializes the CONFIG class, e.g. loading additional values from config files.
	 * Currently only loads the configuration XML at DIR.FILE and stores it.
	 */
	static function init() {
		require_once(self::$CONFIGURATION_READER_LIBRARY);
		sbConfigurationReader::init();
	}
	
	// wrapped functions
	static function getSiteConfig(string $sSitePath) {
		return (sbConfigurationReader::getSiteConfig($sSitePath));
	}
	static function getHandlerConfig(string $sHandlerID) {
		return (sbConfigurationReader::getHandlerConfig($sHandlerID));
	}
	static function getRepositoryConfig(string $sRepositoryID = NULL) {
		return (sbConfigurationReader::getRepositoryConfig($sRepositoryID));
	}
	static function getDatabaseConfig(string $sDatabaseID = NULL) {
		return (sbConfigurationReader::getDatabaseConfig($sDatabaseID));
	}
	
}

// immediately initialize the configuration class
CONFIG::init();

?>