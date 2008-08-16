<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class PathCache {
	
	private $aPrecache = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeData($sKey, $mData) {
		$stmtStore = System::getDatabase()->prepareKnown('sb_system/cache/paths/store');
		$stmtStore->bindParam('path', $sKey, PDO::PARAM_STR);
		$stmtStore->bindParam('node_id', $mData, PDO::PARAM_STR);
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
		$stmtLoad = System::getDatabase()->prepareKnown('sb_system/cache/paths/load');
		$stmtLoad->bindParam('path', $sKey, PDO::PARAM_STR);
		$stmtLoad->execute();
		$iNodeID = FALSE;
		foreach ($stmtLoad as $aRow) {
			$iNodeID = $aRow['fk_node'];
		}
		$stmtLoad->closeCursor();
		return ($iNodeID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*public function exists($sKey) {

	}*/
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clear($sKey = '') {
		$sKey = $sKey.'%';
		$stmtClear = System::getDatabase()->prepareKnown('sb_system/cache/paths/clear');
		$stmtClear->bindParam('path', $sKey, PDO::PARAM_STR);
		$stmtClear->execute();
		$stmtClear->closeCursor();
	}
	
}

?>