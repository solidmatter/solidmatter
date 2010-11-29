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
		$this->aQueries['getAbandonedNodes']		= 'sbSystem/node/trashcan/getAbandonedNodes';
		$this->aQueries['getTrash/all']				= 'sbSystem/node/trashcan/getTrash/all';
		$this->aQueries['getTrash/userspecific']	= 'sbSystem/node/trashcan/getTrash/userspecific';
		$this->aQueries['purge']					= 'sbSystem/node/trashcan/purge';
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
	* TODO: get more info on nodes and display in trash list view
	* @param 
	* @return 
	*/
	public function getTrash($sUserID = NULL) {
		
		if (User::isAdmin()) {
			$stmtGetNodes = $this->crSession->prepareKnown($this->aQueries['getTrash/all']);
		} else {
			$stmtGetNodes = $this->crSession->prepareKnown($this->aQueries['getTrash/userspecific']);
			$stmtGetNodes->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		}
		$stmtGetNodes->execute();
		
		$aTrash = array();
		foreach ($stmtGetNodes as $aRow) {
			$nodeTrashItem = $this->crSession->getNodeByIdentifier($aRow['child_uuid'], $aRow['parent_uuid']);
			$nodeTrashItem->setAttribute('user_label', $aRow['user_label']);
			$nodeTrashItem->setAttribute('parent_label', $aRow['parent_label']);
			$nodeTrashItem->setAttribute('parent_nodetype', $aRow['parent_nodetype']);
			$aTrash[] = $nodeTrashItem;
		}
		return (new sbCR_NodeIterator($aTrash));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*public function recoverTrash($sSubjectUUID, $sParentUUID) {
		
		$stmtGetNodes = $this->crSession->prepareKnown($this->aQueries['getTrash/all']);
		$stmtGetNodes->execute();
		
		$aTrash = array();
		foreach ($stmtGetNodes as $aRow) {
			$aTrash[] = $this->crSession->getNodeByIdentifier($aRow['child_uuid'], $aRow['parent_uuid']);
			
		}
		return (new sbCR_NodeIterator($aTrash));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: implement user-specific purging
	* @param 
	* @return 
	*/
	public function purge($sUserID = NULL) {
		
		$stmtPurgeLinks = $this->crSession->prepareKnown($this->aQueries['purge']);
		$stmtPurgeLinks->execute();
		
	}
	
}

?>