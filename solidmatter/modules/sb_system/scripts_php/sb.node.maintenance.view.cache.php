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
class sbView_maintenance_cache extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'showOptions':
				break;
				
			case 'clearCache':
				$sCache = $_REQUEST->getParam('cache');
				//var_dumpp($sCache);
				$cacheCurrent = CacheFactory::getInstance($sCache);
				$cacheCurrent->clear();
				$this->logEvent(System::MAINTENANCE, 'CACHE_CLEARED', $sCache);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');

			
		}
		
		return ($this->nodeSubject);
		
	}	
	
}


?>