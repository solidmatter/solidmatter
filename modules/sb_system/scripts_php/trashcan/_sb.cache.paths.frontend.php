<?php

import('sb.cache.paths');

class FrontendPathCache extends PathCache {
	
	private $aPrecache = array();
	
	public function storeData($sKey, $mData) {
		return(parent::storeData('frontend::'.$sKey, $mData));
	}
	
	public function loadData($sKey) {
		return(parent::loadData('frontend::'.$sKey));
	}
	
	/*public function exists($sKey) {
		global $_SBSESSION;
		if (isset($_SBSESSION->aData['paths'][$sKey])) {
			return (TRUE);
		}
		return (FALSE);
	}*/
	
	public function clear($sKey = '') {
		parent::clear('frontend::'.$sKey);
	}
	
	
	
	
	
}



?>