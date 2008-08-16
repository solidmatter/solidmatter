<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.pdo.repository.queries');

//------------------------------------------------------------------------------
/**
*/
class sbPDORepository extends sbPDO {
	
	protected static $aQueryCache = array();
	
	protected $bLogEnabled		= FALSE;
	protected $bLogVerbose		= FALSE;
	protected $sLogFile			= '';
	protected $sLogSize			= '4096';
	
	protected $sRepositoryPrefix	= '';
	protected $sWorkspacePrefix		= '';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sxmlDefinition) {
		
		$sHost		= (string) $sxmlDefinition->host;
		$sPort		= (string) $sxmlDefinition->port;
		$sUser		= (string) $sxmlDefinition->user;
		$sPass		= (string) $sxmlDefinition->pass;
		$sDatabase	= (string) $sxmlDefinition->schema;
		$sDSN = 'mysql:host='.$sHost.';port='.$sPort.';dbname='.$sDatabase;
		parent::__construct($sDSN, $sUser, $sPass);
		
		$this->init($sxmlDefinition);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function init($sxmlDefinition) {
		
		$sCharset	= (string) $sxmlDefinition->charset;
		$this->bLogEnabled = constant((string) $sxmlDefinition->log['enabled']);
		$this->bLogVerbose = constant((string) $sxmlDefinition->log['verbose']);
		$this->sLogFile = (string) $sxmlDefinition->log->file;
		$this->sLogSize = (integer) $sxmlDefinition->log->size;
		
		$this->query('SET NAMES '.$sCharset);
		
		$this->log('repository definition loaded', TRUE);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setWorkspace($sRepository, $sWorkspace) {
		$this->sRepositoryPrefix = $sRepository;
		$this->sWorkspacePrefix = $sWorkspace;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function prepareKnown($sID, $aDriverOptions = NULL) {
		
		$this->log($sID);
		
		if (isset(self::$aQueryCache[$sID])) { // query is cached
		
			return (self::$aQueryCache[$sID]);
			
		} else { // not cached, prepare
			
			// check if query exists and retrieve it
			global $_QUERIES;
			if (!isset($_QUERIES[$sID])) {
				throw new QueryNotFoundException((string) $sID);
			}
			$sQuery = $_QUERIES[$sID];
			
			// apply table mapping
			$aSearch = array_keys($_QUERIES['MAPPING']);
			$aReplace = $_QUERIES['MAPPING'];
			$sQuery = str_replace($aSearch, $aReplace, $sQuery);
			
			// apply prefixes
			$sQuery = $this->prepareQuery($sQuery);
			
			// create statement object
			if ($aDriverOptions != NULL) {
				$stmtPrepared = $this->prepare($sQuery, $aDriverOptions);
			} else {
				$stmtPrepared = $this->prepare($sQuery);
			}
			$stmtPrepared->addDebugInfo($sQuery, $sID);
			
			// store query for later usage
			self::$aQueryCache[$sID] = $stmtPrepared;
			
			// log if necessary
			/*if (DEBUG || DEBUG_DB) {
				if (DEBUG_VERBOSE) {
					$sLog = "\r\n".strftime('%y-%m-%d %H:%M:%S', time())." -------------------------------------------------------------\r\n".$sQuery;
				}  else {
					$sLog = $sID;
				}
				LOG2FILE($sLog, 'db');
			}*/
			
			return ($stmtPrepared);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function prepareQuery($sQuery) {
		
		$aSearch = array();
		$aReplace = array();
		$aSearch[] = '{PREFIX_FRAMEWORK}';
		$aReplace[] = $this->sRepositoryPrefix;
		$aSearch[] = '{PREFIX_WORKSPACE}';
		$aReplace[] = $this->sWorkspacePrefix;
		$sQuery = str_replace($aSearch, $aReplace, $sQuery);
		
		return ($sQuery);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function log($sText, $bIncludeHeader = FALSE) {
		
		if (!$this->bLogEnabled) {
			return;
		}
		
		// TODO: use this info?
		//$this->sLogSize
		
		if ($bIncludeHeader) {
			$sText = "\r\n".'-- '.get_class($this).': '.strftime('%y-%m-%d %H:%M:%S', time()).' '.str_repeat('-', 80)."\r\n".$sText;
		}
		
		error_log($sText."\r\n", 3, System::getDir().$this->sLogFile);

	}

}

?>