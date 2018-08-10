<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.system.debug');

//------------------------------------------------------------------------------
/**
*/
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
				$aStructure = $this->getTreeStructure();
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
	private function getTreeStructure(string $sParentUUID = NULL, DOMElement $elemParent = NULL, int $iLevel = 0) {
		
		global $_RESPONSE;
		
		if ($sParentUUID == NULL) {
		
			$nodeRoot = $this->crSession->getRootNode();
			$sParentUUID = $nodeRoot->getProperty('uuid');
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
				$elemChild->setAttribute('uuid', $aRow['uuid']);
				$elemChild->setAttribute('level', $aRow['n_level']);
				$elemChild->setAttribute('order', $aRow['n_order']);
				$elemChild->setAttribute('name', $aRow['s_name']);
				$elemChild->setAttribute('nodetype', $aRow['s_type']);
				$elemChild->setAttribute('primary', $aRow['b_primary']);
				$elemChild->setAttribute('mpath', $aRow['s_mpath']);
				$elemParent->appendChild($elemChild);
				
				if ($aRow['n_numchildren'] != 0) {
					$this->getTreeStructure($aRow['uuid'], $elemChild, ++$iLevel);
				}
			}
		}
	}
	
}

?>