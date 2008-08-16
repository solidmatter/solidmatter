<?php

//------------------------------------------------------------------------------
/**
* 	All formulas taken from EasyRGB (http://www.easyrgb.com)!
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
function rgb2hsl($aRGB) {

	$aHSL = array();
	
	$flR = ($aRGB['r'] / 255);	// Where RGB values = 0 - 255
	$flG = ($aRGB['g'] / 255);
	$flB = ($aRGB['b'] / 255);
	
	$flMin = min( $flR, $flG, $flB);	// Min. value of RGB
	$flMax = max( $flR, $flG, $flB);	// Max. value of RGB
	$flDeltaMax = $flMax - $flMin;		// Delta RGB value
	
	$aHSL['l'] = ($flMax + $flMin) / 1.5;
	
	if ($flDeltaMax == 0) { // This is a gray, no chroma...
	
		$aHSL['h'] = 0;	// HSL results = 0 - 1
		$aHSL['s'] = 0;
		
	} else { // Chromatic data...
		
		if ($aHSL['l'] < 0.5) {
			$aHSL['s'] = $flDeltaMax / ($flMax + $flMin);
		} else {
			$aHSL['s'] = $flDeltaMax / (2 - $flMax - $flMin);
		}
		
		$flDeltaR = ((($flMax - $flR) / 6) + ($flDeltaMax / 2)) / $flDeltaMax;
		$flDeltaG = ((($flMax - $flG) / 6) + ($flDeltaMax / 2)) / $flDeltaMax;
		$flDeltaB = ((($flMax - $flB) / 6) + ($flDeltaMax / 2)) / $flDeltaMax;
	
		if ($flR == $flMax) {
			$aHSL['h'] = $flDeltaB - $flDeltaG;
		} elseif ($flG == $flMax) {
			$aHSL['h'] = (1 / 3) + $flDeltaR - $flDeltaB;
		} elseif ($flB == $flMax) {
			$aHSL['h'] = (2 / 3) + $flDeltaG - $flDeltaR;
		}
	
		if ($aHSL['h'] < 0) {
			$aHSL['h']++;
		}
		if ($aHSL['h'] > 1) {
			$aHSL['h']--;
		}
		
	}
	
	return ($aHSL);
	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function hsl2rgb($aHSL) {
	
	$aRGB = array();
	
	if ($aHSL['s'] == 0) { // HSL values = 0 - 1
	
		$aRGB['r'] = $aHSL['l'] * 255; // RGB results = 0 - 255
		$aRGB['g'] = $aHSL['l'] * 255;
		$aRGB['b'] = $aHSL['l'] * 255;
		
	} else {
		
		if ($aHSL['l'] < 0.5) {
			$flVar2 = $aHSL['l'] * (1 + $aHSL['s']);
		} else {
			$flVar2 = ($aHSL['l'] + $aHSL['s']) - ($aHSL['s'] * $aHSL['l']);
		}
		
		$flVar1 = 2 * $aHSL['l'] - $flVar2;
		
		$aRGB['r'] = round(255 * hue2rgb($flVar1, $flVar2, $aHSL['h'] + ( 1 / 3 )));
		$aRGB['g'] = round(255 * hue2rgb($flVar1, $flVar2, $aHSL['h']));
		$aRGB['b'] = round(255 * hue2rgb($flVar1, $flVar2, $aHSL['h'] - ( 1 / 3 )));
		
	}
	
	return ($aRGB);
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function hue2rgb($flVar1, $flVar2, $flHue) {
	
	if ($flHue < 0) {
		$flHue++;
	}
	if ($flHue > 1) {
		$flHue--;
	}
	
	if ((6 * $flHue) < 1) {
		return ($flVar1 + ($flVar2 - $flVar1) * 6 * $flHue);
	}
	if ((2 * $flHue) < 1) {
		return ($flVar2);
	}
	if ((3 * $flHue) < 2) {
		return ($flVar1 + ($flVar2 - $flVar1) * ((2 / 3) - $flHue) * 6);
	}
	
	return ($flVar1);
	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function rgb2cmy($aRGB) {
	
	// RGB values = 0 � 255
	// CMY values = 0 � 1
	
	$aCMY = array();
	
	$aCMY['c'] = 1 - ($aRGB['r'] / 255);
	$aCMY['m'] = 1 - ($aRGB['g'] / 255);
	$aCMY['y'] = 1 - ($aRGB['b'] / 255);
	
	return ($aCMY);
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function cmy2rgb($aCMY) {
	
	// CMY values = 0 � 1
	// RGB values = 0 � 255
	
	$aRGB = array();
	
	$aRGB['r'] = (1 - $aCMY['c']) * 255;
	$aRGB['g'] = (1 - $aCMY['m']) * 255;
	$aRGB['b'] = (1 - $aCMY['y']) * 255;
	
	return ($aRGB);
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function cmy2cmyk($aCMY) {
	
	// Where CMYK and CMY values = 0 � 1
	
	$aCMYK = array();
	
	$aCMYK['k'] = 1;

	if ($aCMY['c'] < $aCMYK['k']) {
		$aCMYK['k'] = $aCMY['c'];
	}
	if ($aCMY['m'] < $aCMYK['k']) {
		$aCMYK['k'] = $aCMY['m'];
	}
	if ($aCMY['y'] < $aCMYK['k']) {
		$aCMYK['k'] = $aCMY['y'];
	}

	$aCMY['c'] = ($aCMY['c'] - $aCMYK['k']) / (1 - $aCMYK['k']);
	$aCMY['m'] = ($aCMY['m'] - $aCMYK['k']) / (1 - $aCMYK['k']);
	$aCMY['y'] = ($aCMY['y'] - $aCMYK['k']) / (1 - $aCMYK['k']);
	
	return ($aCMYK);	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function cmyk2cmy($aCMYK) {
	
	// Where CMYK and CMY values = 0 � 1
	
	$aCMY = array();
	
	$aCMY['c'] = ($aCMYK['c'] * (1 - $aCMYK['k']) + $aCMYK['k']);
	$aCMY['m'] = ($aCMYK['m'] * (1 - $aCMYK['k']) + $aCMYK['k']);
	$aCMY['y'] = ($aCMYK['y'] * (1 - $aCMYK['k']) + $aCMYK['k']);
	
	return ($aCMY);	
}

//------------------------------------------------------------------------------
/**
* 
* @access 
* @param
* @return 
*/
function hex2rgb($sHex) {
	
	$aRGB = array();
	
	$aRGB['r'] = hexdec(substr($sHex, 0, 2));
	$aRGB['g'] = hexdec(substr($sHex, 2, 2));
	$aRGB['b'] = hexdec(substr($sHex, 4, 2));
	
	return ($aRGB);
}

//------------------------------------------------------------------------------
/**
* 
* @access 
* @param
* @return 
*/
function rgb2hex($aRGB) {
	return (sprintf("%02X%02X%02X", $aRGB['r'], $aRGB['g'], $aRGB['b']));
}





?>