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
class sbNode_idm_orgrole extends sbNode {
	
	protected $aPersons = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function gatherPersons($niPersons = NULL) {
		
		if ($niPersons == NULL) {
			$niPersons = new sbCR_NodeIterator();
		}
		
		foreach ($this->getChildren() as $nodeChild) {
			if ($nodeChild->getPrimaryNodeType() == 'sbIdM:Person') {
				$niPersons->append($nodeChild);
			} else {
				$nodeChild->gatherPersons($niPersons);
			}
		}
		
		$niPersons->makeUnique();
		$niPersons->sortAscending('label');
		
		return ($niPersons);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function gatherOrgRoles($niOrgRoles = NULL) {
		
		if ($niOrgRoles == NULL) {
			$niOrgRoles = new sbCR_NodeIterator();
		}
		
		foreach ($this->getChildren() as $nodeChild) {
			if ($nodeChild->getPrimaryNodeType() == 'sbIdM:Org') {
				$niOrgRoles->append($nodeChild);
			} else {
				$nodeChild->gatherPersons($niOrgRoles);
			}
		}
		
		$niOrgRoles->makeUnique();
		$niOrgRoles->sortAscending('label');
		
		return ($niOrgRoles);
		
	}
	
	
}

?>