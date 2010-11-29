<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* UTF8 expected!
* @param 
* @return 
*/
function str2urlsafe($sString, $bCutUnderscores = TRUE, $bUseLowercase = FALSE) {
	if ($bUseLowercase) {
		$sString = mb_strtolower($sString);
	}
	$sNewString = '';
	for ($i=0; $i<mb_strlen($sString); $i++) {
		$sChar = mb_substr($sString, $i, 1);
		if (preg_match('/^[a-zA-Z0-9_\.]$/', $sChar)) {
			$sNewString .= mb_substr($sString, $i, 1);
		} elseif ($sChar == 'ä') {
			$sNewString .= 'ae';
		} elseif ($sChar == 'ö') {
			$sNewString .= 'oe';
		} elseif ($sChar == 'ü') {
			$sNewString .= 'ue';
		} elseif ($sChar == 'ß') {
			$sNewString .= 'ss';
		} else {
			if (mb_substr($sNewString, -1) != '_') {
				$sNewString .= '_';
			}
		}
	}
	if ($bCutUnderscores && mb_substr($sNewString, mb_strlen($sNewString)-1, 1) == '_') {
		$sNewString = mb_substr($sNewString, 0, mb_strlen($sNewString)-1);
	}
	return ($sNewString);
	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function str2utf8($sString, $sSourceEncoding = NULL) {
	if ($sSourceEncoding != NULL) {
		return (mb_convert_encoding($sString, 'UTF-8', $sSourceEncoding));
	} else {
		return (mb_convert_encoding($sString, 'UTF-8', mb_detect_encoding($sString)));
	}
	
}




?>