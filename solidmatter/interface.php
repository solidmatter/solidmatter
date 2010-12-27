<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 0.50.00
*/
//------------------------------------------------------------------------------

//echo (file_get_contents('php://input'));

//------------------------------------------------------------------------------
// config

if (!defined('PRETTYPRINT'))		define('PRETTYPRINT', TRUE);

//------------------------------------------------------------------------------
// create stopwatch

require_once('modules/sb_system/scripts_php/sb.tools.stopwatch.advanced.php');
Stopwatch::start();

//------------------------------------------------------------------------------
// init

require_once('modules/sb_system/scripts_php/sb.system.essentials.php');

DEBUG('Interface: Request URI = '.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], DEBUG::REQUEST);
DEBUG('Client: User Agent = '.$_SERVER['HTTP_USER_AGENT'], DEBUG::CLIENT);

session_start();
$_SESSIONID = session_id();
// TODO: session id characteristics vary from one PHP-version to another, but this is a crappy workaround anyways -> change to separate handler checking a token
if (preg_match('/sid=([a-z0-9]+)/', $_SERVER['REQUEST_URI'], $aMatches)) {
	$_SESSIONID = $aMatches[1];
	DEBUG('Interface: URL Session ID = '.$_SESSIONID, DEBUG::SESSION);
}
session_write_close();

DEBUG('Interface: Session ID = '.$_SESSIONID, DEBUG::SESSION);

//------------------------------------------------------------------------------
// switch according to site definition

$aRequest = parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$aPath = explode('/', $aRequest['path']);
if (in_array('..', $aPath)) {
	die('parent operator not allowed, aborting.');
}
//var_dumpp($aRequest);
$sRequestLocation = $aRequest['host'].$aRequest['path'];
$sxmlSites = simplexml_load_file('_config/interface.xml');
$aSite = match_site($sxmlSites, $sRequestLocation);

$elemSite = $aSite['site'];
$elemController = $aSite['controller'];

DEBUG('Interface: Site matched ['.(string) $elemSite['location'].'] with handler ['.(string) $elemSite['handler'].']', DEBUG::BASIC);

if ($elemSite == NULL) {
	die('could not handle request, aborting.');
}

Stopwatch::check('tier1_sitematch', 'php');

switch ((string) $elemSite['type']) {
	
	case 'controller':
		
		// load libraries
		import('sb.system.errors');
		import('sb.tools.datetime');
		import('sb.dom.request');
		// optional due to autoload
		import('sb.factory.response');
		import('sb.dom.response');
		
		Stopwatch::check('tier1_load', 'load');
		
		// TODO: transport repository stuff to tier2
		define('DEBUG',					constant((string) $elemController->debug['enabled']));
		
		$_REQUEST = new sbDOMRequest();
		$_REQUEST->setLocation((string) $elemSite['location']);
		$_REQUEST->setHandler((string) $elemSite['handler']);
		if ($elemSite['subject'] == NULL) {
			$_REQUEST->setSubject((string) $elemController['subject']);
		} else {
			$_REQUEST->setSubject((string) $elemSite['subject']);
		}
		$_REQUEST->setRepository(
			(string) $elemController->storage['repository'],
			(string) $elemController->storage['workspace'],
			(string) $elemController->storage['user'],
			(string) $elemController->storage['pass']
		);
		
		// prepare communication to tier2 if necessary
		if (!isset($elemController->tier2) || $elemController->tier2['separated'] == 'false') {
			define('TIER2_SEPARATED', FALSE);
		} else {
			define('TIER2_SEPARATED', TRUE);
			define('TIER2_HOST',			(string) $elemController->tier2['host']);
			define('TIER2_PORT',			(string) $elemController->tier2['port']);
			define('TIER2_PATH',			(string) $elemController->tier2['path']);
		}
		
		break;
		
	case 'theme':
		
		// determine requested file
		if ($aPath[2] == 'global') { // theme-independent global files
			$sFile = 'interface/themes/_global/'.implode('/', array_slice($aPath, 3));
		} else { // files in current theme
			$sHandler = (string) $elemSite['handler'];
			$sTheme = (string) $elemSite['theme'];
			if ($sTheme == '') {
				switch ($sHandler) {
					case 'backend':
						$sTheme = '_admin';
						break;
					case 'application':
						$sTheme = '_default';
						break;
				}
			}
			$sThemeOverride = $sTheme;
			if (isset($elemSite['theme_override'])) {
				$sThemeOverride = (string) $elemSite['theme_override'];
			}
			$sFile = 'interface/themes/'.$sTheme.'/'.$aPath[2].'/'.implode('/', array_slice($aPath, 3));
			$sFileOverride = 'interface/themes/'.$sThemeOverride.'/'.$aPath[2].'/'.implode('/', array_slice($aPath, 3));
		}
		
		// deliver file
		headers('cache');
		import('sb.tools.mime');
		if (file_exists($sFileOverride)) {
			$sCurrentFile = $sFileOverride;
		} elseif (file_exists($sFile)){
			$sCurrentFile = $sFile;
		} else {
			header('HTTP/1.0 404 Not Found');
			$sMimetype = get_mimetype_by_extension($sFile);
			if (TRUE || $sMimetype != 'text/css') {
				die('404 - File not found: '.$sFile.'');
			}
		}
		
		$sMimetype = get_mimetype_by_extension($sCurrentFile);
		header('Content-type: '.$sMimetype);
		$hFile = fopen($sCurrentFile, 'r');
		fpassthru($hFile);
		
		exit;
		
	case 'script':
		include((string) $elemSite['handler']);
		exit;
		
	case 'passthrough':
		$sMimetype = (string) $elemSite['mimetype'];
		if ($sMimetype != '') {
			header('Content-type: '.$sMimetype);
		}
		$hFile = fopen((string) $elemSite['file'], 'r');
		fpassthru($hFile);
		exit;
	
	case 'rewrite':
		$sFile = str_replace($elemSite['location'], $elemSite['destination'], $sRequestLocation);
		//var_dump($sFile);
		import('sb.tools.mime');
		$sMimetype = get_mimetype_by_extension($sFile);
		header('Content-type: '.$sMimetype);
		$hFile = fopen((string) $sFile, 'r');
		fpassthru($hFile);
		exit;
		
	case 'redirect':
		header('Location: '.(string) $elemSite['destination'], TRUE, '303');
		exit;
	
	default:
		header('File not found', TRUE, '404');
		die ('unknown site type, aborting.');
	
}

Stopwatch::check('tier1_siteswitch', 'php');

//------------------------------------------------------------------------------
// rearrange/prepare files array

if (count($_FILES) > 0) {
	import('sb.tools.mime');
	foreach ($_FILES as $sFieldname => $aField) {
		if (is_array($aField['name'])) {
			for ($i=0; $i<count($aField['name']); $i++) {
				$aNewFiles[$sFieldname][$i]['name'] = $_FILES[$sFieldname]['name'][$i];
				$aNewFiles[$sFieldname][$i]['size'] = $_FILES[$sFieldname]['size'][$i];
				$aNewFiles[$sFieldname][$i]['type'] = get_mimetype($_FILES[$sFieldname]['tmp_name'][$i], TRUE, FALSE, $_FILES[$sFieldname]['name'][$i]);
				$aNewFiles[$sFieldname][$i]['error'] = $_FILES[$sFieldname]['error'][$i];
				$aNewFiles[$sFieldname][$i]['tmp_name'] = $_FILES[$sFieldname]['tmp_name'][$i];
			}
		} else {
			$aNewFiles[$sFieldname]['name'] = $_FILES[$sFieldname]['name'];
			$aNewFiles[$sFieldname]['size'] = $_FILES[$sFieldname]['size'];
			$aNewFiles[$sFieldname]['type'] = get_mimetype($_FILES[$sFieldname]['tmp_name'], TRUE, FALSE, $_FILES[$sFieldname]['name'][$i]);
			$aNewFiles[$sFieldname]['error'] = $_FILES[$sFieldname]['error'];
			$aNewFiles[$sFieldname]['tmp_name'] = $_FILES[$sFieldname]['tmp_name'];
		}
	}
	$_FILES = $aNewFiles;
	Stopwatch::check('tier1_rearrangefiles', 'php');
}

//------------------------------------------------------------------------------
// process request

try {

// prepare and enter tier2
if (TIER2_SEPARATED) {
	$_REQUEST->includeRequest($_SESSIONID, TRUE);
	DEBUG('Interface: entering tier2 now (via http)', DEBUG::BASIC);
	$_RESPONSE = $_REQUEST->send(TIER2_PATH, TIER2_HOST, TIER2_PORT);
} else {
	$_REQUEST->includeRequest($_SESSIONID);
	if (isset($_GET['dumprequest'])) {
		header('Content-Type: application/xml');
		echo $_REQUEST->saveXML();
		exit();
	}
	DEBUG('Interface: entering tier2 now (via include())', DEBUG::BASIC);
	include_once('controller.php');
}

DEBUG('Interface: request processing took '.(Stopwatch::stop('execution_time')*1000).'ms', DEBUG::BASIC);
$_RESPONSE->addStopwatchTimes(Stopwatch::getTaskTimes());
if ($_REQUEST->getParam('debug') != TRUE) {
	$_RESPONSE->importLocales();
}

// output
DEBUG('Interface: processing output now', DEBUG::BASIC);
if (isset($elemSite['theme'])) {
	$_RESPONSE->setTheme((string) $elemSite['theme']);
}
$_RESPONSE->saveOutput();

//------------------------------------------------------------------------------
// exception handling

} catch (Exception $e) {
	$_RESPONSE = ResponseFactory::getInstance('global');
	$_RESPONSE->addException($e);
	$_RESPONSE->finalizeMetadata();
	// TODO: support sensible error codes, this is a workaround because default code is 0, which my be considered as 'success'
	$iErrorcode = $e->getCode();
	if ($iErrorcode == 0) {
		$iErrorcode = 1;	
	}
	$_RESPONSE->addHeader('X-sbException: '.$iErrorcode);
	$_RESPONSE->addHeader('X-sbException-Type: '.get_class($e));
	// FIXME: this has no effect
	$sMethod = 'rendered';
	if (DEBUG) {
		if ($_REQUEST->getParam('debug') == TRUE) {
			$sMethod = 'debug';
		} elseif ($_REQUEST->getParam('xml') == TRUE) {
			$sMethod = 'xml';
		}
	}
	$_RESPONSE->saveOutput($sMethod);
}

//------------------------------------------------------------------------------
// utility functions

function match_site($elemCurrentRoot, $sRequestLocation) {
	
	$aSiteDefinition = array();
	$elemCurrentSite = NULL;
	$elemCurrentSubSite = NULL;
	$sCurrentSite = '';
	$sCurrentSubSite = '';
	
	// match site root
	foreach ($elemCurrentRoot->site as $elemSite) {
		$sSiteLocation = (string) $elemSite['location'];
		if (substr_count($sRequestLocation, $sSiteLocation) > 0) {
			if (strlen($sSiteLocation) >= strlen($sCurrentSite)) {
				$sCurrentSite = $sSiteLocation;
				$elemCurrentSite = $elemSite;
			}
		}
	}
	
	// check if site has subsites and match these
	foreach ($elemCurrentSite->site as $elemSite) {
		$sSiteLocation = (string) $elemSite['location'];
		if (substr_count($sRequestLocation, $sSiteLocation) > 0) {
			if (strlen($sSiteLocation) >= strlen($sCurrentSite)) {
				$sCurrentSite = $sSiteLocation;
				$elemCurrentSubSite = $elemSite;
			}
		}
	}
	
	if ($elemCurrentSubSite !== NULL) {
		$aSiteDefinition['site'] = $elemCurrentSubSite;
	} else {
		$aSiteDefinition['site'] = $elemCurrentSite;
	}
	$aSiteDefinition['controller'] = $elemCurrentSite;
	
	return ($aSiteDefinition);
	
}

?>