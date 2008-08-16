<?php

//------------------------------------------------------------------------------
/**
* 	
*	@package solidBrickz
*	@author	()((() [Oliver M�ller]
*	@version 1.00.01
*/
//------------------------------------------------------------------------------

if (!defined('LONGDATETIME'))		define('LONGDATETIME', 130);
if (!defined('MIDDLEDATETIME'))		define('MIDDLEDATETIME', 135);
if (!defined('SHORTDATETIME'))		define('SHORTDATETIME', 131);
if (!defined('LONGDATE'))			define('LONGDATE', 132);
if (!defined('MIDDLEDATE'))			define('MIDDLEDATE', 136);
if (!defined('SHORTDATE'))			define('SHORTDATE', 133);
if (!defined('TIME'))				define('TIME', 134);



//------------------------------------------------------------------------------
/**
* Works exactly like @see datetime_mysql2local(), except that it accepts a
* string in the MySQL date format. Basically, it extends the given string with
* ' 00:00:00' and passes it on to the mentioned function, so not all supported
* formats are reasonable.
* @param string the MySQL-date value ('YYYY-MM-DD')
* @param integer the result's format, as explained in @see
* datetime_mysql2local()
* @return string the localized result
*/
function date_mysql2local($sMySQLDate, $iFormat) {
	return (datetime_mysql2local($sMySQLDate.' 00:00:00', $iFormat));
}

//------------------------------------------------------------------------------
/**
* Works exactly like @see datetime_mysql2local(), except that it accepts an
* integer value resembling a unix timestamp.
* @param integer the unix timestamp
* @param integer the result's format, as explained in @see
* datetime_mysql2local()
* @return string the localized result
*/
function datetime_timestamp2local($tsDateTime, $iFormat) {
	
	global $LOCALE;
	
	//setlocale(LC_TIME, $LOCALE['FORMAT_LANGUAGEIDS']);
	
	//$tsDateTime = datetime_mysql2timestamp($sMySQLDateTime);
	
	switch ($iFormat) {
		
	    case LONGDATETIME:
			$sFormat = $LOCALE['FORMAT_LONGDATETIME'];
			break;
	    case LONGDATE:
			$sFormat = $LOCALE['FORMAT_LONGDATE'];
			break;
		case MIDDLEDATETIME:
			$sFormat = $LOCALE['FORMAT_MIDDLEDATETIME'];
			break;
		case MIDDLEDATE:
			$sFormat = $LOCALE['FORMAT_MIDDLEDATE'];
			break;
	    case SHORTDATETIME:
			$sFormat = $LOCALE['FORMAT_SHORTDATETIME'];
			break;
	    case SHORTDATE:
			$sFormat = $LOCALE['FORMAT_SHORTDATE'];
			break;
	    case TIME:
			$sFormat = $LOCALE['FORMAT_TIME'];
			break;
	}
	
	$sDateTime = strftime($sFormat, $tsDateTime);
	
	//setlocale(LC_TIME, 'en_US');
	
	return ($sDateTime);

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
	//$fTimezone = (float) get_config('system', 'SITE_TIMEZONE');
	//if (TIME_ZONE!="") $date .= " ".str_replace(":","",TIME_ZONE);
	return ($sRFC822DateTime);
}

//------------------------------------------------------------------------------
/**
* Works exactly like @see datetime_mysql2local(), except that it accepts a
* string in the MySQL date format.
* @param string the MySQL-Datetime value ('YYYY-MM-DD HH:MM:SS')
* @return string the datetime in RFC822 format
*/
function date_mysql2rfc822($sMySQLDate) {
	return (datetime_mysql2rfc822($sMySQLDate.' 00:00:00'));	
}

//------------------------------------------------------------------------------
/**
* Returns a string in MySQL-datetime format for the current moment. 
* @return string the datetime in MySQL format
*/
function datetime_getmysqlnow() {
	return (gmdate('Y-m-d H:i:s', time()));
}

//------------------------------------------------------------------------------
/**
* Returns a string in MySQL-date format for the current moment. 
* @return string the date in MySQL format
*/
function date_getmysqlnow() {
	return (gmdate('Y-m-d', time()));
}

//------------------------------------------------------------------------------
/**
* Returns a string in MySQL-time format for the current moment. 
* @return string the time in MySQL format
*/
function date_getmysqltime($iTime) {
	return (gmdate('Y-m-d', $iTime));
}


?>