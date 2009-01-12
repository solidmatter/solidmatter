<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sbUtilities
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbUtilities:sb.node.imagefilter');

//------------------------------------------------------------------------------
/**
*/
class sbNode_imagefilterstack extends Imagefilter {
	
	public function applyToImage($imgCurrent) {
		
		$niChildNodes = $this->getNodes();
		
		if ($this->getProperty('config_reverse') == 'TRUE') {
			$niChildNodes->reverse();	
		}
		
		foreach ($niChildNodes as $nodeCurrent) {
			$nodeCurrent->applyToImage($imgCurrent);
		}
		
	}
	
	public function applyToNode($sNodeID) {
		
		throw new LazyBastardException('Whoops, applyToNode not implemented yet.');
		
	}
	
	public function save() {
		parent::save();
		// TODO: check if clearCache is necessary
		$this->clearCache();
	}
	
	public function clearCache() {
		$cacheImages = CacheFactory::getInstance('images');
		$cacheImages->clearFilterstack($this->getProperty('jcr:uuid'));
		
		$niParents = $this->getParents();
		foreach ($niParents as $nodeCurrent) {
			if ($nodeCurrent->getPrimaryNodeType() == 'sbUtilities:Imagefilterstack') {
				$nodeCurrent->clearCache();
			}
		}
	}
	
}

?>