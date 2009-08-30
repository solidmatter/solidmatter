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
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['getAbandonedNodes'] = 'sbSystem/node/trashcan/getAbandonedNodes';
	}
	
	//--------------------------------------------------------------------------
	/**
	* FIXME: doesn't work currently
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
	
	//--------------------------------------------------------------------------
	/**
	* TODO: implement user-specific purging
	* @param 
	* @return 
	*/
	public function purge($sUserID = NULL) {
		
		$niTrash = $this->getChildren();
		foreach ($niTrash as $nodeTrash) {
			$nodeTrash->remove();
			$nodeTrash->save();
		}
	}
	
	
}

?>