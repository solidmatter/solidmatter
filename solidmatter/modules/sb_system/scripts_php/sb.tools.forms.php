<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author ()((() [Oliver Müller]
* 	@version 0.50.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* Checks if a given string is alphanumeric (consists only of letters and 
* numbers). Can also be configured to allow underscores.
* @param string The string to be checked
* @param boolean Flag to allow (TRUE) or forbid underscores  
* @return TRUE if the string is alphanumeric, FALSE otherwise
*/
function is_alphanumeric($sString, $bAllowUnderscore) {
	if ($bAllowUnderscore) {
		$sPattern = '/^[a-z0-9_]+$/i';
	} else {
		$sPattern = '/^[a-z0-9]+$/i';
	}
	return (preg_match($sPattern, $sString));
}

//------------------------------------------------------------------------------
/**
* Checks if a given string can be an email-address.
* Its realness is NOT checked, only the format!
* @param string The String holding the possible email-adress
* @return boolean TRUE if correct, FALSE otherwise
*/
function is_email($sString) { 
	return (preg_match("/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,8}$/i", $sString));
}

//------------------------------------------------------------------------------
/**
* Checks whether a given string can be an URL.
* Its realness is NOT checked, only the format!!
* @param string The String holding the possible URL
* @return boolean TRUE if correct, FALSE otherwise
*/
function is_url($sString) {
	return (eregi("^([a-z]+://)?[A-Z0-9.-]+\.[A-Z]{2,10}/?[A-Z0-9._?~=/-]*$", $sString));
}

//------------------------------------------------------------------------------
/**
* Checks if a given string is a MySQL date. The format as well as the
* correctness is checked.
* @param string The String holding the possible MySQL date
* @return boolean TRUE if correct, FALSE otherwise
*/
function is_mysqldate($sString) {
	return (is_mysqldatetime($sString.' 00:00:00'));
}

//------------------------------------------------------------------------------
/**
* Checks if a given string is a MySQL datetime.
* The format as well as the correctness is checked.
* @param string The String holding the possible MySQL datetime
* @return boolean TRUE if correct, FALSE otherwise
*/
function is_mysqldatetime($sString) {

	if (!eregi("^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-6][0-9]:[0-6][0-9]$", $sString)) {
		return (FALSE);
	}
	
	list($sPart1, $sPart2) = explode(" ", $sString);
    list($iYear, $iMonth, $iDay) = explode("-", $sPart1);
	list($iHour, $iMinute, $iSecond) = explode(":", $sPart2);
	
	if ($iHour < 0 || $iHour > 23) return (FALSE);
	if ($iMinute < 0 || $iMinute > 59) return (FALSE);
	if ($iSecond < 0 || $iSecond > 59) return (FALSE);
	
	if (!checkdate($iMonth, $iDay, $iYear)) return (FALSE);
	
	return (TRUE);
}

//------------------------------------------------------------------------------
/**
*   Checks if the given string is an IP-address (with decimal values).
*   @param string the string to be checked
*   @return boolean TRUE if the string is an IP-address, FALSE otherwise
*/
function is_ipaddress($sString) {

	$aValues = explode('.', $sString);
	
	for ($i=0; $i<4; $i++) {
		if (!isset($aValues[$i]) || !is_numeric($aValues[$i])) {
			return (FALSE);
		} else {
			if ($aValues[$i] < 0 || $aValues[$i] > 254) {
				return (FALSE);
			}
		}
	}
	if (isset($aValues[4])) {
		return (FALSE);
	}
	
	return (TRUE);
}

//------------------------------------------------------------------------------
/**
* Checks if the given string is an IP-range.
* IP-ranges can be in the format 'xxx.xxx.xxx.xxx-yyy.yyy.yyy.yyy' (leading zeros are not required) or
* in the wildcard format where '*' stands for an arbitrary number (e.g. '123.456.*.*').
* @param string the string to be checked
* @return boolean TRUE if the string is a valid IP-range, FALSE otherwise
*/
function is_iprange($sString) {

	if (substr_count($sString, '*') > 0) {
		
		$aValues = explode('.', $sString);
		
		for ($i=0; $i<4; $i++) {
			if (!isset($aValues[$i])) {
				return (FALSE);
			} else {
				if (!is_numeric($aValues[$i])) {
					if ($aValues[$i] != '*') {
						return (FALSE);
					}
				} elseif ($aValues[$i] < 0 || $aValues[$i] > 254) {
					return (FALSE);
				}
			}
		}
		if (isset($aValues[4])) {
			return (FALSE);
		}
		
	} else {
		
		$aValues = explode('-', $sString);
		
		for ($i=0; $i<2; $i++) {
			if (!isset($aValues[$i])) {
				return (FALSE);
			} else {
				if (!is_ipaddress($aValues[$i])) {
					return (FALSE);
				}
			}
		}
		if (isset($aValues[2])) {
			return (FALSE);
		}
	}

	return (TRUE);
}

//------------------------------------------------------------------------------
/**
* Checks  if a given string can be a domain.
* Its realness is NOT checked, only the format!
* @param string The String holding the possible domain
* @return boolean TRUE if correct, FALSE otherwise
*/
function is_domain($sString) {
	return (preg_match("/^[a-z][0-9a-z]*\.([-0-9a-z]+\.)*([a-z]){2,8}$/", $sString));
}

//------------------------------------------------------------------------------
/**
* Returns the number of a float's digits without the decimal point and decimal
* places.
* @param float the float value to be measured
* @return integer the number of digits
*/
function count_integerdigits($flNumber) {
	
	$iCounter = 0;
	if ($flNumber < 0) {
		$iCounter = 1;
	}
	
	$flNumber = abs($flNumber);
	
	while ($flNumber >= 1) {
		$iCounter++;
		$flNumber = $flNumber / 10;
	}
	return ($iCounter);
}

?>