<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbNode_trashcan extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __Queries() {
		parent::__setQueries();
		$this->aQueries['getAbandonedNodes'] = 'sb_system/node/trashcan/getAbandonedNodes';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAbandonedNodes() {
		
		$stmtGetNodes = $this->crSession->prepareKnown($this->aQueries['getAbandonedNodes']);
		$stmtGetNodes->execute();
		
		$aAbandonedUUIDs = array();
		foreach ($stmtGetNodes as $aRow) {
			$aAbandonedUUIDs[] = $aRow['uuid'];
		}
		$stmtGetNodes->closeCursor();
		
		if (count($aAbandonedUUIDs) > 0) {
			
			foreach ($aAbandonedUUIDs as $aAbandonedUUID) {
				$nodeCurrent = $this->crSession->getInstance($aAbandonedUUID);
				$aChildNodes[] = $nodeCurrent;
			}
			
			$niChildNodes = new sbCR_NodeIterator($aChildNodes);
			return ($niChildNodes);
			
		}
		
		return (FALSE);
		
	}
	
	
}

?>