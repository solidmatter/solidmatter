<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* Static class that provides information on the system environment the current
* solidMatter installation is running on. 
*/
class System {
	
	// log levels
	const SECURITY		= 1;
	const ERROR			= 2;
	const WARNING		= 4;
	const MAINTENANCE	= 8;
	const INFO			= 16;
	const DEBUG			= 32;
	
	// basic system info
	const VERSION		= '0.0alpha';
	const API_VERSION	= '0.0alpha';
	
	private static $iLogLevel = 63;
	private static $crSession;
	private static $dbSystem;
	private static $hndRequest;
	
	private static $sSystemDirectory = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* Initializes the class, should be called early in request processing.
	*/
	public static function init() {
		
		if (self::$sSystemDirectory == NULL) {
			self::$sSystemDirectory = getcwd().'/';
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the full system path of the current solidMatter installation.
	* @return string the system path
	*/
	public static function getDir() {
		return (self::$sSystemDirectory);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the system's database.
	* Note that this is the global, workspace-independend database used for
	* things like session storage and caching.
	* @return 
	*/
	public static function getDatabase() {
		if (self::$dbSystem === NULL) {
			global $_CONTROLLERCONFIG;
			import('sb.pdo.system');
			self::$dbSystem = new sbPDOSystem($_CONTROLLERCONFIG->system_db);
		}
		return (self::$dbSystem);
	}
	
	
	
	//--------------------------------------------------------------------------
	/**
	* Sets the currently used sbCRSession which will be used for logging.
	* @param 
	* @return 
	*/
	public static function setSession($crSession) {
		self::$crSession = $crSession;	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function setLogLevel($iLevel) {
		self::$iLogLevel = $iLevel;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Adds an entry to the global event log. 
	* @param integer basic event type (may be one of the class constants INFO, MAINTENANCE, SECURITY, ERROR, DEBUG, WARNING) 
	* @param string module ID that this entry should be logged under
	* @param string a unique name of the event type, should be human-readable
	* @param string a text with additional details (optional)
	* @param string the subject node UUID
	*/
	public static function logEvent($eType, $sModule, $sUID, $sText, $sSubjectUUID) {
		if (!(self::$iLogLevel & $eType)) {
			return (FALSE);
		}
		switch ($eType) {
			case System::INFO:			$sType = 'INFO';			break;
			case System::MAINTENANCE:	$sType = 'MAINTENANCE';		break;
			case System::SECURITY:		$sType = 'SECURITY';		break;
			case System::ERROR:			$sType = 'ERROR';			break;
			case System::DEBUG:			$sType = 'DEBUG';			break;
			case System::WARNING:		$sType = 'WARNING';			break;
		}
		$stmtLog = self::$crSession->prepareKnown('sbSystem/eventLog/LogEntry');
		$stmtLog->bindValue('module', $sModule, PDO::PARAM_STR);
		$stmtLog->bindValue('type', $sType, PDO::PARAM_STR);
		$stmtLog->bindValue('loguid', $sUID, PDO::PARAM_STR);
		$stmtLog->bindValue('logtext', $sText, PDO::PARAM_STR);
		$stmtLog->bindValue('subject', $sSubjectUUID, PDO::PARAM_STR);
		if (Registry::getValue('sb.system.privacy.events')) {
			$stmtLog->bindValue('user', NULL, PDO::PARAM_STR);
		} else {
			$stmtLog->bindValue('user', User::getUUID(), PDO::PARAM_STR);	
		}
		$stmtLog->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function LOGG($sText, $eType = System::INFO, $eContext = System::SYSTEM, $bIncludeHeader = FALSE) {
		if (!(self::$iLogLevel & $eType)) {
			return (FALSE);
		}
		switch ($eContext) {
			case System::SYSTEM:
				$sFile = '_logs/controller_system.txt';
				break;
			case System::DEBUG:
				$sFile = '_logs/controller_debug.txt';
				break;
			case System::REQUEST:
				$sFile = '_logs/controller_requests.txt';
				break;
			default:
				die('unknown context "'.$eContext.'"');
		}
		if ($bIncludeHeader) {
			$sText = "\r\n".'-- '.get_class($this).': '.strftime('%y-%m-%d %H:%M:%S', time()).' '.str_repeat('-', 80)."\r\n".$sText;
		}
		
		error_log($sText."\r\n", 3, self::getDir().$sFile);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array with info on all installed modules in this solidMatter instance.
	* @return 
	*/
	public static function getModules() {
		
		// FIXME: hm, sb_system has to be first, otherwise css rendering does not work correctly... why?  
		$aModuleInfos	= array('sb_system' => array());
		$hModules		= opendir('modules');
		
		while ($sModule = readdir($hModules)) {
			if (substr($sModule, 0, 1) == '.' || $sModule == 'sb_system') {
				continue;
			}
			if (is_dir('modules/'.$sModule)) {
				$aModuleInfos[$sModule] = array();
			}
		}
		closedir($hModules);
		//var_dump($aModuleInfos);
		return ($aModuleInfos);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the failsafe (lowercase, filesystem friendly) name of the given module.
	* TODO: make obsolete and remove if possible (problem: namespaces may contain invalid characters)
	* @return string the failsafe name
	*/
	public static function getFailsafeModuleName($sModule) {
		$aModules = array(
			'sbSystem' => 'sb_system',
			'sbCMS' => 'sb_cms',
			'sbFiles' => 'sb_files',
			'sbNews' => 'sb_news',
			'sbUtilities' => 'sb_utilities',
			'sbForum' => 'sb_forum',
			'sbGuestbook' => 'sb_guestbook',
			'sbJukebox' => 'sb_jukebox',
			'sbPortal' => 'sb_portal',
			'sbIdM' => 'sb_idm',
		);
		if (isset($aModules[$sModule])) {
			return ($aModules[$sModule]);
		}
		return ($sModule);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the handler processing the current request.
	*/
	public static function setRequestHandler($hndProcessor) {
		self::$hndRequest = $hndProcessor;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the handler processing the current request.
	*/
	public static function getRequestHandler() {
		return (self::$hndRequest);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a fully qualified request URL based on the current handler.
	* @param 
	* @return 
	*/
	public static function getRequestURL($mSubject = NULL, $sView = NULL, $sAction = NULL, $aParameters = NULL) {
		return(self::getRequestHandler()->generateRequestURL($mSubject, $sView, $sAction, $aParameters));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a request path and query based on the current handler.
	* @param 
	* @return 
	*/
	public static function getRequestPath($mSubject = NULL, $sView = NULL, $sAction = NULL, $aParameters = NULL) {
		return(self::getRequestHandler()->generateRequestPath($mSubject, $sView, $sAction, $aParameters));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the operating system solidMatter is running on.
	* @return string identifier of the os
	*/
	public static function getEnvironment() {
		
		// $_ENV['OS']???
		if (substr( PHP_OS, 0, 3 ) == 'WIN') {
			return ('windows');
		} else {
			return ('linux');	
		}

		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the character encoding used by the operating system.
	* @return string the encoding in a format ready for iconv()
	*/
	public static function getFilesystemEncoding() {
		
		// TODO: actually check the server environment
		if (self::getEnvironment() == 'windows') {
			return ('Windows-1252');
		} elseif (self::getEnvironment() == 'linux') {
			return ('UTF-8');
		}
		
		return ('UTF-8');
		
	}
	
}

?>