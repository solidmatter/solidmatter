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
class sbNode_idm_person extends sbNode {
	
	protected $aOrgRoles = array();
	protected $aInheritedOrgRoles = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getDefaultViewName() {
		return ('details');	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*public function getProperty($sName) {
		if (!Registry::getValue('sb.idm.display.persons.enabled')) {
			switch ($sName) {
				case 'label':
					return ('Person');
					break;
				case 'name':
					return ('Person');
					break;
			}
		}
		return (parent::getProperty($sName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @return 
	*/
	protected function getElementModifications($elemSubject) {
		if (!Registry::getValue('sb.idm.display.persons.enabled')) {
			if (substr((string) $elemSubject->getAttribute('label'), 0, 4) != 'Alle') {
				$elemSubject->setAttribute('label', 'Person');
				$elemSubject->setAttribute('name', 'Person');
			}
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeRelevantData() {
		
		$this->gatherOrgRoles();
		
		// raw data
		foreach ($this->aOrgRoles as $nodeOrgRole) {
			$niTechRoles = $nodeOrgRole->loadChildren('gatherTechRoles', TRUE, TRUE, TRUE);
			foreach ($niTechRoles as $nodeTechRole) {
				$nodeTechRole->gatherTechRoles(TRUE);
				$nodeTechRole->aGetElementFlags['children'] = TRUE;
			}
			$nodeOrgRole->storeChildren('gatherTechRoles');
		}
		
		$this->addContent('OrgRoles', new sbCR_NodeIterator($this->aOrgRoles));
		
		$this->aGetElementFlags['content'] = TRUE;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function gatherOrgRoles($nodeCurrent = NULL) {
		
		if ($nodeCurrent == NULL) {
			
			$nodeCurrent = $this;
			foreach ($nodeCurrent->getParents() as $nodeParent) {
				$nodeParent->setAttribute('assigned', 'direct');
				$this->aOrgRoles[$nodeParent->getProperty('jcr:uuid')] = $nodeParent;
				$this->gatherOrgRoles($nodeParent);
			}
		
		} else {
			
			foreach ($nodeCurrent->getParents() as $nodeParent) {
				if (!isset($this->aInheritedOrgRoles[$nodeParent->getProperty('jcr:uuid')]) && $nodeParent->isNodeType('sbIdM:OrgRole')) {
					$nodeParent->setAttribute('assigned', 'inherited');
					if (!isset($this->aOrgRoles[$nodeParent->getProperty('jcr:uuid')])) {
						$this->aOrgRoles[$nodeParent->getProperty('jcr:uuid')] = $nodeParent;
						$this->gatherOrgRoles($nodeParent);
					}
				}
			}
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function gatherTechRoles($nodeCurrent) {
		
		$niChildren = $nodeCurrent->loadChildren('debug', TRUE, TRUE);
		
		foreach ($niChildren as $nodeChild) {
			//$nodeChild->initTags();
			$nodeChild->aGetElementFlags['tags'] = TRUE;
			$nodeChild->aGetElementFlags['children'] = TRUE;
			$nodeChild->aGetElementFlags['content'] = TRUE;
			$this->gatherTechRoles($nodeChild);
		}
		
		//$nodeCurrent->storeChildren();
		
	}
	
	
}

?>