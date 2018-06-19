<?php

//---------------------------------------------------------
/**
*	@package solidBrickz
*	@author	()((() [Oliver Müller]
*	@version 0.00.00
*/
//---------------------------------------------------------

$TEST = 'UUIDSTUFF';

require_once('modules/sb_system/scripts_php/sb.system.essentials.php');
// require_once('modules/sb_system/scripts_php/sb.tools.stopwatch.php');



//---------------------------------------------

if ($TEST == 'ARRAYSPEED') {
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i = 0; $i < 50000; $i++) {
	
		$LOCALE['ERROR_OCCURED']				= 'Fehler aufgetreten!';
		$LOCALE['ERROR_MODULE_NOT_FOUND']		= 'das Modul wurde nicht gefunden!';
		$LOCALE['ERROR_ACTION_NOT_FOUND']		= 'diese Aktion wird von dem Modul nicht unterst�tzt!';
		$LOCALE['ERROR_DONT_PLAY_AROUND']		= 'Bitte nicht mit den Parametern herumspielen!';
		$LOCALE['ERROR_MISSING_VARIABLE']		= 'Eine Variable fehlt!';
		$LOCALE['ERROR_ACTION_IS_SECURED']		= 'Diese Aktion ist nur vom System nutzbar!';
		$LOCALE['ERROR_ACTION_IS_FALSE_TYPE']	= 'Diese Aktion wurde falsch aufgerufen!';
		$LOCALE['ERROR_MALFORMED_SUPERTOKEN']	= 'Im Layout ist ein Supertoken nicht korrekt formatiert!';
	
	}
	$sw->Stop();
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i = 0; $i < 50000; $i++) {
	
		$LOCALE = Array(
			'ERROR_OCCURED'					=> 'Fehler aufgetreten!',
			'ERROR_MODULE_NOT_FOUND'		=> 'das Modul wurde nicht gefunden!',
			'ERROR_ACTION_NOT_FOUND'		=> 'diese Aktion wird von dem Modul nicht unterst�tzt!',
			'ERROR_DONT_PLAY_AROUND'		=> 'Bitte nicht mit den Parametern herumspielen!',
			'ERROR_MISSING_VARIABLE'		=> 'Eine Variable fehlt!',
			'ERROR_ACTION_IS_SECURED'		=> 'Diese Aktion ist nur vom System nutzbar!',
			'ERROR_ACTION_IS_FALSE_TYPE'	=> 'Diese Aktion wurde falsch aufgerufen!',
			'ERROR_MALFORMED_SUPERTOKEN'	=> 'Im Layout ist ein Supertoken nicht korrekt formatiert!'
		);
	}
	$sw->Stop();
}

if ($TEST == 'ARRAY') {

	$sTest = 'test:FLOAT:OPTIONAL:::3';
	
	$aTest = explode(':', $sTest);
	
	print_r($aTest);

}

if ($TEST == 'ENCODE') {
 	
 	$bcCode = eaccelerator_encode('index.php');
 	//$hSave = fopen('index.compiled.php', 'w');
 	file_put_contents('index.compiled.php', $bcCode);
 	exit();
}

if ($TEST == 'MD5') {
 	
 	echo md5('068engel');
 	exit();
}
 	
if ($TEST == 'GMDATE') {
 	
 	echo date('D, d M Y H:i:s', time());
 	echo gmdate('D, d M Y H:i:s', time());
 	echo gmdate('D, d M Y H:i:s', time() + 3600*(date("I")));
 	exit();
}
 
if ($TEST == 'REPLACESPEED') {
	
	
	$iRuns = 1;
	
	$aValues['TEST1'] = 'Hello';
	$aValues['TEST2'] = 'World!';
	
	$sJunk = 'dfjklgfjlljfsdgsdfgjkfghjdfjklfdjgkkjuioertdsfuiohsdfuiouioertuiuos
ddffguiogdsffuziseruizuigdsfuiofdguioseguisgdsfdsfudfuisuoiseruzsdsfertuoerzgusduifoguiseguzs
erthgoudsfisguioseroguisergdsfuiosfduzsghisdfgusguiodsfdsfseguioseriogdfudhvvbhdfvdfbhjhjx
dvuzdfdsfghsdgidsfersuioertioeroziwetdsfdsfiowerthjsfdgkjsdfhkgseidsfuteruitseuigihsdfgjkserk
lghdfgsklddsfdsfgfhsuigtdsfldgsdfgsdsfdfgdfgsdfghdfgjksdkfjafkagwefgukdsfadfkadfkadfkkadsfghk
ergsergsgfusfgihsfdsfdigsdjkgjksdfgjksdfkskgf>>ksdfjdsfgkjsrdsfetuiowedsfgoiusdfuigsuiotgoe
guigtiuoguiodsfsiighuiosetguiseudsfituioserghs785tseo8rdsfgzoseort9serotgz7s34hoduoghu
sdfidfsuiofuiuidsfhjjlkuiuzdsfguzdsfu789545785445%765/dsf876758gbfghdfgdfgdfg';
	
	$sEval = '$sString = $sJunk.$aValues[\'TEST1\'].$sJunk.$aValues[\'TEST2\'].$sJunk;';
	
	$aSearch[] = 'sTEST1s';
	$aSearch[] = 'sTEST2s';
	$aReplace[] = $aValues['TEST1'];
	$aReplace[] = $aValues['TEST2'];
	
	$sSubject = $sJunk.'sTEST1s'.$sJunk.'sTEST2s'.$sJunk;
	
	$sXML = '<?xml version="1.0" encoding="UTF-8"?>
			<lang><TEST1>Hello</TEST1><TEST2>World!</TEST2></lang>';
	$sXSL = '<?xml version="1.0" encoding="UTF-8"?>

		<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" exclude-result-prefixes="html" xmlns:html="http://www.w3.org/1999/xhtml">
		
		<xsl:template match="/">
			'.$sJunk.'
			<xsl:value-of select="/TEST1" />
			'.$sJunk.'
			<xsl:value-of select="/TEST2" />
			'.$sJunk.'
		</xsl:template>

		</xsl:stylesheet>
	';
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	
	// eval
	for ($i=0; $i<$iRuns; $i++) {
		eval($sEval);
	}	
	echo $sString;
	$sw->Stop();
	
	// str_replace
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	
	for ($i=0; $i<$iRuns; $i++) {
		$sString = str_replace($aSearch, $aReplace, $sSubject);
	}
	echo $sString;
	$sw->Stop();
	
	// XSL
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	
	$domXML = new DOMDocument();
	$domXML->loadXML($sXML);
	$domXSL = new DOMDocument();
	$domXSL->loadXML($sXSL);
	$procTest = new XSLTProcessor();
	$procTest->importStyleSheet($domXSL);
	
	for ($i=0; $i<$iRuns; $i++) {
		$sString = $procTest->transformToXML($domXML);
	}
	echo $sString;
	$sw->Stop();
}

if ($TEST == 'MIMETYPES') {
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	
	require_once('_test/mimetypes/inc.php');
	
	if ($hDirectory = opendir('_test/mimetypes')) {
		echo "Directory handle: $hDirectory<br>"."\r\n";
		echo 'Files:<br>'."\r\n";
		echo '<table>';
		
		// This is the correct way to loop over the directory. 
		while (false !== ($sFile = readdir($hDirectory))) {
			if (substr($sFile, 0, 1) != '.') {
				echo "<tr><td>$sFile</td><td>".mime_content_type('_test/mimetypes/'.$sFile).'</td></tr>'."\r\n";
			}
		}
		echo '</table>';
		
		$sw->Stop();
		
		closedir($hDirectory); 
	}
	
}

if ($TEST == 'MIMETYPES2') {
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	

	//require_once('_test/mimetypes/inc.php');
	
	if ($hDirectory = opendir('../solidbrickz/_test/mimetypes')) {
		echo "Directory handle: $hDirectory<br>"."\r\n";
		echo 'Files:<br>'."\r\n";
		echo '<table>';
		$fiTest = finfo_open(FILEINFO_NONE, 'magic.mime');
		// This is the correct way to loop over the directory. 
		while (false !== ($sFile = readdir($hDirectory))) {
			if (substr($sFile, 0, 1) != '.') {
				//echo "<tr><td>$sFile</td><td>".var_dump(finfo_file($fiTest, '../solidbrickz/_test/mimetypes/'.$sFile)).'</td></tr>'."\r\n";
				echo "<tr><td>$sFile</td><td>".var_dump(finfo_file($fiTest, 'test.php')).'</td></tr>'."\r\n";
			}

			
		}
		echo '</table>';
		
		$sw->Stop();
		
		closedir($hDirectory); 
	}
	
}



if ($TEST == 'MIMETYPES3') {
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	

	require_once('modules/sb_system/scripts_php/sb.tools.mime.php');
	
	if ($hDirectory = opendir('../solidbrickz/_test/mimetypes')) {
		echo "Directory handle: $hDirectory<br>"."\r\n";
		echo 'Files:<br>'."\r\n";
		echo '<table>';
		
		// This is the correct way to loop over the directory. 
		while (false !== ($sFile = readdir($hDirectory))) {
			if (substr($sFile, 0, 1) != '.') {
				echo "<tr><td>$sFile</td><td>".get_mimetype('../solidbrickz/_test/mimetypes/'.$sFile, TRUE).'</td></tr>'."\r\n";
				//echo "<tr><td>$sFile</td><td>".get_mimetype('test.php').'</td></tr>'."\r\n";
			}
		}
		echo '</table>';
		
		$sw->Stop();
		
		closedir($hDirectory); 
	}
	
}


if ($TEST == 'DOM') {
	
	$domSubject = new DOMDocument();
	if ($domSubject->load('modules/system/properties.xml')) {
		echo '<pre>'.$domSubject->saveXML().'</pre>';
		var_dump($domSubject);
	}
	
}

if ($TEST == 'XMLSPEED') {
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	
	require_once('locales/ger/forum/title.php');
	require_once('locales/ger/forum/frontend.php');
	require_once('locales/ger/forum/backend.php');
	
	for ($i=0; $i<50; $i++) {
		$sTemp = $LOCALE['FORUM_TITLE_NOAVATAR'];
	}
	
	//print_r($LOCALE);
	
	$sw->Check();
	
	$domLocale = new DOMDocument();
	$domLocale->load('locales/ger/forum/locale.xml');
	
	for ($i=0; $i<50; $i++) {
		$xpathQuery = new DOMXPath($domLocale);
		$nlMatches =  $xpathQuery->query('/sb_forum:locale/profile/avatar/no_avatar');
		$sTemp = (string) $nlMatches->item(0)->textContent;
	}
	
	
	//echo $domLocale->saveXML();
	
	$sw->Check();
	//$domForeign = new DOMDocument();
	//$elemContent = $domForeign->importNode($domSubject->)
		
}
	
	

if ($TEST == 'STRINGSPEED') {
	
	$sString = 'STRING';
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i = 0; $i < 10000; $i++) {
	
		echo 'blablablabla '.$sString.' blablablabla ';
	
	}
	$sw->Stop();
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i = 0; $i < 10000; $i++) {
		
		echo "blablablabla $sString blablablabla ";
		
	}
	$sw->Stop();
	
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i = 0; $i < 10000; $i++) {
		
		echo "blabl$blabla $sString blabl$blabla";
		
	}
	$sw->Stop();
}

 
/*function uuid() {
	//return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		mt_rand( 0, 0x0fff ) | 0x4000,
		mt_rand( 0, 0x3fff ) | 0x8000,
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) 
	);
}*/

function base32uuid() {
	
	return(base_convert(uuid(), 16, 32));	
	
}
 
 
function unfucked_base_convert ($numstring, $frombase, $tobase) {

   $chars = "0123456789abcdefghijklmnopqrstuvwxyz=!'§$%&/()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
   			 
   $tostring = substr($chars, 0, $tobase);

   $length = strlen($numstring);
   $result = '';
   for ($i = 0; $i < $length; $i++) {
       $number[$i] = strpos($chars, $numstring{$i});
   }
   do {
       $divide = 0;
       $newlen = 0;
       for ($i = 0; $i < $length; $i++) {
           $divide = $divide * $frombase + $number[$i];
           if ($divide >= $tobase) {
               $number[$newlen++] = (int)($divide / $tobase);
               $divide = $divide % $tobase;
           } elseif ($newlen > 0) {
               $number[$newlen++] = 0;
           }
       }
       $length = $newlen;
       $result = $tostring{$divide} . $result;
   }
   while ($newlen != 0);
   return $result;
}
 

 
if ($TEST == 'BASECONVERT') {
	echo '<pre>';
	for ($i=0; $i<100; $i++) {
		$sUUID = uuid();
		$sCUUID = unfucked_base_convert($sUUID, 16, 2);
		$in = $sUUID;
		$out = unfucked_base_convert(unfucked_base_convert($sUUID, 16, 32), 32, 16);
		//echo ($sUUID.'|'.$sCUUID.'|'.strlen($sCUUID).'<br>');
		echo ($in.'|'.$out."\r\n");
	}
	echo '</pre>';
}

if ($TEST == 'LOADREPOSSTRUCTURE') {
	echo '<pre>';
	
	echo 'load stuff from DB';
	import('sb.factory.db');
	$_REPOSITORYDEFINITIONS = simplexml_load_file('repositories.xml');
	$DB = DEACIVATEDDBFactory::getInstance('system');
	$DB->setWorkspace('fwk', 'sb');
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i=0; $i<1000; $i++) {
		$sRepository = 'fwk';
		$aRepositoryInfo = array();
		// get nodetypes
		$stmtNodetypes = $DB->prepareKnown('sb_system/repository/getNodetypes');
		$stmtNodetypes->execute();
		$stmtNodetypes = $stmtNodetypes->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtNodetypes as $aRow) {
			$aRepositoryInfo[$aRow['s_type']]['details'] = $aRow;
		}
		// get views
		$aViews = array();
		$stmtViews = $DB->prepareKnown('sb_system/repository/getViews');
		$stmtViews->execute();
		$stmtViews = $stmtViews->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtViews as $aRow) {
			$aRepositoryInfo[$aRow['fk_nodetype']]['views'][$aRow['s_view']]['details'] = $aRow;
		}
		// get actions
		$aActions = array();
		$stmtActions = $DB->prepareKnown('sb_system/repository/getActions');
		$stmtActions->execute();
		$stmtActions = $stmtActions->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtActions as $aRow) {
			$aRepositoryInfo[$aRow['fk_nodetype']]['views'][$aRow['s_view']]['actions'][$aRow['s_action']]['details'] = $aRow;
		}
	}
	$sw->stop();
	//var_dump($aRepositoryInfo);
	
	$sTest = serialize($aRepositoryInfo);
	
	echo 'unserialize from string';
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i=0; $i<1000; $i++) {
		$sxml = unserialize($sTest);
	}
	$sw->stop();
	
	echo 'load XML from file';
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i=0; $i<1000; $i++) {
		$sxml = simplexml_load_file('repository_structure_fwk.xml');
	}
	$sw->stop();
	
	echo 'load XML from string';
	$sXML = file_get_contents('repository_structure_fwk.xml');
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i=0; $i<1000; $i++) {
		$sxml = simplexml_load_string($sXML);
	}
	$sw->stop();
	
	echo 'perform XPath';
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	for ($i=0; $i<1000; $i++) {
		//$aMatch = $sxml->xpath('/repositorystructure/nodetypes/nodetype[s_type="sb_system:page"]/views/view[s_view="list"]/actions/action[s_action="display"]');
		$aMatch = $sxml->xpath('/repository/nodetypes/nodetype[@s_type="sb_system:page"]/views/view');
	}
	$sw->stop();
	//var_dump($aMatch);
	echo '</pre>';
}

if ($TEST == 'IMPORTQUERIES') {
	echo '<h1>Import Queries</h1>';
	echo '<pre>';
	
	echo 'as PHP file directly';
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	include('modules/sb_system/scripts_php/sb.pdo.sysdb.php');
	$sw->stop();
	
	echo 'via import()';
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	import('sb.pdo.sysdb');
	$sw->stop();
	
	echo 'as XML';
	$sw = new Stopwatch();
	$sw->bQuiet = FALSE;
	simplexml_load_file('modules/sb_system/scripts_php/sb.pdo.sysdb.queries.xml');
	$sw->stop();
	
	echo '</pre>';
}

if ($TEST == 'UUIDSTUFF') {

	import('sbUUID');
	
	echo '<body style="font-family: Andale Mono, monospace;">';
	
	for ($i=0; $i<1000; $i++) {

		$sUUID64 = sbUUID::create();
		$sUUID16a = sbUUID::convertBase64to16($sUUID64);
		$sUUID16b = sbUUID::convertBase64to16($sUUID64, FALSE);
		$sUUID64a = sbUUID::convertBase16to64($sUUID16a);
		$sUUID64b = sbUUID::convertBase16to64($sUUID16b, FALSE);
		
		echo $sUUID64.'|'.$sUUID16a.'|'.$sUUID16b.'|'.$sUUID64a.'|'.$sUUID64b;
		
		
		echo '<br>';
		
// 		for ($j=0;$j<100000;$j++) {
			
// 		}
		
	}
	
	echo '</body>';
	
}


//---------------------------------------------


?>