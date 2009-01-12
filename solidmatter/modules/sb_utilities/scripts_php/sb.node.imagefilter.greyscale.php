<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbUtilities:sb.node.imagefilter');

//------------------------------------------------------------------------------
/**
*/
class sbNode_imagefilter_greyscale extends Imagefilter {
	
	public function applyToImage($imgCurrent) {
		
		$iStrength = $this->getProperty('config_strength');
		
		if ($this->getProperty('config_strength') == 100) { 
			$imgCurrent->applyFilter(IMG_FILTER_GRAYSCALE);
		} else {
			$imgGrey = $imgCurrent->copy();
			$imgGrey->applyFilter(IMG_FILTER_GRAYSCALE);
			$imgCurrent->mix($imgGrey, $iStrength);
		}
		
	}
	
	public function applyToNode($sNodeID) {
		
		
		
	}
	
}

?>