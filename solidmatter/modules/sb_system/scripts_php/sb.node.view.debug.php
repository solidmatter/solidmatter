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
class sbView_debug extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		$crNodeType = $this->crSession->getWorkspace()->getNodeTypeManager()->getNodeType($this->nodeSubject->getPrimaryNodeType());
		
		$this->nodeSubject->loadChildren('debug', TRUE);
		$this->nodeSubject->storeChildren();
		//$this->nodeSubject->loadAncestors();
		//$this->nodeSubject->storeAncestors();
		$this->nodeSubject->loadParents();
		$this->nodeSubject->storeParents();
		//$this->nodeSubject->setDeepMode(TRUE);
		$this->nodeSubject->loadProperties();
		$this->nodeSubject->getTags();
		
		$this->nodeSubject->loadUserAuthorisations();
		$this->nodeSubject->loadSecurityAuthorisations();
		
		$this->nodeSubject->setAttribute('full_path', $this->nodeSubject->getPath());
		$this->nodeSubject->setAttribute('internal_path', $this->nodeSubject->getMPath());
		
		$this->nodeSubject->storeSupertypeNames();
		$this->nodeSubject->storeSupportedLifecycleTransitions();
		$this->nodeSubject->storeSupportedRelations();
		
		return (NULL);
		
	}
	
}


?>