<?php

//-------------------------------------------------------------------
/**
* 	
*	@package solidBrickz
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//-------------------------------------------------------------------

//-------------------------------------------------------------------
/**
* NOTE: this is not yet functional
* @todo remove overloaded functions from the system
* @access public
* @param string the string to be converted to binary numbers
* @return string the output
*/
function string2binary($sString) {
	$sOutput = '';
	for ($i=0; $i<strlen($sString); $i++) {
		$sOutput .= char2binary($sString[$i]).' ';
	}
	return ($sOutput);
}

//-------------------------------------------------------------------
/**
* NOTE: this is not yet functional
* @todo remove overloaded functions from the system
* @access public
* @param string the character to be converted to a binary number
* @return string the output
*/
function char2binary($aChar) {
	$iByte = ord($aChar);
	$sOutput = decbin($iByte);
	$sPrefix = '';
	for ($i=0; $i<8-strlen($sOutput); $i++) {
		$sPrefix .= '0';
	}
	$sOutput = $sPrefix.$sOutput;
	return ($sOutput);
}




?>