<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbIdM]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_idm_system_details extends sbView {
	
	protected $aUserAssignableRoles = array();
	protected $aMainRoles = array();
	
	protected $aRequiredAuthorisations = array(
		
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		//$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'display':
				break;
			case 'print':
				
				ini_set('max_execution_time', '600');
				
				$sMode = $this->requireParam('mode');
				
				if ($sMode == 'usermain') {
					
					$this->gatherUserAssignableRoles($this->nodeSubject);
					$this->gatherMainRoles($this->nodeSubject);
					
					$niUserAssignableRoles = new sbCR_NodeIterator($this->aUserAssignableRoles);
					$niMainRoles = new sbCR_NodeIterator($this->aMainRoles);
					
					foreach ($niUserAssignableRoles as $nodeCurrent) {
						$nodeCurrent->storeRelevantData();
					}
					foreach ($niMainRoles as $nodeCurrent) {
						$nodeCurrent->storeRelevantData();
					}
					
					$_RESPONSE->addData($niUserAssignableRoles, 'userassignable_roles');
					$_RESPONSE->addData($niMainRoles, 'main_roles');
					
				} elseif ($sMode == 'dsb') {
					
					$this->gatherUserAssignableRoles($this->nodeSubject);
					
					$niUserAssignableRoles = new sbCR_NodeIterator($this->aUserAssignableRoles);
					
					foreach ($niUserAssignableRoles as $nodeCurrent) {
						$nodeCurrent->storeRelevantData(TRUE, FALSE, TRUE);
					}
					
					$_RESPONSE->addData($niUserAssignableRoles, 'userassignable_roles');
				
				} elseif ($sMode == 'userall') {
					
					$this->gatherUserAssignableRoles($this->nodeSubject);
					$niUserAssignableRoles = new sbCR_NodeIterator($this->aUserAssignableRoles);
					
					foreach ($niUserAssignableRoles as $nodeCurrent) {
						$nodeCurrent->storeRelevantData(TRUE, FALSE, FALSE);
					}
					
					$_RESPONSE->addData($niUserAssignableRoles, 'userassignable_roles');
					
				}
				
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
	protected function gatherUserAssignableRoles($nodeCurrent) {
		
		$niChildren = $nodeCurrent->loadChildren('debug', TRUE, TRUE);
		foreach ($niChildren as $nodeChild) {
			if ($nodeChild->getPrimaryNodeType() == 'sbIdM:TechRole') {
				if ($nodeChild->getProperty('userassignable') == 'TRUE') {
					$nodeChild->storeRelations();
					$this->aUserAssignableRoles[$nodeChild->getProperty('jcr:uuid')] = $nodeChild;
				}
			}
			$this->gatherUserAssignableRoles($nodeChild);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function gatherMainRoles($nodeCurrent) {
		
		$niChildren = $nodeCurrent->loadChildren('debug', TRUE, TRUE);
		foreach ($niChildren as $nodeChild) {
			if ($nodeChild->getPrimaryNodeType() == 'sbIdM:TechRole') {
				if ($nodeChild->getProperty('mainrole') == 'TRUE') {
					$nodeChild->storeRelations();
					$this->aMainRoles[$nodeChild->getProperty('jcr:uuid')] = $nodeChild;
				}
			}
			$this->gatherMainRoles($nodeChild);
		}
		
	}
	
}

?>