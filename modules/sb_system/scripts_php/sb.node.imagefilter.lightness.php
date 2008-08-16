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
class sbNode_imagefilter_lightness extends Imagefilter {
	
	public function applyToImage($imgCurrent) {
		
		$iBrightness = (int) $this->getProperty('config_brightness');
		$iContrast = (int) $this->getProperty('config_contrast');
		$sContrastFirst = $this->getProperty('config_contrastfirst');
		
		if ($sContrastFirst == 'TRUE') {
			$imgCurrent->applyFilter(IMG_FILTER_CONTRAST, 0-$iContrast);
			$imgCurrent->applyFilter(IMG_FILTER_BRIGHTNESS, $iBrightness);
		} else {
			$imgCurrent->applyFilter(IMG_FILTER_BRIGHTNESS, $iBrightness);
			$imgCurrent->applyFilter(IMG_FILTER_CONTRAST, 0-$iContrast);
		}
		
	}
	
	public function applyToNode($sNodeID) {
		
		
		
	}
	
}

?>