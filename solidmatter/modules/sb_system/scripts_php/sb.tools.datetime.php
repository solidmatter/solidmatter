<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

// TODO: place timezone where it's correct!!!!!!
date_default_timezone_set('europe/berlin');
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');

//------------------------------------------------------------------------------
/**
* 
* @param 
* @param 
* @param 
* @return 
*/
function datetime_convert($sInputString, $sFormatIn, $sFormatOut) {
	
	$dtSubject = DateTime::createFromFormat($sFormatIn, $sInputString);
	return ($dtSubject->format($sFormatOut));

}

//------------------------------------------------------------------------------
/**
* Converts a MySQL-datetime value to a local format, dependent on the current locale.
* There are always 7 formats defined: LONGDATETIME, MIDDLEDATETIME,
* SHORTDATETIME, LONGDATE, MIDDLEDATE, SHORTDATE and TIME. E.g., for german
* LONGDATETIME is the format 'Dienstag, 03. Mai 2004, 13:53:12', SHORTDATE would
* be '03.05.04'.
* @param string the MySQL-datetime value ('YYYY-MM-DD HH:MM:SS')
* @param integer the result's format, as explained above
* @return string the localized result
*/
function datetime_mysql2local($sMySQLDateTime, $sFormat='%a, %d. %B %Y, %H:%M') {
	
	$tsDateTime = datetime_mysql2timestamp($sMySQLDateTime);
	
	$sDateTime = strftime($sFormat, $tsDateTime);
	$sDateTime = iconv('ISO-8859-1', 'UTF-8', $sDateTime);
	
	return ($sDateTime);

}

//------------------------------------------------------------------------------
/**
* Converts a MySQL-Datetime value to an associative array.
* The indexes are 'year', 'month', 'day', 'hour', 'minute', 'second'
* @param string the MySQL-Datetime value ('YYYY-MM-DD HH:MM:SS')
* @return array associative array containing the extracted values
*/
function &datetime_mysql2array($sMySQLDateTime) {

	$aDateTime = array();
	
	list($sPart1, $sPart2) = explode(' ', $sMySQLDateTime);
	list($aDateTime['year'], $aDateTime['month'], $aDateTime['day']) = explode('-', $sPart1);
	list($aDateTime['hour'], $aDateTime['minute'], $aDateTime['second']) = explode(':', $sPart2);
	
	return ($aDateTime);
}

//------------------------------------------------------------------------------
/**
* Converts a MySQL-Datetime value to a Unix timestamp.
* @param string the MySQL-Datetime value ('YYYY-MM-DD HH:MM:SS')
* @return integer the datetime in seconds passed since 1.1.1970, 00:00:00
*/
function datetime_mysql2timestamp($sMySQLDateTime) {
	
	if ($sMySQLDateTime == '') {
		return (0);
	}
	
	list($sPart1, $sPart2) = explode(' ', $sMySQLDateTime);
	list($sYear, $sMonth, $sDay) = explode('-', $sPart1);
	list($sHour, $sMinute, $sSecond) = explode(':', $sPart2);
	
	return (mktime($sHour, $sMinute, $sSecond, $sMonth, $sDay, $sYear));
}

//------------------------------------------------------------------------------
/**
* Converts a Unix timestamp value to a MySQL-Datetime.
* @param integer the datetime in seconds passed since 1.1.1970, 00:00:00 (now is assumed if not given)
* @return string the MySQL-Datetime value ('YYYY-MM-DD HH:MM:SS')
*/
function datetime_timestamp2mysql($iTimestamp = NULL) {
	
	if ($iTimestamp == NULL) {
		$iTimestamp = time();
	}
	
	return (date('Y-m-d H:i:s', $iTimestamp));
}

//------------------------------------------------------------------------------
/**
* Works exactly like @see datetime_mysql2timestamp(), except that it accepts a
* string in the MySQL date format.
* @param string the MySQL-date value ('YYYY-MM-DD')
* @return integer the unix timestamp
*/
function date_mysql2timestamp($sMySQLDate) {
	return (datetime_mysql2timestamp($sMySQLDate.' 00:00:00'));	
}

//------------------------------------------------------------------------------
/**
* Converts a MySQL-datetime value to a datetime value according to RFC822.
* This format is use in various situations, e.g. eMails and RSS-feeds.
* @param string the MySQL-Datetime value ('YYYY-MM-DD HH:MM:SS')
* @return string the datetime in RFC822 format
*/
function datetime_mysql2rfc822($sMySQLDateTime) {
	$iTime = datetime_mysql2timestamp($sMySQLDateTime);
	$sRFC822DateTime = gmdate('D, d M Y H:i:s \G\M\T', $iTime+3600*date("I"));
	return ($sRFC822DateTime);
}

?>