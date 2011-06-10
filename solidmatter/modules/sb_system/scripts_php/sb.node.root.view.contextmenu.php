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
				
				$sNodePath = $this->requireParam('subjectnode');
				$sParentPath = $this->requireParam('parentnode');
				
				$nodeParent = $this->crSession->getNode($sParentPath);
				$sParentUUID = $nodeParent->getProperty('jcr:uuid');
				
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