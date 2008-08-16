<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.node.imagefilter');

//------------------------------------------------------------------------------
/**
*/
class sbNode_imagefilter_negative extends Imagefilter {
	
	public function applyToImage($imgCurrent) {
		
		$iStrength = $this->getProperty('config_strength');
		
		if ($this->getProperty('config_strength') == 100) {
			$imgCurrent->applyFilter(IMG_FILTER_NEGATE);
		} else {
			$imgNegative = $imgCurrent->copy();
			$imgNegative->applyFilter(IMG_FILTER_NEGATE);
			$imgCurrent->mix($imgNegative, $iStrength);
		}
		
	}
	
	public function applyToNode($sNodeID) {
		
		
		
	}
	
}

?>