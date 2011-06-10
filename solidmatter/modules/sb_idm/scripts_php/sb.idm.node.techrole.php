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
class sbNode_idm_techrole extends sbNode {
	
	protected $aOrgRoles = array();
	protected $aPersons = array();
	protected $aDuties = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getDefaultViewName() {
		return ('properties');	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeRelevantData($bIncludeSubRoles = FALSE, $bIncludeOrgRoles = TRUE, $bIncludePersons = TRUE) {
		
		
		// raw data
		// TODO: use more specific filtering of actions/output
		if ($bIncludeOrgRoles || $bIncludePersons) {
		
			foreach ($this->getParents() as $nodeParent) {
				if ($nodeParent->getPrimaryNodeType() == 'sbIdM:OrgRole') {
					$this->aOrgRoles[$nodeParent->getProperty('jcr:uuid')] = $nodeParent;
					$this->gatherPersons($nodeParent);
				}
			}
			
			$this->addContent('OrgRoles', new sbCR_NodeIterator($this->aOrgRoles));
			$this->addContent('Persons', new sbCR_NodeIterator($this->aPersons));
			
		}
		
		
		//$this->initTags();
		
		$this->aGetElementFlags['tags'] = TRUE;
		$this->aGetElementFlags['children'] = TRUE;
		$this->aGetElementFlags['content'] = TRUE;
		
		// subrole data
		if ($bIncludeSubRoles) {
			$this->gatherTechRoles(TRUE);
		}
		
		// duties
		/*if ($this->getProperty('userassignable') == 'TRUE') {
			
		}
		$this->addContent('Duties', new sbCR_NodeIterator($this->aDuties));*/
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function gatherPersons($nodeCurrent) {
		
		// dirty hack, clean up gatherSomething-code and remove
		if (!$nodeCurrent instanceof sbNode) {
			return;
		}
		
		foreach ($nodeCurrent->getChildren() as $nodeChild) {
			if ($nodeChild->getPrimaryNodeType() == 'sbIdM:Person') {
				$this->aPersons[$nodeChild->getProperty('jcr:uuid')] = $nodeChild;
			} else {
				$this->gatherPersons($nodeChild);
			}
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function gatherTechRoles($bDeep = FALSE) {
		
		$niChildren = $this->loadChildren('gatherTechRoles', TRUE, TRUE, TRUE);
		
		foreach ($niChildren as $nodeChild) {
			$nodeChild->aGetElementFlags['tags'] = TRUE;
			$nodeChild->aGetElementFlags['children'] = TRUE;
			$nodeChild->aGetElementFlags['content'] = TRUE;
			if ($bDeep) {
				$nodeChild->gatherTechRoles(TRUE);
			}
		}
		
		$this->storeChildren('gatherTechRoles');
		
	}
	
	
}

?>