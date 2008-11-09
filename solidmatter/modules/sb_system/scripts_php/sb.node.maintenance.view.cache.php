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
				if ($sCache == 'all') {
					$aCaches = array(
						'system',
						'paths',
						'registry',
						'images',
						'authorisations',
						'repository',
						'misc',
					);
				} else {
					$aCaches = array($sCache);
				}
				// actually clear caches	
				foreach ($aCaches as $sCurrentCache) {
					$cacheCurrent = CacheFactory::getInstance($sCurrentCache);
					$cacheCurrent->clear();
					$this->logEvent(System::MAINTENANCE, 'CACHE_CLEARED', $sCurrentCache);
				}
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
		return ($this->nodeSubject);
		
	}	
	
}

?>