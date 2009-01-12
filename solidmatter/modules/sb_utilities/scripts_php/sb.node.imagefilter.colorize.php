<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbUtilities:sb.node.imagefilter');
import('sb.tools.colors');

//------------------------------------------------------------------------------
/**
*/
class sbNode_imagefilter_colorize extends Imagefilter {
	
	public function applyToImage($imgCurrent) {
		
		$sHexColor = $this->getProperty('config_color');
		$iStrength = $this->getProperty('config_strength');
		$aRGB = hex2rgb($sHexColor);
		
		if ($this->getProperty('config_strength') == 100) { 
			$imgCurrent->applyFilter(IMG_FILTER_COLORIZE, $aRGB['r'], $aRGB['g'], $aRGB['b']);
		} else {
			$imgColorized = $imgCurrent->copy();
			$imgColorized->applyFilter(IMG_FILTER_COLORIZE, $aRGB['r'], $aRGB['g'], $aRGB['b']);
			$imgCurrent->mix($imgColorized, $iStrength);
		}
		
	}
	
	public function applyToNode($sNodeID) {
		
		
		
	}
	
}

?>