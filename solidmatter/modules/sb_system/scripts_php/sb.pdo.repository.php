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
	public function __construct($sRepositoryID) {
		
		$aRepositoryDefinition = CONFIG::getRepositoryConfig($sRepositoryID);
		$aDBConfig = CONFIG::getDatabaseConfig($aRepositoryDefinition['db']);
		$sDSN = 'mysql:host='.$aDBConfig['host'].';port='.$aDBConfig['port'].';dbname='.$aDBConfig['schema'];
		parent::__construct($sDSN, $aDBConfig['user'], $aDBConfig['pass']);
		
		$this->init($aDBConfig);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function init($aDBConfig) {
		
		$this->bLogEnabled = $aDBConfig['log_enabled'];;
		$this->bLogVerbose = $aDBConfig['log_verbose'];;
		$this->sLogFile = $aDBConfig['log_file'];
		$this->sLogSize = $aDBConfig['log_size'];;
		
		$this->query('SET NAMES '.$aDBConfig['charset']);
		
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
		$aSearch[] = '{PREFIX_REPOSITORY}';
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