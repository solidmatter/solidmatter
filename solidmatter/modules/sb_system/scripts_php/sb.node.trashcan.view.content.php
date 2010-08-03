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
class sbView_trashcan_content extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'list':
				// TODO: implement user-specific listing of contents (serves privacy/security)
				$niTrash = $this->nodeSubject->getTrash();
				foreach ($niTrash as $nodeCurrent) {
					$nodeCurrent->loadProperties();
				}
				$_RESPONSE->addData($niTrash, 'trash');
				return;
				
			case 'recover':
				$sSubjectUUID = $_REQUEST->getParam('subject_uuid');
				$sParentUUID = $_REQUEST->getParam('parent_uuid');
				$nodeTrash = $this->crSession->getNodeByIdentifier($sSubjectUUID, $sParentUUID);
				$nodeTrash->recoverFromTrash();
				$nodeTrash->save();
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'content');
				return;
				
			case 'purge':
				$this->nodeSubject->purge();
				return;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
			
	}
	
}


?>