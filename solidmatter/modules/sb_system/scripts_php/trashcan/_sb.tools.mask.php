<?php

//------------------------------------------------------------------------------
/**
* @package solidBrickz
* @author	()((() [Oliver Müller]
* @version 1.00.00
*/
//------------------------------------------------------------------------------

//load_library('functions_binhex');
//load_library('external/utf8entities');
//require_once($PATH_GLOBALSCRIPTS.'/functions_binhex.php');
///require_once($PATH_GLOBALSCRIPTS.'/external/utf8entities.php');

//------------------------------------------------------------------------------

if (!defined('HTML')) 		define('HTML',			1);
if (!defined('BR')) 		define('BR',			2);
if (!defined('ADDSL')) 		define('ADDSL',			4);
if (!defined('STRSL')) 		define('STRSL',			8);
if (!defined('SQL')) 		define('SQL',			16);

if (!defined('EMAIL')) 		define('EMAIL',			32);
if (!defined('URL')) 		define('URL',			64);
if (!defined('CHECKMARK')) 	define('CHECKMARK',		8192);

if (!defined('EMPS')) 		define('EMPS',			128);
if (!defined('SMILIES')) 	define('SMILIES',		256);
if (!defined('URLS')) 		define('URLS',			512);
if (!defined('QUOTES')) 	define('QUOTES',		1024);
if (!defined('SOURCE')) 	define('SOURCE',		2048);
if (!defined('IMAGES')) 	define('IMAGES',		4096);
if (!defined('MONEY')) 		define('MONEY',			16384);
if (!defined('PERCENTAGE'))	define('PERCENTAGE',	32768);
if (!defined('ICONS'))		define('ICONS',			65536);
if (!defined('DATE'))		define('DATE',			131072);
if (!defined('DATETIME'))	define('DATETIME',		262144);
if (!defined('SPOILER'))	define('SPOILER',		524288);

//------------------------------------------------------------------------------
/**
* This function masks strings resp. contents of strings in certain ways.
* How the string is masked can be set via the options, which are combinable via 'oring', e.g. 'HTML|BR'.
* However, not all combinations make sense, since some options are meant for masking the string as a whole,
* not parts of the string, e.g. URL|EMAIL|BR makes absolutely no sense.
* The usually combined options for output(!) are: HTML BR EMPS SMILIES QUOTES SOURCE IMAGES,
* the other options are expected to be used alone.
* The available options are:
* - HTML - masks all HTML entities (e.g. '<' becomes '&lt;')
* - BR - masks all linefeeds for HTML (e.g. '\r\n' becomes '<br />')
* - ADDSL - adds slashes to the string's problematic chars (e.g. '"' becomes '\"')
* - STRSL - strips slashes inserted like above
* - SQL - masks a string for use in queries, adds slashes and encloses the string in single quotes (if i't not numeric). NULL values are returned as string 'NULL'. 
* - EMAIL - builds a 'mailto:'-link with the string as title and address
* - URL - builds a link with the string as title and URL
* - EMPS - masks various tokens enclosed in [{token}] to their HTML representations (e.g. '[BIG]...[/BIG]' becomes '<big>...</big>'). Supported are B, I, BIG and SMALL
* - SMILIES - masks smilie tokens as '[:BIGGRIN:]' or '[:SAD:]' to their images
* - URLS - masks all urls beginning with 'www.' to their resp. links
* - QUOTES - masks all occurences of '[QUOTE]...[/QUOTE]' to tables (with class 'quote') containing the quotes
* - SOURCE - masks all occurences of '[PHP]{sourcecode}[/PHP]' to tables (with class 'code') containing the code
* - IMAGES - masks all occurences of '[IMG]{absolute URL to an image}[/IMG]' to image tags using the URLs
* - CHECKMARK - returns an image-tag (showing a checkmark) or an empty string 
* - MONEY - a number is masked to have two decimals
* - PERCENTAGE - an number is masked to have to decimals and a '%' appended
* @access public
* @param string the string to be masked
* @param integer one or more ('ored') option constants
* @return 
*/
function mask_string($sString, $iOptions, $sBaseURL = '', $sClass = '') {

	global $LOCALE;
	global $CHARSET;
	global $PATH_GLOBALTHEME;

	if ($sClass != '') {
		$sClass = ' class="'.$sClass.'"';
	}
	
	$sDQuotes = '"';
	
	if ($iOptions & HTML) {
		$sString = htmlspecialchars($sString, ENT_COMPAT, $CHARSET);
		$sDQuotes = '&quot;';
	}
	if ($iOptions & BR) {
		$sString = nl2br($sString);
	}
	if ($iOptions & ADDSL) {
		$sString = addslashes($sString);
	} elseif ($iOptions & STRSL) {
		$sString = stripcslashes($sString);
	}
	if ($iOptions & SQL) {
		if (!is_numeric($sString) && $sString == NULL) {
			$sString = 'NULL';
		} else {
			$sString = mysql_escape_string($sString);
			$sString = "'$sString'";
		}
	}
	
	if ($iOptions & EMAIL) {
		if ($sString != '') {
			$sString = '<a href="mailto://'.$sString.'"'.$sClass.'>'.$sString.'</a>';
		}
	}
	if ($iOptions & URL) {
		if ($sString != '') {
			if (!preg_match('/[a-z]+:\/\//', $sString)) {
				$sString = 'http://'.$sString;
			}
			$sString = '<a href="'.$sString.'"'.$sClass.'>'.$sString.'</a>';
		}
	}

	if ($iOptions & EMPS) {
		$aTokens		= Array('[B]','[/B]','[BIG]','[/BIG]','[I]','[/I]','[SMALL]','[/SMALL]', '[EM]', '[/EM]');
		$aReplacements	= Array('<b>','</b>','<big>','</big>','<i>','</i>','<small>','</small>', '<em>', '</em>');
		$sString = str_replace($aTokens, $aReplacements, $sString);
	}
	if ($iOptions & SMILIES) {
		$aTokens = Array(
			'[:ROLLEYES]',
			'[:BIGGRIN]',
			'[:CONFUSED]',
			'[:EEK]',
			'[:YAWN]',
			'[:ANGRY]',
			'[:SORRY]',
			'[:SHOCKED]',
			'[:SMILE]',
			'[:TONGUE]',
			'[:WINK]',
			'[:SAD]',
			'[:RAYBAN]',
			'[:UGLY]'
		);
		$sURLStart	= '<img src="'.$PATH_GLOBALTHEME.'/images_smilies/';
		$sURLEnd	= '" alt="'.mask_string($LOCALE['SYSTEM_TITLE_SMILIE'], HTML).'" align="middle" />';
		$aReplacements = Array(
			$sURLStart.'rolleyes.gif'.$sURLEnd,
			$sURLStart.'biggrin.gif'.$sURLEnd,
			$sURLStart.'confused.gif'.$sURLEnd,
			$sURLStart.'eek.gif'.$sURLEnd,
			$sURLStart.'yawn.gif'.$sURLEnd,
			$sURLStart.'angry.gif'.$sURLEnd,
			$sURLStart.'sorry.gif'.$sURLEnd,
			$sURLStart.'shocked.gif'.$sURLEnd,
			$sURLStart.'smile.gif'.$sURLEnd,
			$sURLStart.'tongue.gif'.$sURLEnd,
			$sURLStart.'wink.gif'.$sURLEnd,
			$sURLStart.'sad.gif'.$sURLEnd,
			$sURLStart.'rayban.gif'.$sURLEnd,
			$sURLStart.'ugly.gif'.$sURLEnd
		);
		$sString = str_replace($aTokens, $aReplacements, $sString);
	}
	if ($iOptions & ICONS) {
		$aTokens = Array(
			'[!IDEA]',
			'[!INFO]',
			'[!QUESTION]',
			'[!EXCLAMATION]',
			'[!ARROWRIGHT]',
			'[!ARROWLEFT]',
			'[!WARNING]',
			'[!LOGO]'
		);
		$sURLStart	= '<img src="'.$PATH_GLOBALTHEME.'/images_icons/';
		$sURLEnd	= '" alt="" align="middle" />';
		$aReplacements = Array(
			$sURLStart.'idea.gif'.$sURLEnd,
			$sURLStart.'info.gif'.$sURLEnd,
			$sURLStart.'question.gif'.$sURLEnd,
			$sURLStart.'exclamation.gif'.$sURLEnd,
			$sURLStart.'arrowright.gif'.$sURLEnd,
			$sURLStart.'arrowleft.gif'.$sURLEnd,
			$sURLStart.'warning.gif'.$sURLEnd,
			$sURLStart.'logo.gif'.$sURLEnd
		);
		$sString = str_replace($aTokens, $aReplacements, $sString);
	}
	if ($iOptions & URLS) {
		$sTarget1 = '<a href="\\1" target="_blank"'.$sClass.'>\\1</a>';
		$sTarget2 = '<a href="\\1" target="_blank"'.$sClass.'>\\2</a>';
		$sString = preg_replace('/\[URL\](.+)\[\/URL\]/Ui', $sTarget1, $sString);
		$sString = preg_replace('/\[URL='.$sDQuotes.'(.+)'.$sDQuotes.'\](.+)\[\/URL\]/Ui', $sTarget2, $sString);
		$sString = preg_replace('/\[LINK\](.+)\[\/LINK\]/Ui', $sTarget1, $sString);
		$sString = preg_replace('/\[LINK='.$sDQuotes.'(.+)'.$sDQuotes.'\](.+)\[\/LINK\]/Ui', $sTarget2, $sString);
	}
	if ($iOptions & QUOTES) {
		if ($sClass == '') {
			$sClass = ' class="quote"';
		}
		//$sString = preg_replace('/\[QUOTE\]/Uis', '<table width="95%" align="center" cellpadding="2" cellspacing="0"'.$sClass.'><tr><th>'.$LOCALE['SYSTEM_TITLE_QUOTE'].':</th></tr><tr><td>', $sString);
		//$sString = preg_replace('/\[QUOTE='.$sDQuotes.'(.+)'.$sDQuotes.'\]/Uis', '<table width="95%" align="center" cellpadding="2" cellspacing="0"'.$sClass.'><tr><th>'.$LOCALE['SYSTEM_TITLE_QUOTEBY'].' \\1:</th></tr><tr><td>', $sString);
		//$sString = preg_replace('/\[\/QUOTE\]/Uis', '</td></tr></table>', $sString);
		$sString = preg_replace('/\[QUOTE\]/Uis', '<div class="quote"><div class="quote_head">'.mask_string($LOCALE['SYSTEM_TITLE_QUOTE'], HTML).':</div><div class="quote_body">', $sString);
		$sString = preg_replace('/\[QUOTE='.$sDQuotes.'(.+)'.$sDQuotes.'\]/Uis', '<div class="quote"><div class="quote_head">'.mask_string($LOCALE['SYSTEM_TITLE_QUOTEBY'], HTML).' \\1:</div><div class="quote_body">', $sString);
		$sString = preg_replace('/\[\/QUOTE\]/Uis', '</div></div>', $sString);
	}
	if ($iOptions & SPOILER) {
		if ($sClass == '') {
			$sClass = ' class="spoiler"';
		}
		//$sString = preg_replace('/\[QUOTE\]/Uis', '<table width="95%" align="center" cellpadding="2" cellspacing="0"'.$sClass.'><tr><th>'.$LOCALE['SYSTEM_TITLE_QUOTE'].':</th></tr><tr><td>', $sString);
		//$sString = preg_replace('/\[QUOTE='.$sDQuotes.'(.+)'.$sDQuotes.'\]/Uis', '<table width="95%" align="center" cellpadding="2" cellspacing="0"'.$sClass.'><tr><th>'.$LOCALE['SYSTEM_TITLE_QUOTEBY'].' \\1:</th></tr><tr><td>', $sString);
		//$sString = preg_replace('/\[\/QUOTE\]/Uis', '</td></tr></table>', $sString);
		$sString = preg_replace('/\[SPOILER\]/Uis', '<div class="quote"><div class="spoiler_head">'.mask_string($LOCALE['FORUM_TITLE_SPOILER'], HTML).':</div><div class="spoiler_body">', $sString);
		//$sString = preg_replace('/\[QUOTE='.$sDQuotes.'(.+)'.$sDQuotes.'\]/Uis', '<div class="quote"><div class="quote_head">'.mask_string($LOCALE['SYSTEM_TITLE_QUOTEBY'], HTML).' \\1:</div><div class="spoiler_body">', $sString);
		$sString = preg_replace('/\[\/SPOILER\]/Uis', '</div></div>', $sString);
	}
	if ($iOptions & SOURCE) {
		$sString = preg_replace_callback('/\[PHP](.+)\[\/PHP]/Uis', 'process_source', $sString);
		//$sString = preg_replace('/\[CODE](.+)\[\/CODE]/Uis', '<div class="code"><div class="code_head">'.mask_string($LOCALE['SYSTEM_TITLE_CODE'], HTML).'</div><div class="code_body"><pre>\\1</pre></div></div>', $sString);
	}
	if ($iOptions & IMAGES) {
		$sString = preg_replace('/\[IMG\](.+)\[\/IMG\]/Ui', '<img src="\\1" alt="'.mask_string($LOCALE['SYSTEM_TITLE_IMAGE'], HTML).'" />', $sString);
	}
	if ($iOptions & CHECKMARK) {
		if ($sString == 'TRUE') {
			$sString = build_checkmark();
		} else {
			$sString = '';
		}
	}
	if ($iOptions & MONEY) {
		$sString = number_format($sString, 2, $LOCALE['FORMAT_DECIMALPOINT'], $LOCALE['FORMAT_DECIMALSEPERATOR']);
	}
	if ($iOptions & PERCENTAGE) {
		$sString = number_format($sString, 2, $LOCALE['FORMAT_DECIMALPOINT'], $LOCALE['FORMAT_DECIMALSEPERATOR']).' %';
	}
	if ($iOptions & DATE) {
		$sString = date_mysql2local($sString, MIDDLEDATE);
		/*
		if (is_mysqldate($sString)) {
			$sString = date_mysql2local($sString, MIDDLEDATE);
		} elseif (is_numeric($sString)) {
			$sString = datetime_timestamp2local($sString, MIDDLEDATE);
		} else {
			$sString = 'no valid date';
		}
		*/
	} elseif ($iOptions & DATETIME) {
		$sString = datetime_mysql2local($sString, MIDDLEDATETIME);
		/*
		if (is_mysqldatetime($sString)) {
			$sString = datetime_mysql2local($sString, MIDDLEDATETIME);
		} elseif (is_numeric($sString)) {
			$sString = datetime_timestamp2local($sString, MIDDLEDATETIME);
		} else {
			$sString = 'no valid datetime';	
		}
		*/
	}
	
	
	return ($sString);
}

function ms($sString, $iOptions, $sBaseURL = '', $sClass = '') {
	return(mask_string($sString, $iOptions, $sBaseURL = '', $sClass = ''));
}

//------------------------------------------------------------------------------
/**
* This is an assisting function to mask_string(), which comes into action when
* the option SOURCE is used. It returns a string where parts that were enclosed
* in [PHP]...[/PHP] are converted to highlighted source
* code, in a table with class 'code'.
* @access public
* @param array an array as sent by preg_replace_callback()
* @return string the hightlighted sourcecode
*/
function process_source($aMatches) {
	
	global $LOCALE;
	
	$aMatches[0] = html_entity_decode($aMatches[0]);
	$aMatches[0] = str_replace("[PHP]", "<?", $aMatches[0]);
	$aMatches[0] = str_replace("[/PHP]", "?>", $aMatches[0]);
	$aMatches[0] = str_replace("<br />", "", $aMatches[0]);
	
	ob_start();
	
	echo '<div class="code"><div class="code_head">'.mask_string($LOCALE['SYSTEM_TITLE_CODE'], HTML).'</div><div class="code_body">';
	highlight_string($aMatches[0]);
	echo '</div></div>';
	$sReplacement = ob_get_contents();
	
	ob_end_clean();
	
	return ($sReplacement);
	
}

//------------------------------------------------------------------------------
/**
* Cuts words in a multibyte string after a given number of characters.
* @todo not working correctly, needs to split at other points than ' '
* @access public
* @param string the string to be cut
* @param integer the maximum size a word can have, longer words are also split
* @return string the cut string
*/
function mb_wordcut($sString, $iMaxwordlength = 20) {
	
	$aTemp = explode(' ', $sString);
	
	while (list($sKey, $sValue) = each($aTemp)) {
		if (mb_strlen($sValue) > $iMaxwordlength) {
			$aTemp[$sKey] = mb_strimwidth($sValue, 0, $iMaxwordlength-3, '...');
		}
	}
	return (implode(' ', $aTemp));
	
}


?>