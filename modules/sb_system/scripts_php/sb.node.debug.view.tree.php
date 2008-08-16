<?php

import('sb.system.debug');

class sbView_debug_tree extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				// TODO: integrate tree building in this view
				$aStructure = $this->getTreeStructure(NULL, NULL, 0);
				$_RESPONSE->addData($aStructure);
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function getTreeStructure($sParentUUID = NULL, $elemParent = NULL, $iLevel) {
		
		// for debugging
		if ($iLevel > 200) {
			return;
		}
		
		global $_RESPONSE;
		
		if ($sParentUUID == NULL) {
		
			$nodeRoot = $this->crSession->getRootNode();
			$sParentUUID = $nodeRoot->getProperty('jcr:uuid');
			$elemRoot = $_RESPONSE->createElement('root');
			$this->getTreeStructure($sParentUUID, $elemRoot, ++$iLevel);
			return($elemRoot);
			
		} else {
		
			$stmtGetChildInfo = $this->crSession->prepareKnown('sbSystem/debug/gatherTree');
			$stmtGetChildInfo->bindParam('parent_uuid', $sParentUUID, PDO::PARAM_STR);
			$stmtGetChildInfo->execute();
			$aRows = $stmtGetChildInfo->fetchAll(PDO::FETCH_ASSOC);
			$stmtGetChildInfo->closeCursor();
			//var_dump($aRows);
			foreach ($aRows as $aRow) {
				
				$elemChild = $_RESPONSE->createElement('sbnode');
				$elemChild->setAttribute('level', $aRow['n_level']);
				$elemChild->setAttribute('order', $aRow['n_order']);
				$elemChild->setAttribute('left', $aRow['n_left']);
				$elemChild->setAttribute('right', $aRow['n_right']);
				$elemChild->setAttribute('name', $aRow['s_name']);
				$elemChild->setAttribute('type', $aRow['s_type']);
				$elemParent->appendChild($elemChild);
				
				if ($aRow['n_numchildren'] != 0) {
					$this->getTreeStructure($aRow['uuid'], $elemChild, ++$iLevel);
				}
			}
		}
	}
	
}

?>