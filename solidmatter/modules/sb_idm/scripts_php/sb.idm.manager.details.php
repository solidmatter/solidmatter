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
class sbView_idm_manager_details extends sbView {
	
	protected $aOrgRoles = array();
	protected $aInheritedOrgRoles = array();
	
	protected $aRequiredAuthorisations = array(
		
	);
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		//$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'display':
				
				$this->nodeSubject->getBranchTags();
				
				$iTagID = $_REQUEST->getParam('tagid');
				if ($iTagID != NULL) {
					$iTagID = (int) $iTagID;
					$niTaggedItems = $this->nodeSubject->getBranchNodesByTag($iTagID);
					foreach ($niTaggedItems as $nodeTagged) {
						$nodeTagged->loadProperties();
						
					}
					$this->nodeSubject->addContent('tagged', $niTaggedItems);
					$this->nodeSubject->aGetElementFlags['content'] = TRUE;
				}
				
				/*$niPersons = new sbCR_NodeIterator();
				foreach ($niTaggedItems as $nodeCurrent) {
					if ($nodeCurrent->getPrimaryNodeType() == 'sbIdM:TechRole') {
						$niPersons->append($nodeCurrent->gatherPersons());
					}
				}
				$niPersons->makeUnique();
				
				$_RESPONSE->addData($niPersons, 'persons');*/
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
}

?>