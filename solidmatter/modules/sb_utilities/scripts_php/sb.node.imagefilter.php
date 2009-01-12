<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sbUtilities
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
abstract class Imagefilter extends sbNode {
	
	abstract function applyToImage($imgSubject);
	abstract function applyToNode($sNodeID);
	
	public function save() {
		if (Registry::getValue('sb.system.cache.images.enabled')) {
			$niParents = $this->getParents();
			foreach ($niParents as $nodeCurrent) {
				if ($nodeCurrent->getPrimaryNodeType() == 'sbUtilities:Imagefilterstack') {
					$nodeCurrent->clearCache();
				}
			}
		}
		parent::save();
	}
	
}

?>