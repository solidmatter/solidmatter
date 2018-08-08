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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'showOptions':
				
				break;
			
			case 'rebuildMaterializedPaths':
				$this->logEvent(System::MAINTENANCE, 'REBUILD_MPATHS_STARTED', 'starting to fix materialized paths');
				$this->rebuildMaterializedPaths();
				$this->logEvent(System::MAINTENANCE, 'REBUILD_MPATHS_ENDED', 'done with rebuilding materialized paths');
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
					$nodeTrashcan->addExistingNode($nodeTrash);
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
				
			case 'optimizeUUIDs':
// 				$this->logEvent(System::MAINTENANCE, 'UUID_OPTIMIZATION_STARTED', 'started to convert to sbUUIDs');
				$stmtSelect = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/getAllUUIDs');
				$stmtSelect->execute();
				$aResultset = $stmtSelect->fetchALL(PDO::FETCH_ASSOC);
				
				$stmtUpdate = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/updateUUID');
				
				foreach ($aResultset as $aRow) {
					if ($aRow['uuid'] != '00000000000000000000000000000000') {
						$sNewUUID = sbUUID();
						$stmtUpdate->bindParam('uuid_old', $aRow['uuid'], PDO::PARAM_STR);
						$stmtUpdate->bindParam('uuid_new', $sNewUUID, PDO::PARAM_STR);
// 						echo $aRow['uuid'] . ' -> ' . $sNewUUID . '<br/>';
// 						$stmtUpdate->debug();
						$stmtUpdate->execute();
					}
				}
				
// 				$this->logEvent(System::MAINTENANCE, 'UUID_OPTIMIZATION_ENDED', 'Conversion to sbUUIDs finished');
				break;
			
		}
		
		return ($this->nodeSubject);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
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
			$sMPath = $sMPath.substr(sha1($sCurrentUUID), -REPOSITORY_MPHASH_SIZE);
		}
		
		$iOrder = 0;
		foreach ($aResultset as $aRow) {
			// FIX: there are logical problems with non-primary links, the "true" is a quick workaround (which means the last link will "win")
			if (true || !isset($aRow['b_primary']) || $aRow['b_primary'] == 'TRUE') {
				$this->rebuildMaterializedPaths($aRow['uuid'], $sMPath, $iLevel+1);
			}
			$stmtSetCoordinates = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/setCoordinates/MPath');
			$stmtSetCoordinates->bindParam('fk_child', $aRow['uuid'], PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('fk_parent', $sCurrentUUID, PDO::PARAM_STR);
			$stmtSetCoordinates->bindParam('level', $iLevel, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('order', $iOrder, PDO::PARAM_INT);
			$stmtSetCoordinates->bindParam('mpath', $sMPath, PDO::PARAM_STR);
			$stmtSetCoordinates->execute();
			$iOrder++;
		}
		
	}
	
}

?>