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
	
	// contains the current stack of nested transactions
	private static $aTransactionUIDs = array();
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function __construct(string $sDSN, string $sUsername = '', string $sPassword = '', array $aOptions = array()) {
		parent::__construct($sDSN, $sUsername, $sPassword, $aOptions);
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('sbPDOStatement'));
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Begins an (optionally) nested transaction.
	 * Transactions can be nested and have to be committed in reverse order.
	 * @param string The transaction ID
	 */
	public function beginTransaction(string $sUID = 'DEFAULT') {
		
		DEBUG('PDO: started transaction "'.$sUID.'"', DEBUG::PDO);
		
		if (count(self::$aTransactionUIDs) == 0) {
			parent::beginTransaction();
		}
		
		array_push(self::$aTransactionUIDs, $sUID);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Commits a transaction.
	 * If nested transactions are used, it has to be the transaction that was begun last. 
	 * @param string The transaction ID
	 */
	public function commit(string $sUID = 'DEFAULT') {
		
		DEBUG('PDO: committed transaction "'.$sUID.'"', DEBUG::PDO);
		
		$sStackUID = array_pop(self::$aTransactionUIDs);
		
		if ($sUID != $sStackUID) {
			parent::rollback();
			throw new NestedTransactionException('commit with non-matching opening and closing UID ('.$sStackUID.', '.$sUID.')');	
		}
		
		if (count(self::$aTransactionUIDs) == 0) {
			parent::commit();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Rolls back the complete stack of transactions.
	 * @param
	 * @return
	 */
	public function rollback() {
		
		if (count(self::$aTransactionUIDs) == 0) {
			throw new NestedTransactionException('rollback called without active transactions');	
		}
		
		self::$aTransactionUIDs = array();
		parent::rollback();
		
		DEBUG('PDO: rolled back all active transactions', DEBUG::PDO);
		
	}
	
}

?>