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
				
				import('sb.pdo.repository.queries.sbuuid');
				import('sbJukebox:sb.pdo.queries');
				
				ob_end_clean();
				
// 				$this->logEvent(System::MAINTENANCE, 'UUID_OPTIMIZATION_STARTED', 'started to convert to sbUUIDs');
				$stmtSelect = $this->crSession->prepareKnown('sbSystem/sbUUID/getAllUUIDs');
				$stmtSelect->execute();
				$aResultset = $stmtSelect->fetchALL(PDO::FETCH_ASSOC);
				
				$stmtUpdateRoot = $this->crSession->prepareKnown('sbSystem/sbUUID/updateRoot');
				
				// always update these
				$aUpdates['All'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateUUID');
				$aUpdates['All'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateEventlog/subject');
				$aUpdates['All'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateProperties');
				
				// system nodes
				$aUpdates['sbSystem:Root'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateRegistry');
				$aUpdates['sbSystem:Root'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateEventlog/user');
				$aUpdates['sbSystem:User'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateRegistry');
				$aUpdates['sbSystem:User'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateEventlog/user');
				$aUpdates['sbSystem:User'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateNodes/created');
				$aUpdates['sbSystem:User'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateNodes/modified');
				
				// jukebox stuff
				$aUpdates['sbJukebox:Artist'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateAlbums/artist');
				$aUpdates['sbJukebox:Artist'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateAlbums/albumartist');
				$aUpdates['sbJukebox:Album'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateTracks');
				$aUpdates['sbSystem:User'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateAlbumhistory/user');
				$aUpdates['sbSystem:User'][] = $this->crSession->prepareKnown('sbSystem/sbUUID/updateTrackhistory/user');
				
				echo '<body style="font-family: Andale Mono, monospace;">';
				
				foreach ($aResultset as $aRow) {
					
					$this->crSession->beginTransaction('sbUUID');
					
					if ($aRow['uuid'] == '00000000000000000000000000000000') {
						$sNewUUID = '0000000000000000000000';
						$stmtUpdateRoot->execute();
						$stmtUpdateRoot->closeCursor();
					} else {
						$sNewUUID = sbUUID::create();
					}
					
					$iQueries = 0;
					if (sbUUID::issbUUID($aRow['uuid'])) {
						echo 'Skipped '.$aRow['uuid'].', already sbUUID<br/>';
					} else {
						foreach ($aUpdates as $sNodetype => $aStatements) {
							if ($sNodetype == $aRow['fk_nodetype'] || $sNodetype == 'All') {
								foreach ($aStatements as $stmtUpdate) {
// 									echo 'Nodetype '.$aRow['nodetype'].' Query '.$stmtUpdate->getStatementID().'<br/>';
									$stmtUpdate->bindParam('uuid_old', $aRow['uuid'], PDO::PARAM_STR);
									$stmtUpdate->bindParam('uuid_new', $sNewUUID, PDO::PARAM_STR);
									$stmtUpdate->execute();
									//$stmtUpdate->debug();
									$iQueries++;
								}
							}
						}
					}
					
					$this->crSession->commit('sbUUID');
					if ($iQueries > 0) {
						echo 'Updated '.$aRow['fk_nodetype'].' - '.$aRow['uuid'].' => '.$sNewUUID.' ('.$iQueries.' queries)<br/>';
					}
				}
				
// 				
				
				$this->logEvent(System::MAINTENANCE, 'UUID_OPTIMIZATION_ENDED', 'Conversion to sbUUIDs finished');
				
				// clear all caches
				$aCaches = array(
					'system',
					'paths',
					'registry',
					'images',
					'authorisations',
					'repository',
					'misc',
				);
				foreach ($aCaches as $sCurrentCache) {
					$cacheCurrent = CacheFactory::getInstance($sCurrentCache);
					$cacheCurrent->clear();
					$this->logEvent(System::MAINTENANCE, 'CACHE_CLEARED', $sCurrentCache);
				}
				sbSession::destroy();
				
				exit();
				
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
	private function rebuildMaterializedPaths($sParentUUID = NULL, $sMPath = NULL, $iLevel = 0) {
		
		static $stmtSetCoordinates = NULL;
		if ($stmtSetCoordinates == NULL) {
			$stmtSetCoordinates = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/setCoordinates/MPath');
		}
		
		if ($sParentUUID === NULL) {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/loadRoot');
		} else {
			$stmtLoadChildren = $this->crSession->prepareKnown('sbSystem/maintenance/view/repair/loadChildren');
			$stmtLoadChildren->bindValue('fk_parent', $sParentUUID, PDO::PARAM_STR);
		}
		$stmtLoadChildren->execute();
		$aResultset = $stmtLoadChildren->fetchALL(PDO::FETCH_ASSOC);
		$stmtLoadChildren->closeCursor();
// 		var_dumppp($aResultset);
		if ($iLevel == 0 || $iLevel == 1) {
			$sMPath = '';
		} else {
			$sMPath = $sMPath.sbUUID::generateMPath($sParentUUID);
		}
		
		$iOrder = 0;
		foreach ($aResultset as $aRow) {
			
			$stmtSetCoordinates->bindValue('fk_child', $aRow['uuid'], PDO::PARAM_STR);
			$stmtSetCoordinates->bindValue('fk_parent', $sParentUUID, PDO::PARAM_STR);
			$stmtSetCoordinates->bindValue('level', $iLevel, PDO::PARAM_INT);
			$stmtSetCoordinates->bindValue('order', $iOrder, PDO::PARAM_INT);
			$stmtSetCoordinates->bindValue('mpath', $sMPath, PDO::PARAM_STR);
			$stmtSetCoordinates->execute();
			$stmtSetCoordinates->closeCursor();
			$iOrder++;
			
			// FIX: there are logical problems with non-primary links, the "true" is a quick workaround (which means the last link will "win")
			if (true || !isset($aRow['b_primary']) || $aRow['b_primary'] == 'TRUE') {
				$this->rebuildMaterializedPaths($aRow['uuid'], $sMPath, $iLevel+1);
			}
			
		}
		
	}
	
}

?>