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
class sbView_list extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				$niChildren = $this->nodeSubject->loadChildren('list', TRUE, TRUE, TRUE);
				foreach($niChildren as $nodeChild) {
					$_RESPONSE->addLocale($nodeChild->getModule());
				}
				$this->nodeSubject->storeChildren();
				return;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
			
	}
	
}


?>