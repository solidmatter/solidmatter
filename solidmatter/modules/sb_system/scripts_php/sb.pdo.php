<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter
*	@subpackage sbPDO
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.pdo.statement');

//------------------------------------------------------------------------------
/**
*/
class sbPDO extends PDO {
	
	// Logging 
	protected $logDB			= NULL;
	protected $bLogEnabled		= FALSE;
	protected $bLogVerbose		= FALSE;
	
	// Array of queries 
	protected $aKnownQueries = NULL;
	
	// Array of placeholders to rewrite on prepare (search => replace)
	protected $aPrepareRewrites = array();
	
	// contains the current stack of nested transactions
	protected $aTransactionUIDs = array();
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param string Database connection string
	 * @param string DB Username
	 * @param string db Password
	 * @param array Connection options
	 * @param array Array of known (predefined) queries (optional, defaults to global $_QUERIES)
	 * @return
	 */
	public function __construct(string $sDSN, string $sUser = '', string $sPass = '', array $aOptions = array(), array $aKnownQueries = NULL) {
		parent::__construct($sDSN, $sUser, $sPass, $aOptions);
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('sbPDOStatement'));
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if ($aKnownQueries == NULL) {
			global $_QUERIES;
			$this->aKnownQueries = $_QUERIES;
		} else {
			$this->aKnownQueries = $aKnownQueries;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function attachLogger(Logger $logDB) {
		$this->logDB = $logDB;
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Prepares a statement that is defined in the global  
	 * @param
	 * @return
	 */
	public function prepareKnown(string $sID, array $aDriverOptions = NULL) : sbPDOStatement {
		
		// Todo: add rewrite info to log
		$this->log('prepareKnown: '.$sID);
		
		// check if query exists and retrieve it
		global $_QUERIES;
		if (!isset($_QUERIES[$sID])) {
			throw new QueryNotFoundException((string) $sID);
		}
		$sQuery = $_QUERIES[$sID];
		
		// apply table mapping
		$sQuery = str_replace(array_keys($_QUERIES['MAPPING']), $_QUERIES['MAPPING'], $sQuery);
		
		// apply prefixes
		$sQuery = $this->rewriteQuery($sQuery);
		
		// create statement object
		if ($aDriverOptions != NULL) {
			$stmtPrepared = $this->prepare($sQuery, $aDriverOptions);
		} else {
			$stmtPrepared = $this->prepare($sQuery);
		}
		$stmtPrepared->addDebugInfo($sQuery, $sID);
		$stmtPrepared->setPDO($this);
		
		return ($stmtPrepared);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function setRewrite(string $sSearch, string $sReplace) {
		$this->aPrepareRewrites[$sSearch] = $sReplace;
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function rewriteQuery(string $sQuery) : string {
		return (str_replace(array_keys($this->aPrepareRewrites), $this->aPrepareRewrites, $sQuery));
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function log(string $sText) {
		if ($this->bLogEnabled && $this->logDB != NULL) {
			$this->logDB->addText($sText);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Begins an (optionally) nested transaction.
	 * Transactions can be nested and have to be committed in reverse order.
	 * @param string The transaction ID
	 */
	public function beginTransaction(string $sUID = 'DEFAULT') {
		
		DEBUG('PDO: started transaction "'.$sUID.'"', DEBUG::PDO);
		
		if (count($this->aTransactionUIDs) == 0) {
			parent::beginTransaction();
		}
		
		array_push($this->aTransactionUIDs, $sUID);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Commits a transaction.
	 * If nested transactions are used, it has to be the transaction that was begun last. 
	 * @param string The transaction ID
	 */
	public function commit(string $sUID = 'DEFAULT') {
		
		DEBUG('PDO: committed transaction "'.$sUID.'"', DEBUG::PDO);
		
		$sStackUID = array_pop($this->aTransactionUIDs);
		
		if ($sUID != $sStackUID) {
			parent::rollback();
			throw new NestedTransactionException('commit with non-matching opening and closing UID ('.$sStackUID.', '.$sUID.')');	
		}
		
		if (count($this->aTransactionUIDs) == 0) {
			parent::commit();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Returns the currently active transaction ID (the last started level for nested transactions).
	 * @param string The transaction ID
	 */
	public function getActiveTransaction() {
		return end($this->aTransactionUIDs);
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Rolls back the complete stack of transactions.
	 * @param
	 * @return
	 */
	public function rollback() {
		
		if (count($this->aTransactionUIDs) == 0) {
			throw new NestedTransactionException('rollback called without active transactions');	
		}
		
		$this->aTransactionUIDs = array();
		parent::rollback();
		
		DEBUG('PDO: rolled back all active transactions', DEBUG::PDO);
		
	}
	
}

?>