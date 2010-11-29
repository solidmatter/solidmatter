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
class sbView_idm_orgrole_details extends sbView {
	
	protected $aOrgRoles = array();
	protected $aInheritedOrgRoles = array();
	
	protected $aRequiredAuthorisations = array(
		
	);
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		//$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'display':
				
				//$crQuery = $this->crSession->getWorkspace()->getQueryManager()->createQuery("test:test[@assasa='sdsdds' and @sweew='sdsdds']/ancestor-or-self::sdsdsd[sdsd=sdsd]/following-siblings::element(*, 'ws:sdsdsd')[@foobar='ss' and @bar='ererer']//*[@nodetype='sdsdd']", 'XPATH');
				/*$crQuery = $this->crSession->getWorkspace()->getQueryManager()->createQuery("//element(*, 'sbIdM:TechRole')[@mainrole='TRUE']", 'XPATH');
				$crQuery->execute();*/
				
				$this->nodeSubject->loadChildren('gatherTechRoles');
				//$this->nodeSubject->storeChildren('gatherTechRoles');
				
				$this->gatherParentRoles();
				
				$niOrgRoles = new sbCR_NodeIterator($this->aOrgRoles);
				$niInheritedOrgRoles = new sbCR_NodeIterator($this->aInheritedOrgRoles);
				
				foreach ($niOrgRoles as $nodeOrgRole) {
					$nodeOrgRole->loadChildren('gatherTechRoles');
					$nodeOrgRole->storeChildren('gatherTechRoles');
				}
				foreach ($niInheritedOrgRoles as $nodeOrgRole) {
					$nodeOrgRole->loadChildren('gatherTechRoles');
					$nodeOrgRole->storeChildren('gatherTechRoles');
				}
				
				$_RESPONSE->addData($niOrgRoles, 'OrgRoles');
				$_RESPONSE->addData($niInheritedOrgRoles, 'InheritedOrgRoles');
				
				$this->gatherChildren($this->nodeSubject);
				
				
				
				break;
			
			default:
				parent::execute($sAction);
				
		}
		
	}

	protected function gatherParentRoles($nodeCurrent = NULL) {
		
		if ($nodeCurrent == NULL) {
			
			$nodeCurrent = $this->nodeSubject;
			foreach ($nodeCurrent->getParents() as $nodeParent) {
				$this->aOrgRoles[$nodeParent->getProperty('jcr:uuid')] = $nodeParent;
				$this->gatherParentRoles($nodeParent);
			}
		
		} else {
			
			foreach ($nodeCurrent->getParents() as $nodeParent) {
				if (!isset($this->aInheritedOrgRoles[$nodeParent->getProperty('jcr:uuid')]) && $nodeParent->isNodeType('sbIdM:OrgRole')) {
					$this->aInheritedOrgRoles[$nodeParent->getProperty('jcr:uuid')] = $nodeParent;
					$this->gatherParentRoles($nodeParent);
				}
			}
			
		}
		
	}
	
	protected function gatherChildren($nodeCurrent) {
		
		$niChildren = $nodeCurrent->loadChildren('debug', TRUE, TRUE);
		foreach ($niChildren as $nodeChild) {
			$this->gatherChildren($nodeChild);
		}
		$nodeCurrent->storeChildren();
		
	}
	
	
}

?>