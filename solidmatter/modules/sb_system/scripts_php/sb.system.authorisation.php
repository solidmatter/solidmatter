<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function merge_authorisations($aLocal, $aInherited) {
	
	if (count($aInherited) == 0) {
		return ($aLocal);
	}
	
	//var_dump($aLocal);
	//var_dump($aInherited);
	
	foreach ($aInherited as $iID => $aAuthorisations) {
		foreach ($aAuthorisations as $sAuthorisation => $sGrantType) {
			if (isset($aLocal[$iID][$sAuthorisation]) && $aLocal[$iID][$sAuthorisation] == 'DENY') {
				continue;
			} else {
				$aLocal[$iID][$sAuthorisation] = $sGrantType;
			}
		}
	}
	
	return ($aLocal);
	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function merge_auth_groups(&$aGroup1Auth, $aGroup2Auth) {
	
	$aMergedAuth = array();
	
	if (count($aGroup1Auth) == 0) {
		return ($aGroup2Auth);
	} elseif (count($aGroup2Auth) == 0) {
		return ($aGroup1Auth);
	}
	
	foreach ($aGroup2Auth as $sAuthorisation => $sGrantType) {
		if (!isset($aLocalAuth[$sAuthorisation]) && $aLocalAuth[$sAuthorisation] == 'DENY') {
			continue;
		} else {
			$aLocalAuth[$sAuthorisation] = $sGrantType;
		}
	}
	
	return ($aLocalAuth);

}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function merge_auth_user_group(&$aUserAuth, $aGroupAuth) {
	
	if (count($aGroupAuth) == 0) {
		return ($aUserAuth);
	}
	
	foreach ($aGroupAuth as $sAuthorisation => $sGrantType) {
		if (!isset($aUserAuth[$sAuthorisation]) || $aUserAuth[$sAuthorisation] == 'DENY') {
			continue;
		} else {
			$aUserAuth[$sAuthorisation] = $sGrantType;
		}
	}
	
	return ($aUserAuth);

}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function merge_auth_local_parent(&$aLocalAuth, $aParentAuth) {
	
	if (count($aParentAuth) == 0) {
		return ($aLocalAuth);
	}
	
	foreach ($aParentAuth as $sAuthorisation => $sGrantType) {
		if (isset($aLocalAuth[$sAuthorisation]) && $aLocalAuth[$sAuthorisation] == 'DENY') {
			continue;
		} else {
			$aLocalAuth[$sAuthorisation] = $sGrantType;
		}
	}
	
	return ($aLocalAuth);

}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function merge_auth_local_supported(&$aLocalAuth, $aSupportedAuthorisations, $sCurrentAuth = NULL) {
	
	$aChildAuth = array();
	
	//var_dumpp($aLocalAuth);
	
	//reset($aSupportedAuthorisations);
	foreach ($aSupportedAuthorisations as $sAuth => $sParentAuth) {
		if ($sParentAuth == $sCurrentAuth) {
			$aChildAuth[] = $sAuth;
		}
	}
	//echo $sCurrentAuth.':';
	//var_dumpp($aChildAuth);
	
	foreach ($aChildAuth as $sChildAuth) {
		if (!isset($aLocalAuth[$sChildAuth])) {
			if (isset($aLocalAuth[$sCurrentAuth]) && !isset($aLocalAuth[$sChildAuth])) {
				//echo 'Child: '.$sChildAuth.' Parent: '.$sCurrentAuth.' ('.$aLocalAuth[$sCurrentAuth].')<br>';
				$aLocalAuth[$sChildAuth] = $aLocalAuth[$sCurrentAuth];
			} else {
				//echo 'Child: '.$sChildAuth.' Parent: '.$sCurrentAuth.' (DENY)<br>';
				$aLocalAuth[$sChildAuth] = '';
			}
		}
		merge_auth_local_supported($aLocalAuth, $aSupportedAuthorisations, $sChildAuth);
	}
	
}


?>