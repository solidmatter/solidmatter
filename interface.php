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

if (!defined('CHARSET'))			define('CHARSET', 'UTF-8');	
if (!defined('PRETTYPRINT'))		define('PRETTYPRINT', TRUE);

//------------------------------------------------------------------------------
// create stopwatch

require_once('modules/sb_system/scripts_php/sb.tools.stopwatch.advanced.php');
$_STOPWATCH = new AdvancedStopwatch();

//------------------------------------------------------------------------------
// init

require_once('modules/sb_system/scripts_php/sb.system.essentials.php');

DEBUG('Interface: Request URI', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], DEBUG::REQUEST);
DEBUG('Client: User Agent', $_SERVER['HTTP_USER_AGENT'], DEBUG::CLIENT);

session_start();
$_SESSIONID = session_id();
if (preg_match('/sid=([a-f0-9]{32})/', $_SERVER['REQUEST_URI'], $aMatches)) {
	$_SESSIONID = $aMatches[1];
	DEBUG('Interface: URL Session ID', $_SESSIONID, DEBUG::SESSIONID);
}
session_write_close();

DEBUG('Interface: Session ID', $_SESSIONID, DEBUG::SESSIONID);

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

DEBUG('Interface: Site matched', (string) $elemSite['location'].' | '.(string) $elemSite['handler'], DEBUG::BASIC);

if ($elemSite == NULL) {
	die('could not handle request, aborting.');
}

$_STOPWATCH->check('tier1_sitematch', 'php');

switch ((string) $elemSite['type']) {
	
	case 'controller':
		
		// load libraries
		import('sb.system.errors');
		import('sb.tools.datetime');
		import('sb.dom.request');
		// optional due to autoload
		import('sb.factory.response');
		import('sb.dom.response');
		
		$_STOPWATCH->check('tier1_load', 'load');
		
		// TODO: transport repository stuff to tier2
		define('DEBUG',					constant((string) $elemController->debug['enabled']));
		
		$_REQUEST = new sbDOMRequest();
		$_REQUEST->setLocation((string) $elemSite['location']);
		$_REQUEST->setHandler((string) $elemSite['handler']);
		$_REQUEST->setSubject((string) $elemSite['subject']);
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
		$sHandler = (string) $elemSite['handler'];
		$sTheme = (string) $elemSite['theme'];
		$sMimetype = '';
		
		switch ($sHandler) {
			
			case 'backend':
				switch ($aPath[3]) {
					case 'css':
						$aPath[3] = 'css';
						break;
					case 'images':
						$aPath[3] = 'images';
						break;
					case 'icons':
						$aPath[3] = 'icons';
						break;
					case 'js':
					//case 'scripts_js':
						$aPath[3] = 'js';
						break;
					default:
						die('theme component does not exist, aborting.');
				}
				if ($sTheme == '') {
					$sTheme = '_admin';
				}
				$sFile = 'interface/themes/'.$sTheme.'/'.$aPath[2].'/'.implode('/', array_slice($aPath, 3));
				break;
				
			case 'application':
				if ($sTheme == '') {
					$sTheme = '_default';
				}
				$sFile = 'interface/themes/'.$sTheme.'/'.$aPath[2].'/'.implode('/', array_slice($aPath, 3));
				break;
				
		}
		
		headers('cache');
		
		// deliver file
		if (file_exists($sFile)) {
			if ($sMimetype == '') {
				import('sb.tools.mime');
				$sMimetype = get_mimetype_by_extension($sFile);
				header('Content-type: '.$sMimetype);
			}
			$hFile = fopen($sFile, 'r');
			fpassthru($hFile);
		} else {
			header('HTTP/1.0 404 Not Found');
			if ($sMimetype == '') {
				import('sb.tools.mime');
				$sMimetype = get_mimetype_by_extension($sFile);
			}
			if (TRUE || $sMimetype != 'text/css') {
				die('404 - File not found: '.$sFile.'');
			}
		}
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
		
	case 'redirect':
		header('Location: '.(string) $elemSite['destination'], TRUE, '303');
		exit;
	
	default:
		header('File not found', TRUE, '404');
		die ('unknown site type, aborting.');
	
}

$_STOPWATCH->check('tier1_siteswitch', 'php');

//------------------------------------------------------------------------------
// rearrange/prepare files array

if (count($_FILES) > 0) {
	import('sb.tools.mime');
	foreach ($_FILES as $sFieldname => $aField) {
		for ($i=0; $i<count($aField['name']); $i++) {
			$aNewFiles[$sFieldname][$i]['name'] = $_FILES[$sFieldname]['name'][$i];
			$aNewFiles[$sFieldname][$i]['size'] = $_FILES[$sFieldname]['size'][$i];
			$aNewFiles[$sFieldname][$i]['type'] = get_mimetype($_FILES[$sFieldname]['tmp_name'][$i], TRUE, FALSE, $_FILES[$sFieldname]['name'][$i]);
			$aNewFiles[$sFieldname][$i]['error'] = $_FILES[$sFieldname]['error'][$i];
			$aNewFiles[$sFieldname][$i]['tmp_name'] = $_FILES[$sFieldname]['tmp_name'][$i];
		}
	}
	$_FILES = $aNewFiles;
	$_STOPWATCH->check('tier1_rearrangefiles', 'php');
	//var_dump($_FILES);
}

//------------------------------------------------------------------------------
// process request

try {

// prepare and enter tier2
if (TIER2_SEPARATED) {
	$_REQUEST->includeRequest($_SESSIONID, TRUE);
	DEBUG('Interface: Flow', 'entering tier2 now (via http)', DEBUG::BASIC);
	$_RESPONSE = $_REQUEST->send(TIER2_PATH, TIER2_HOST, TIER2_PORT);
} else {
	$_REQUEST->includeRequest($_SESSIONID);
	DEBUG('Interface: Flow', 'entering tier2 now (via include())', DEBUG::BASIC);
	include_once('controller.php');
}

$_STOPWATCH->stop('execution_time');
$_RESPONSE->addStopwatchTimes($_STOPWATCH->getTaskTimes());
if ($_REQUEST->getParam('debug') != TRUE) {
	$_RESPONSE->importLocales();
}

// output
DEBUG('Interface: Flow', 'processing output now', DEBUG::BASIC);
$_RESPONSE->saveOutput();

//------------------------------------------------------------------------------
// exception handling

} catch (Exception $e) {
	$_RESPONSE = ResponseFactory::getInstance('global');
	$_RESPONSE->addException($e);
	$_RESPONSE->addMetadata();
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