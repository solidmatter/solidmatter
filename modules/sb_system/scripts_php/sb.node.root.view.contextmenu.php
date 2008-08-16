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
class sbView_root_contextmenu extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		global $_REQUEST;
		
		switch ($sAction) {
			
			case 'generate':
				
				// TODO: change to requireParam()
				$sNodePath = $_REQUEST->getParam('subjectnode');
				$sParentPath = $_REQUEST->getParam('parentnode');
				
				if ($sParentPath != NULL) {
					$nodeParent = $this->crSession->getNode($sParentPath);
					$sParentUUID = $nodeParent->getProperty('jcr:uuid');
				} else {
					$sParentUUID = NULL;
				}
				
				$nodeCurrent = $this->crSession->getNode($sNodePath);
				$elemContextMenu = $nodeCurrent->getContextMenu($sParentUUID);
				$elemContextMenu->setAttribute('path', str_replace('/', ':', $sNodePath));
				
				$_RESPONSE->addData($elemContextMenu);
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
		
		}
		
	}
		
}


?>