<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

/*
function pretty_print($sXML) {
	$doc = new DOMDocument('1.0', 'utf-8');
	
	$doc->LoadHTML($sXML);
	
	return ($doc->SaveXML());
}


//------------------------------------------------------------------------------
/**
* 
* @access public
* @param 
* @return
*/
/*
function pretty_print($sXML) {
	//$sXML = preg_replace("/\>[\r\n\s]*\</",">\n<", $sXML);
	
	//$a = array();
	
	//preg_match_all("|<([a-z_]+).*>([a].+)</\\1>|s", $sXML, $a);
	//print_r($a);
	
	$aSearch = array(
		"|\r|",
		"|<!--|",
		"|-->|"
	);
	$aReplace = array(
		"",
		"\n<!--\n",
		"\n-->\n"
	);
	$sXML = preg_replace($aSearch, $aReplace, $sXML);
	
	return preg_replace_callback("|(<([a-z0-9_]+)[^>]*>)(.+)(</\\2>)|s", 'pretty_print_assist', $sXML);
	
}

function pretty_print_assist($aMatches) {
	
	
	//print_r($aMatches); exit();
	static $iDepth = -1;
	static $it = 0;
	
	$it++;
	//if ($it > 30) { return (FALSE); }
	
	if ($iDepth < 0) {
		$iEffectiveDepth = 0;
	} else {
		$iEffectiveDepth = $iDepth;
	}
	
	//echo str_repeat("\t", $iEffectiveDepth).$aMatches[1]."\r\n";
	//echo str_repeat("\t", $iDepth).$aMatches[2].'-'.$iDepth.'|'."\r\n";
	//echo $aMatches[2].'-'.$iDepth.'|'."\r\n\r\n".$sXML."\r\n\r\n";
	//$sStartTag = preg_match("|<([a-z_]+).*>|")
	
	//echo $sStartTag."\r\n".$sEndTag."\r\n";
	
	
	
	$sStartTag	= $aMatches[1];
	$sEndTag	= $aMatches[4];
	$sSpacer	= str_repeat("\t", $iEffectiveDepth);
	$sNL		= "\r\n";
	
	$sInner = $aMatches[3];
	$sInner = trim($sInner);
	$sInner = preg_replace("|[\s\n]*\n[\s\n]*|", "\n", $sInner);
	//$sInner = preg_replace("|\n+|", "\n", $sInner);
	
	$sXML  = $sSpacer.$sStartTag.'<!--'.$aMatches[2].'-->'.$sNL;
	$sXML .= preg_replace_callback("|(<([a-z0-9_]+)[^>]*>)(.+)(</\\2>)|s", 'pretty_print_assist', $sInner).$sNL;
	$sXML .= $sSpacer.$sEndTag.'<!--'.$aMatches[2].'-->'.$sNL;
	/*
	if (mb_strlen($sInner) == 0) {
		$sXML = $sSpacer.$sStartTag.$sEndTag.$sNL;
	} elseif (mb_strlen($sInner) < 50 && !preg_match("|<|", $sInner)) {
		$sXML = $sSpacer.$sStartTag.$sInner.$sEndTag.$sNL;
	} elseif (preg_match("|<([a-z0-9_]+)[^>]*>.+</\\1>|s", $sInner)) {
		
		$sXML  = $sSpacer.$sStartTag.$sNL;
		$iDepth++;
		$sXML .= preg_replace_callback("|(<([a-z0-9_]+)[^>]*>)(.+)(</\\2>)|s", 'pretty_print_assist', $sInner).$sNL;
		$iDepth--;
		$sXML .= $sSpacer.$sEndTag.$sNL;
		
	} else {
		//$sXML = $sSpacer.$sStartTag.$sInner.$sEndTag.$sNL;
		
		$sXML  = $sSpacer.$sStartTag.$sNL;
		$aLines = explode("\n", $sInner);
		foreach ($aLines as $sLine) {
			$sXML .= $sSpacer."\t".$sLine.$sNL;
		}
		$sXML .= $sSpacer.$sEndTag.$sNL;
		
	}*/
	
	
	
	//echo str_repeat("\t", $iEffectiveDepth).$aMatches[4]."\r\n";
	/*
	return ($sXML);
	
}
*/

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function pretty_print($sXML) {
	
	$sPrettyXML = '';
	
	$sXML = preg_replace("/>[\n\s]*</",">\n<", $sXML); 
	$aXMLLines = explode("\n", $sXML);
	$iDepth = -1;
	
	foreach($aXMLLines as $sLine) {
		
		$sLine = trim($sLine);
		
		if (preg_match("|<[^/{1}]|", $sLine)) {
			$iDepth++;
		}
		if ($iDepth < 0) {
			$iEffectiveDepth = 0;
		} else {
			$iEffectiveDepth = $iDepth;
		}
		$sPrettyXML .= str_repeat("\t", $iEffectiveDepth);
		//if(preg_match("|</|", $sLine) || preg_match("|/>|", $sLine) || preg_match("|-->|" , $sLine)) { 
		if(preg_match("|</|", $sLine) || preg_match("|.*/>|", $sLine) || preg_match("|.*-->|" , $sLine) || preg_match("|.*\?>|", $sLine)) { 
			$iDepth--;
		}
		$sPrettyXML .= $sLine."\r\n";
	}
	
	return ($sPrettyXML);

}

if (!defined('XML'))		define('XML',	140);
if (!defined('XHTML'))		define('XHTML',	141);

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function pretty_printt($sXML, $iType = XML, $bRemoveComments = FALSE) {
	
	$sPrettyXML = '';
	$bIgnore = FALSE;
	$bDontTouch = FALSE;
	
	if ($bRemoveComments) {
		$sXML = preg_replace("|<!--.*-->|Us", "", $sXML);
	}
	
	if ($iType == XML) {
		$aSearch = array(
			"|>[\n\s]*<|",
			"|<!--|",
			"|-->|",
			"|\s*>|"
		);
		$aReplace = array(
			">\n<",
			"\n<!--\n",
			"\n-->\n",
			">"
		);
	} else {
		$aSearch = array(
			//"|<input(.*)/>|Us",
			"|([^/])>[\n\s]*|",
			"|[\n\s]*<|",
			"|<!--|",
			"|-->|",
			"|\s*>|"
		);
		$aReplace = array(
			//"<input\\1/>",
			"\\1>\n",
			"\n<",
			"\n<!--\n",
			"\n-->\n",
			">"
		);
	}
	$sXML = preg_replace($aSearch, $aReplace, $sXML);
	$aXMLLines = explode("\n", $sXML);
	$iDepth = -1;
	$bFirstLine = TRUE;
	
	foreach($aXMLLines as $sLine) {
		
		$sLine = trim($sLine);
		
		if (mb_strlen($sLine) == 0) {
			continue;
		}
		
		if (preg_match('|<!--|', $sLine)) {
			$bIgnore = TRUE;
		}
		
		if (preg_match('|<input.*value="|', $sLine)) {
			$bDontTouch = TRUE;
		}
		
		if (!$bIgnore 
			&& preg_match("|^</.+>$|", $sLine)
			|| preg_match("|-->|", $sLine)
			) {
			$iDepth--;
		}
		
		if (preg_match('|-->|', $sLine)) {
			$bIgnore = FALSE;
		}
		
		if ($iDepth < 0) {
			$iEffectiveDepth = 0;
		} else {
			$iEffectiveDepth = $iDepth;
		}
		
		if (!$bFirstLine && !$bDontTouch) {
			$sPrettyXML .= "\r\n";
			$sPrettyXML .= str_repeat("\t", $iEffectiveDepth);
		}
		
		$sPrettyXML .= $sLine;
		
		if ($bDontTouch && preg_match('|".*/>|', $sLine)) {
			$bDontTouch = FALSE;
		}
		
		if (!$bIgnore
			&& preg_match("|^<[a-z]+.*>|", $sLine)
			&& !preg_match("|^<[a-z]+.*/>$|", $sLine)
			&& !preg_match("|^<.+>.*</.+>$|", $sLine)
			|| preg_match("|^<!--$|", $sLine)
			) {
			
			$iDepth++;
		}
		
		$bFirstLine = FALSE;
		
	}
	
	return ($sPrettyXML);

}


?>