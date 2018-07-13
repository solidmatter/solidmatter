<?php

//ini_set('opcache.enable', 0);

class CONFIG {
	
	// storage information for solidMatter config XMLs
	const DIR = '_config/';
	const INTERFACE = 'interface.xml';
	const CONTROLLER = 'controller.xml';
	const REPOSITORIES = 'repositories.xml';
	
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
	
	static $SITES = array();
	static $REPOSITORIES = array();
	static $DATABASES = array();
	static $HANDLERS = array();
	
	//------------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	static function init() {
// 		self::$SITES = include(self::DIR.'databases.php');
// 		self::$REPOSITORIES = include(self::DIR.'repositories.php');
// 		self::$DATABASES = include(self::DIR.'databases.php');
// 		self::$DATABASES = include(self::DIR.'databases.php');
		self::$SITES = simplexml_load_file(self::DIR.'sites.xml');
		self::$HANDLERS = simplexml_load_file(self::DIR.'handlers.xml');
		self::$REPOSITORIES = simplexml_load_file(self::DIR.'repositories.xml');
		self::$DATABASES = simplexml_load_file(self::DIR.'databases.xml');
	}
			
	//------------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	static function getHandlerConfig(string $sHandlerID) {
		if (!isset(self::$HANDLERS->$sHandlerID)) {
			throw new sbException('handler '.$sHandlerID.' could not be initialized');
		} else {
			$elemHandler = self::$HANDLERS->$sHandlerID;
			$aHandler['library'] = (string) $elemHandler->library;
			$aHandler['class'] = (string) $elemHandler->class;
			// Todo: implement some kind of handler registration for modules
			$aHandler['module'] = 'sb_system';
			if (isset($elemHandler->module)) {
				$aHandler['module'] = (string) $elemHandler->module;
			}
			return ($aHandler);
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	static function getRepositoryConfig(string $sRepositoryID) : array {
		if (!isset(self::$REPOSITORIES->$sRepositoryID)) {
			throw new sbException('repository '.$sRepositoryID.' not defined');
		} else {
			$elemRepository = self::$REPOSITORIES->$sRepositoryID;
			return ($elemRepository);
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	static function getDatabaseConfig(string $sDatabaseID) : array {
		if (!isset(self::$DATABASES->$sDatabaseID)) {
			throw new sbException('database '.$sHandlerID.' not defined');
		} else {
			$elemDatabase = self::$DATABASES->$sDatabaseID;
			$aDatabaseDefinition['host'] = (string) $elemDatabase->host;
			$aDatabaseDefinition['port'] = (string) $elemDatabase->port;
			$aDatabaseDefinition['user'] = (string) $elemDatabase->user;
			$aDatabaseDefinition['pass'] = (string) $elemDatabase->pass;
			$aDatabaseDefinition['schema'] = (string) $elemDatabase->schema;
			$aDatabaseDefinition['charset'] = (string) $elemDatabase->charset;
			$aDatabaseDefinition['log_enabled'] = (string) constant((string) $elemDatabase->log['enabled']);
			$aDatabaseDefinition['log_verbose'] = (string) constant((string) $elemDatabase->log['verbose']);
			$aDatabaseDefinition['log_file'] = self::LOGDIR . (string) $elemDatabase->log->file;
			$aDatabaseDefinition['log_size'] = (integer) $elemDatabase->log->size;
			return ($aDatabaseDefinition);
		}
	}
	
}

CONFIG::init();

?>