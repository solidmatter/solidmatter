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
* Checks if an IP fits into a given range resp. set of ranges.
* The IP is expected to be in decimal form, e.g. '127.0.0.1'.
* An IP-range can be a sole IP-address ('192.168.100.33'), a range defined by
* wildcards ('192.168.*.*'), a real range ('192.168.100.20-192.168.100.80') or a
* combination of these, seperated by commas without spaces ('127.0.0.1,192.168.100.*')
* @access public
* @param string the IP that is to be matched
* @param string the IP-range(s)
* @return boolean TRUE, if the IP fits into the range, FALSE otherwise
*/
function match_ipranges($sIPAddress, $sIPRanges) {
	$aIPRanges = explode(',', $sIPRanges);
	reset($aIPRanges);
	while (list( , $sIPRange) = each($aIPRanges)) {
		$sIPRange = trim($sIPRange);
		if (substr_count($sIPRange, '-') != 0) {
			list($sIPRangeStart, $sIPRangeEnd) = explode('-', $sIPRange);
			$iIPAddress 		= str_replace('.', '', $sIPAddress);
			$iIPRangeStart 		= str_replace('.', '', $sIPRangeStart);
			$iIPRangeEnd 		= str_replace('.', '', $sIPRangeEnd);
			if ($iIPAddress < $iIPRangeStart || $iIPAddress > $iIPRangeEnd) {
				return (FALSE);
			}
		} elseif (substr_count($sIPRange, '*') != 0) {
			$aIPAddressComponents 	= explode('.', $sIPAddress);
			$aIPRangeComponents		= explode('.', $sIPRange);
			for ($i=0; $i<count($aIPAddressComponents); $i++) {
				if ($aIPAddressComponents[$i] != $aIPRangeComponents[$i] && $aIPRangeComponents[$i] != '*') {
					return (FALSE);
				}
			}
		} else {
			if ($sIPAddress != $sIPRange) {
				return (FALSE);
			}
		}
	}
	return (TRUE);
}

//------------------------------------------------------------------------------
/**
* Salts a given plain text password with a dynamically generated salt to the form "sha1:<salt>:<saltedpassword>"
* @param string the password
* @param string an additional salt component to be included in the generated salt
* @return string the salted password
*/
function salt_password($sPassword, $sPepper = NULL) {
	
	// prepare salt
	$sSalt = uniqid(mt_rand(), TRUE);
	if ($sPepper != NULL) {
		$sSalt .= $sPepper;
	}
	$sSalt = sha1($sSalt);
	
	// hash salt & pass multiple times
	for ($i=0; $i<10; $i++) {
		$sPassword = sha1($sSalt.$sPassword);
	}
	
	// build storable string
	$sStorablePass = 'sha1:'.$sSalt.':'.$sPassword;
	
	return ($sStorablePass);
	
}

//------------------------------------------------------------------------------
/**
* Checks a given plain text password against a (stored) salted password
* @param string the password
* @param string the stored password
* @return boolean true if passwords match, false otherwise
*/
function check_password($sPassword, $sStoredPassword) {
	
	// fallback if the password was not stored in salted format 
	// TODO: to be removed!
	if (!preg_match('/sha1:.{40}:.{40}/', $sStoredPassword)) {
		if ($sPassword == $sStoredPassword) {
			return (TRUE);
		}
	} else { // new, secure storage used
		list($sAlgorithm, $sSalt, $sStoredPassword) = explode(':', $sStoredPassword);
		
		for ($i=0; $i<10; $i++) {
			$sPassword = sha1($sSalt.$sPassword);
		}
		if ($sPassword == $sStoredPassword) {
			return (TRUE);
		}
	}
	
	return (FALSE);
	
}

//------------------------------------------------------------------------------
/**
* Checks if the given password is a salted password in the form "sha1:<salt>:<saltedpassword>"
* (collisions are possible, but unlikely)
* @param string the password
* @return boolean true if password is already salted, false otherwise
*/
function is_salted_password($sPassword) {
	if (!preg_match('/sha1:.{40}:.{40}/', $sPassword)) {
		return (FALSE);
	}
	return (TRUE);
}

?>