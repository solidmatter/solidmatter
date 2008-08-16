<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

// TODO: implement generic pattern matcher

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function preg_match_masks($aMasks, $aTokens, $sSubject, $aMatches = array()) {
	
	// cycle through directory formats
	$bPatternFound = FALSE;
	foreach ($this->aKnownDirFormats as $sFormat) {
		
		
	}
	
	
}

//------------------------------------------------------------------------------
/**
* TODO: reactivate and implement
* @param 
* @return 
*/
/*function preg_match_mask($sMask, $aTokens, $sSubject, $aPatternMatches = array()) {

	// init
	$bPatternFound = TRUE;
	$aMeanings = array();
	$aLinks = array();
	
	// build pattern
	$sPattern = str_replace($aTokens, $aReplacements, $sFormat);
	$sPattern = '/^'.$sPattern.'$/U';
	
	// build matches sequence
	foreach ($aTokens as $sMeaning => $sPlaceholder) {
		if (!is_numeric($sMeaning) && substr_count($sFormat, $sPlaceholder) > 0) {
			$aMeanings[strpos($sFormat, $sPlaceholder)] = $sMeaning;
		}
	}
	ksort($aMeanings);
	foreach ($aMeanings as $sMeaning) {
		$aLinks[] = $sMeaning;
	}
	$aLinks = array_flip($aLinks);
	$aLinks = array_flip($aLinks);
	
	// match directory and assign
	$aMatches = array();
	if (preg_match($sPattern, $dirAlbum->getName(), $aMatches)) {
		
		$bPatternFound = TRUE;
		if ($this->aVerboseFlags['dirname_format']) {
			echo 'Recognized Format: '.$sFormat.'<br>';
		}
		
		foreach ($aMatches as $iKey => $sMatch) {
			if ($iKey == 0) {
				continue;
			}
			$aPatternMatches[$aLinks[$iKey-1]] = $sMatch;
		}
		break;
		
	}
			
		

}*/

?>