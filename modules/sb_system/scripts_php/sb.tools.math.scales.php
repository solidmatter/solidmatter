<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

if (!defined('SCALE_DISTANCE')) 	define('SCALE_DISTANCE',	70);
if (!defined('SCALE_QUANTITY')) 	define('SCALE_QUANTITY',	71);

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function get_diagramscale($flMax, $flMin, $flValue, $iMode) {
	
	$aDistances = array(
		10000,
		7500,
		5000,
		4000,
		3000,
		2500,
		2000,
		1500,
		1000,
		750,
		500,
		400,
		300,
		250,
		200,
		150,
		100,
		75,
		50,
		40,
		30,
		25,
		20,
		15,
		10,
		9,
		8,
		7,
		6,
		5,
		4,
		3,
		2,
		1,
		0.75,
		0.5,
		0.4,
		0.3,
		0.25,
		0.2,
		0.15,
		0.1,
		0.075,
		0.05,
		0.04,
		0.03,
		0.025,
		0.02,
		0.015,
		0.01
	);
	
	$aScale = array();
	
	$flRange = $flMax - $flMin;
	
	switch ($iMode) {
		
		case SCALE_DISTANCE:
			$aScale['distance']		= $flValue;
			$aScale['top_value']	= 0;
			$aScale['bottom_value']	= 0;
			if ($flMax > 0) {
				$aScale['top_value'] = ceil($flMax / $flValue) * $flValue;
			}
			if ($flMin < 0) {
				$aScale['bottom_value'] = floor($flMin / $flValue) * $flValue;
			}
			$aScale['range']		= $aScale['top_value'] - $aScale['bottom_value'];
			$aScale['quantity']		= round($aScale['range'] / $flValue) + 1;
			$aScale['real_range']	= $flRange;
			break;
			
		case SCALE_QUANTITY:
			$flAssumedDistance = $flRange / ($flValue - 1);
			$flRealDistance = 0;
			foreach ($aDistances as $flDistance) {
				if ($flDistance >= $flAssumedDistance) {
					$flRealDistance = $flDistance;
				}
			}
			$aScale['quantity']		= $flValue;
			$aScale['distance']		= $flRealDistance;
			$aScale['top_value']	= $flRealDistance * ceil($flMax / $flRealDistance);
			$aScale['bottom_value']	= $flRealDistance * floor($flMin / $flRealDistance);
			$aScale['range']		= $aScale['top_value'] - $aScale['bottom_value'];
			if ($aScale['range'] != ($aScale['quantity'] - 1) * $aScale['distance']) {
				$aScale['range'] = ($aScale['quantity'] - 1) * $aScale['distance'];
				$aScale['top_value'] = $aScale['bottom_value'] + $aScale['range'];
			}
			$aScale['real_range']	= $flRange;
			break;
	}
	
	return ($aScale);
}

?>