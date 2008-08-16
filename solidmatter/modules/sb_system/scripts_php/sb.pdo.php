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
	
	const FILTER_DATETIME = 1;
	
	private static $aTransactionUIDs = array();
	
	public function __construct($sDSN, $sUsername = '', $sPassword = '', $aOptions = array()) {
		parent::__construct($sDSN, $sUsername, $sPassword, $aOptions);
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('sbPDOStatement'));
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function beginTransaction($sUID) {
		
		//echo str_repeat('&nbsp;&nbsp;&nbsp;', count(self::$aTransactionUIDs)).'beginTransaction: '.$sUID.'<br>';
		
		if (count(self::$aTransactionUIDs) == 0) {
			parent::beginTransaction();
		}
		
		array_push(self::$aTransactionUIDs, $sUID);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function commit($sUID) {
		
		//echo str_repeat('&nbsp;&nbsp;&nbsp;', count(self::$aTransactionUIDs)-1).'commit: '.$sUID.'<br>';
		
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
	* 
	* @param 
	* @return 
	*/
	public function rollback() {
		
		if (count(self::$aTransactionUIDs) == 0) {
			throw new NestedTransactionException('rollback called without active transactions');	
		}
		
		self::$aTransactionUIDs = array();
		parent::rollback();
		
	}
	
	
}





?>