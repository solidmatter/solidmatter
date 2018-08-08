<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem.directory');

//------------------------------------------------------------------------------
/**
*/
class sbNode_modules extends sbNode {
	
	protected $aModules = array();
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function __init() {
		$dirModules = new sbDirectory('modules');
		$this->aModules = $dirModules->getDirectories(TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function getNumberOfChildren($sMode = NULL) {
		return (count($this->aModules));
	}
	
	//--------------------------------------------------------------------------
	/**
	 * CUSTOM:
	 * @param
	 * @return
	 */
	public function getChildren($sMode = 'debug', $aRequiredAuthorisations = array()) {
		
		if (!isset($this->aChildNodes[$sMode])) { // load children
			
			// 			if ($sMode == 'debug') {
			// 				$stmtChildren = $this->crSession->prepareKnown($this->aQueries['loadChildren']['debug']);
			// 				$mParam = $this->elemSubject->getAttribute('uuid');
			// 				$stmtChildren->bindParam(':parent_uuid', $mParam, PDO::PARAM_STR);
			// 			} else {
			// 				$stmtChildren = $this->crSession->prepareKnown($this->aQueries['loadChildren']['byMode']);
			// 				$mParam = $this->elemSubject->getAttribute('uuid');
			// 				$stmtChildren->bindParam(':parent_uuid', $mParam, PDO::PARAM_STR);
			// 				$stmtChildren->bindParam(':mode', $sMode, PDO::PARAM_STR);
			// 			}
			// 			$stmtChildren->execute();
			// 			$aChildren = $stmtChildren->fetchAll(PDO::FETCH_ASSOC);
			
			
			
			// build new NodeIterator
			$aChildNodes = array();
			foreach ($this->aModules as $dirModule) {
				$nodeCurrentChild = $this->crSession->createVirtualNode('sbSystem:Module', $dirModule->getName(), $dirModule->getName(), 'sbSystem:Modules');
				$aChildNodes[$dirModule->getName()] = $nodeCurrentChild;
			}
			$niChildNodes = new sbCR_NodeIterator($aChildNodes);
			
		} else { // cached
			
			$niChildNodes = $this->aChildNodes[$sMode];
			
		}
		
		// 		// filter nodes after retrieval if necessary
		// 		if (count($aRequiredAuthorisations) > 0) {
		// 			$aFilteredChildNodes = array();
		// 			foreach ($niChildNodes as $nodeCurrentChild) {
		// 				$bCheck = TRUE;
		// 				foreach ($aRequiredAuthorisations as $sAuthorisation) {
		// 					if (!User::isAuthorised($sAuthorisation, $nodeCurrentChild)) {
		// 						$bCheck = FALSE;
		// 					}
		// 				}
		// 				if ($bCheck) {
		// 					$aFilteredChildNodes[] = $nodeCurrentChild;
		// 				}
		// 			}
		// 			$niChildNodes = new sbCR_NodeIterator($aFilteredChildNodes);
		// 		}
		
		return ($niChildNodes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 * @param string the child's name
	 * @return array contains the info on the found child
	 */
	public function getNode($sName) {
		
		$dirModule = new sbDirectory('modules/'.$sName);
		
		$nodeCurrentChild = $this->crSession->createVirtualNode('sbSystem:Module', $dirModule->getName(), $dirModule->getName(), 'sbSystem:Modules');
		
		return ($nodeCurrentChild);
		
	}
	

	
	
}

?>