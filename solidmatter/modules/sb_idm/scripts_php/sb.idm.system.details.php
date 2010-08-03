<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_idm_system_details extends sbView {
	
	protected $aMainRoles = array();
	
	protected $aRequiredAuthorisations = array(
		
	);
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		//$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'display':
				
				$this->gatherMainRoles($this->nodeSubject);
				
				$niMainRoles = new sbCR_NodeIterator($this->aMainRoles);
				
				foreach ($niMainRoles as $nodeMain) {
					$nodeMain->storeRelevantData();
				}
				
				$_RESPONSE->addData($niMainRoles, 'main_roles');
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
	protected function gatherMainRoles($nodeCurrent) {
		
		$niChildren = $nodeCurrent->loadChildren('debug', TRUE, TRUE);
		foreach ($niChildren as $nodeChild) {
			if ($nodeChild->getPrimaryNodeType() == 'sbIdM:TechRole') {
				if ($nodeChild->getProperty('mainrole') == 'TRUE') {
					$this->aMainRoles[$nodeChild->getProperty('jcr:uuid')] = $nodeChild;
				}
			}
			$this->gatherMainRoles($nodeChild);
		}
		
	}
	
}

?>