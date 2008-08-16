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
*//*
class sbCRInternals {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*
	public static function initNestedSets($sCurrentUUID = NULL, $iCounter = 1, $iLevel = 0) {
		
		if ($sCurrentUUID == NULL) {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbCR/test/loadRoot');
		} else {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbCR/test/loadChildren');
			$stmtLoadChildren->bindParam('fk_parent', $sCurrentUUID, PDO::PARAM_STR);
		}
		$stmtLoadChildren->execute();
		$aResultset = $stmtLoadChildren->fetchALL(PDO::FETCH_ASSOC);
		$stmtLoadChildren->closeCursor();
		
		$iOrder = 0;
		
		foreach ($aResultset as $iRownumber => $aRow) {
			$iLeft = $iCounter++;
			$this->rebuildNestedSets($aRow['uuid'], &$iCounter, $iLevel+1);
			$iRight = $iCounter++;
			$stmtSetCoordinates = $this->crSession->prepareKnown('sbCR/test/setCoordinates');
			$stmtSetCoordinates->bindParam('fk_child', $aRow['uuid'], PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('fk_parent', $sCurrentUUID, PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('left', $iLeft, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('right', $iRight, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('level', $iLevel, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('order', $iOrder, PDO::PARAM_INT);
			$stmtSetCoordinates->execute();
			$iOrder++;
		}
	}	
	
}*/

?>