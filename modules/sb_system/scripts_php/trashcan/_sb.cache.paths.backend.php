<?php

import('sb.cache.paths');

class BackendPathCache extends PathCache {
	
	private $aPrecache = array();
	
	public function storeData($sKey, $mData) {
		return(parent::storeData('backend::'.$sKey, $mData));
	}
	
	public function loadData($sKey) {
		return(parent::loadData('backend::'.$sKey));
	}
	
	/*public function exists($sKey) {
		global $_SBSESSION;
		if (isset($_SBSESSION->aData['paths'][$sKey])) {
			return (TRUE);
		}
		return (FALSE);
	}*/
	
	public function clear($sKey = '') {
		parent::clear('backend::'.$sKey);
	}
	
	
	
	
	
}



?>