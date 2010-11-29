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
	public function storeRelevantData($bIncludeSubRoles = FALSE) {
		
		
		// raw data
		foreach ($this->getParents() as $nodeParent) {
			if ($nodeParent->getPrimaryNodeType() == 'sbIdM:OrgRole') {
				$this->aOrgRoles[$nodeParent->getProperty('jcr:uuid')] = $nodeParent;
				$this->gatherPersons($nodeParent);
			}
		}
		
		$this->addContent('OrgRoles', new sbCR_NodeIterator($this->aOrgRoles));
		$this->addContent('Persons', new sbCR_NodeIterator($this->aPersons));
		
		//$this->initTags();
		
		$this->aGetElementFlags['tags'] = TRUE;
		$this->aGetElementFlags['children'] = TRUE;
		$this->aGetElementFlags['content'] = TRUE;
		
		// subrole data
		if ($bIncludeSubRoles) {
			$this->gatherSubRoles($this);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function gatherPersons($nodeCurrent) {
		
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
	protected function gatherSubRoles($nodeCurrent) {
		
		$niChildren = $nodeCurrent->loadChildren('debug', TRUE, TRUE);
		
		foreach ($niChildren as $nodeChild) {
			//$nodeChild->initTags();
			$nodeChild->aGetElementFlags['tags'] = TRUE;
			$nodeChild->aGetElementFlags['children'] = TRUE;
			$nodeChild->aGetElementFlags['content'] = TRUE;
			$this->gatherSubRoles($nodeChild);
		}
		
		//$nodeCurrent->storeChildren();
		
	}
	
	
}

?>