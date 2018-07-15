<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter
*	@subpackage sbPDO
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.pdo');
import('sb.pdo.system.queries');
import('sb.system.errors');

//------------------------------------------------------------------------------
/**
* 
*/
class sbPDOSystem extends sbPDO {
	
	protected static $aQueryCache = array();
	
	protected $lgLog			= NULL;
	protected $bLogEnabled		= FALSE;
	protected $bLogVerbose		= FALSE;
	
	// array of placeholders to rewrite on prepare (search -> replace)
	protected $aPrepareRewrites = array(
		'{PREFIX_SYSTEM}' => 'global'
	);
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function __construct(string $sDatabaseID) {
		
		$elemDB = CONFIG::getDatabaseConfig($sDatabaseID);
		$sDSN = 'mysql:host='.$elemDB['host'].';port='.$elemDB['port'].';dbname='.$elemDB['schema'];
		parent::__construct($sDSN, $elemDB['user'], $elemDB['pass']);
		if (isset($elemDB->log)) {
			if ((string) $elemDB->log['enabled'] == 'true') {
				$this->bLogEnabled = TRUE;
				if ((string) $elemDB->log['verbose'] == 'true') {
					$this->bLogVerbose = TRUE;
				}
			}
			if (!CONFIG::LOGDIR_ABS) { // log directory is not absolute path
				$this->sLogFile = System::getDir().'/'.CONFIG::LOGDIR.$elemDB->log['file'];
			} else {
				$this->sLogFile = CONFIG::LOGDIR.$elemDB->log['file'];
			}
			$this->sLogSize = $elemDB->log['size'];
			if ($this->bLogEnabled) {
				$this->lgLog = new Logger(get_class($this), (string) $elemDB->log['file']);
				$this->log('Database "'.$sDatabaseID.'" connected to schema "'.$elemDB['schema'].'" in "'.$elemDB['host'].':'.$elemDB['port'].'"');
			}
		}
		
		$this->query('SET NAMES '.$elemDB['charset']);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function prepareKnown(string $sID, array $aDriverOptions = NULL) : sbPDOStatement {
		
		$this->log('prepareKnown: '.$sID);
		
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
			
			return ($stmtPrepared);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function setWorkspace(string $sRepository, string $sWorkspace) {
		$this->sRepositoryPrefix = $sRepository;
		$this->sWorkspacePrefix = $sWorkspace;
		$this->aPrepareRewrites['{PREFIX_REPOSITORY}'] = $sRepository;
		$this->aPrepareRewrites['{PREFIX_WORKSPACE}'] = $sWorkspace;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function prepareQuery(string $sQuery) : string {
		$sQuery = str_replace(array_keys($this->aPrepareRewrites), array_values($this->aPrepareRewrites), $sQuery);
		return ($sQuery);
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function log(string $sText) {
		if ($this->bLogEnabled) {
			$this->lgLog->addText($sText);
		}
	}
	
}

?>