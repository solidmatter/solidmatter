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
	public function gatherPersons() {
		
		$niPersons = new sbCR_NodeIterator();
		
		foreach ($this->getChildren() as $nodeChild) {
			if ($nodeChild->getPrimaryNodeType() == 'sbIdM:Person') {
				$this->aPersons[$nodeChild->getProperty('jcr:uuid')] = $nodeChild;
			} else {
				$niPersons->append($nodeChild->gatherPersons());
			}
			
		}
		
		$niPersons->append(new sbCR_NodeIterator($this->aPersons));
		$niPersons->makeUnique();
		
		return ($niPersons);
		
	}
	
}

?>