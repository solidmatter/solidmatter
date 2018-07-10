<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 0.50.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
// config

if (!defined('PRETTYPRINT'))		define('PRETTYPRINT', TRUE);

if (!$sConfigFile = getenv('SOLIDMATTER_CONFIG_FILE')) {
	$sConfigFile = 'config.php';
}
require_once $sConfigFile;

//------------------------------------------------------------------------------
// create stopwatch

require_once('modules/sb_system/scripts_php/sb.tools.stopwatch.advanced.php');
Stopwatch::start();

//------------------------------------------------------------------------------
// init

require_once('modules/sb_system/scripts_php/sb.system.essentials.php');

// load libraries
import('sb.system.errors');
import('sb.tools.datetime');
import('sb.dom.request');
// optional due to autoload
import('sb.factory.response');
import('sb.dom.response');
import('sb.factory.db');
import('sb.cr.node');
import('sb.node');
import('sb.node.view');
import('sb.system');
import('sb.system.registry');
import('sb.pdo.system');
import('sb.cr.credentials');
import('sb.cr.repository');
import('sb.cr.session');

//------------------------------------------------------------------------------
// display form

$sCompleteURI = '';
if (isset($_SERVER['HTTPS'])) {
	$sCompleteURI = 'https://';
} else {
	$sCompleteURI = 'http://';
}
$sCompleteURI = $sCompleteURI.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$aRequest = parse_url($sCompleteURI);
$aPath = explode('/', $aRequest['path']);
if (in_array('..', $aPath)) {
	die_fancy('parent operator not allowed, aborting.');
}
//var_dumpp($aRequest);
$sRequestLocation = $aRequest['host'].$aRequest['path'];

// TODO: transport repository stuff to tier2
define('DEBUG', constant((string) $elemController->debug['enabled']));
		
$_REQUEST = new sbDOMRequest();

// store basic request info
$sLocation = (string) $elemSite['location'];
$_REQUEST->setLocation($sLocation);

if (substr($_SERVER['SERVER_PROTOCOL'], 0, 4) == 'HTTP') {
	if (!isset($_SERVER['HTTPS'])) {
		$sProtocol = 'http';
	} else {
		$sProtocol = 'https';
	}
} else {
	$sProtocol = 'unknown';
}
$sFullURI = $sProtocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$_REQUEST->setURI($sFullURI);

// store handler and repository info
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
	
//------------------------------------------------------------------------------
// process request




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
if (DEBUG) {
	if ($_REQUEST->getParam('debug') !== NULL) {
		$_RESPONSE->forceRenderMode('debug');
	} elseif ($_REQUEST->getParam('xml') !== NULL) {
		$_RESPONSE->forceRenderMode('xml');
	}
}
$_RESPONSE->saveOutput();


?>
