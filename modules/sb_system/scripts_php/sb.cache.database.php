<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/** TODO: use other db than system, or move cache table to global scope!
*/
class DatabaseCache {
	
	protected $sPrefix = 'DEFAULT:';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeData($sKey, $mData) {
		$stmtStore = System::getDatabase()->prepareKnown('sbSystem/cache/flat/store');
		$stmtStore->bindValue('key', $this->sPrefix.$sKey, PDO::PARAM_STR);
		$stmtStore->bindValue('data', serialize($mData), PDO::PARAM_STR);
		$stmtStore->execute();
		$stmtStore->closeCursor();
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadData($sKey) {
		$stmtLoad = System::getDatabase()->prepareKnown('sbSystem/cache/flat/load');
		$stmtLoad->bindValue('key', $this->sPrefix.$sKey, PDO::PARAM_STR);
		$stmtLoad->execute();
		$mData = FALSE;
		foreach ($stmtLoad as $aRow) {
			$mData = unserialize($aRow['t_value']);
		}
		$stmtLoad->closeCursor();
		return ($mData);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function exists($sKey) {
		$stmtExists = System::getDatabase()->prepareKnown('sbSystem/cache/flat/check');
		$stmtExists->bindValue('key', $this->sPrefix.$sKey, PDO::PARAM_STR);
		$stmtExists->execute();
		foreach ($stmtExists as $aRow) {
			$stmtExists->closeCursor();
			return (TRUE);
		}
		$stmtExists->closeCursor();
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clear($sKey = '') {
		$sKey = $sKey.'%';
		$stmtClear = System::getDatabase()->prepareKnown('sbSystem/cache/flat/clear');
		$stmtClear->bindValue('key', $this->sPrefix.$sKey, PDO::PARAM_STR);
		$stmtClear->execute();
		$stmtClear->closeCursor();
	}
	
}

?>