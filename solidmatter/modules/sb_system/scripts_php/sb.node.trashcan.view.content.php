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
				$this->nodeSubject->loadChildren('debug', TRUE, FALSE, TRUE);
				$this->nodeSubject->storeChildren();
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