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
class sbView_maintenance_repair extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'showOptions':
				
				break;
			
			case 'rebuildMaterializedPaths':
				$this->logEvent(System::MAINTENANCE, 'REBUILD_NESTEDSETS_STARTED', 'starting to fix materialized paths');
				$this->rebuildMaterializedPaths();
				$this->logEvent(System::MAINTENANCE, 'REBUILD_NESTEDSETS_ENDED', 'done with rebuilding materialized paths');
				break;
			
			case 'rebuildNestedSets':
				$this->logEvent(System::MAINTENANCE, 'REBUILD_NESTEDSETS_STARTED', 'starting to fix nested set values');
				$this->rebuildNestedSets();
				$this->logEvent(System::MAINTENANCE, 'REBUILD_NESTEDSETS_ENDED', 'done with rebuilding nested set values');
				break;
				
			case 'rebuildNestedSetsMemory':
				$this->logEvent(System::MAINTENANCE, 'REBUILD_NESTEDSETSMEM_STARTED', 'starting to fix nested set values');
				$this->rebuildNestedSetsMemory();
				$this->logEvent(System::MAINTENANCE, 'REBUILD_NESTEDSETSMEM_ENDED', 'done with rebuilding nested set values');
				break;
				
			case 'rebuildPositions':
				
				break;
			
			case 'check':
				
				break;
			
			case 'removeAbandonedProperties':
				$stmtRemove = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/removeAbandonedProperties/normal');
				$stmtRemove->execute();
				$stmtRemove = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/removeAbandonedProperties/binary');
				$stmtRemove->execute();
				$this->logEvent(System::MAINTENANCE, 'PROPERTIES_REMOVED', 'removed abandoned properties');
				break;
			
			case 'gatherAbandonedNodes':
				$nodeTrashcan = $this->crSession->getNode('//*[@uid="sbSystem:Trashcan"]');
				$niAbandonedNodes = $nodeTrashcan->getAbandonedNodes();
				foreach ($niAbandonedNodes as $nodeTrash) {
					$nodeTrashcan->addExistingNode($nodeTrashcan);
				}
				$this->logEvent(System::MAINTENANCE, 'TRASHCAN_FILLED', 'gathered abandoned nodes in trashcan');
				break;
				
			case 'rebuildAuthorisationCache':
				$nodeRoot = $this->crSession->getRoot();
				//$nodeRoot->rebuild
				throw new LazyBastardException();
			
			case 'removeAbandonedNodes':
				$stmtRemove = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/removeAbandonedNodes/normal');
				$stmtRemove->execute();
				$this->logEvent(System::MAINTENANCE, 'NODES_REMOVED', 'removed abandoned nodes');
				break;
			
		}
		
		return ($this->nodeSubject);
		
	}
	
	private function rebuildMaterializedPaths($sCurrentUUID = NULL, $sMPath = '', $iLevel = 0) {
		
		if ($sCurrentUUID == NULL) {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/loadRoot');
		} else {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/loadChildren');
			$stmtLoadChildren->bindParam('fk_parent', $sCurrentUUID, PDO::PARAM_STR);
		}
		$stmtLoadChildren->execute();
		$aResultset = $stmtLoadChildren->fetchALL(PDO::FETCH_ASSOC);
		$stmtLoadChildren->closeCursor();
		
		if ($sCurrentUUID == NULL) {
			$sMPath = '';	
		} else {
			$sMPath = $sMPath.substr(md5($sCurrentUUID), -5);
		}
		
		$iOrder = 0;
		foreach ($aResultset as $aRow) {
			$this->rebuildMaterializedPaths($aRow['uuid'], $sMPath, $iLevel+1);
			$stmtSetCoordinates = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/setCoordinates/MPath');
			$stmtSetCoordinates->bindParam('fk_child', $aRow['uuid'], PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('fk_parent', $sCurrentUUID, PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('level', $iLevel, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('order', $iOrder, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('mpath', $sMPath, PDO::PARAM_INT);
			$stmtSetCoordinates->execute();
			$iOrder++;
		}
		
	}
	
	private function rebuildNestedSets($sCurrentUUID = NULL, $iCounter = 1, $iLevel = 0) {
		
		if ($sCurrentUUID == NULL) {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/loadRoot');
		} else {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/loadChildren');
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
			$stmtSetCoordinates = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/setCoordinates/nestedSets');
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
	
	private function rebuildNestedSetsMemory($sCurrentUUID = NULL, $iCounter = 1, $iLevel = 0) {
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
			$this->rebuildNestedSetsMemory($aRow['uuid'], &$iCounter, $iLevel+1);
			$iRight = $iCounter++;
			$stmtSetCoordinates = $this->crSession->prepareKnown('sbCR/test/setCoordinates');
			$stmtSetCoordinates->bindParam('fk_child', $aRow['uuid'], PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('fk_parent', $sCurrentUUID, PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('left', $iLeft, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('right', $iRight, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('level', $iLevel, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('order', $iOrder, PDO::PARAM_INT);
			if ($sCurrentUUID != NULL) {
				$stmtSetCoordinates->execute();
			}
			$iOrder++;
		}
	}
	
}


?>