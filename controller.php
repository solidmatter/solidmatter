<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 0.00.00
*/
//------------------------------------------------------------------------------

try {

//------------------------------------------------------------------------------
// config

if (!defined('TIMEZONE'))			define('TIMEZONE', 'Europe/Berlin');
if (!defined('USE_REGISTRYCACHE'))	define('USE_REGISTRYCACHE', TRUE);
if (!defined('USE_SSL'))			define('USE_SSL', FALSE);
if (!defined('CHARSET'))			define('CHARSET', 'UTF-8');
if (!defined('DEBUG'))				define('DEBUG', TRUE);
if (!defined('TIER2_SEPARATED'))	define('TIER2_SEPARATED', TRUE);

define('REPOSITORY_DEFINITION_FILE', '_config/repositories.xml');

//------------------------------------------------------------------------------
// init

// locale settings
date_default_timezone_set(TIMEZONE);

// create stopwatch
require_once('modules/sb_system/scripts_php/sb.tools.stopwatch.advanced.php');
if (!isset($_STOPWATCH)) {
	$_STOPWATCH = new AdvancedStopwatch();
} else {
	$_STOPWATCH->check('tier_transition', 'com');
}

// basic
require_once('modules/sb_system/scripts_php/sb.system.essentials.php');

// load
import('sb.system.errors');
// optional due to autoload
//import('sb.factory.handler');
import('sb.factory.response');
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
//import('sb.cr.workspace');
//import('sb.cr.nodetypemanager');

$_STOPWATCH->check('tier2_load', 'load');

// globals
$_CONTROLLERCONFIG		= simplexml_load_file('_config/controller.xml');
$_RESPONSE				= ResponseFactory::getInstance('global');

// configure
DEBUG ? error_reporting(E_ALL|E_STRICT) : error_reporting(0);
DEBUG ? $iDebuglevel = E_ALL : $iDebuglevel = 0;
mb_internal_encoding(CHARSET);
System::init();

$_STOPWATCH->check('tier2_init', 'php');

//------------------------------------------------------------------------------
// recieve request

if (TIER2_SEPARATED) {
	$_REQUEST = new sbDOMRequest();
	$_REQUEST->recieveData();
	$_REQUEST->extractFiles();
	// TODO: disabled because DB is not yet initialized
	if ($_REQUEST->getParam('bounce') && TRUE) { //Registry::getValue('syb.system.debug.bounce.enabled')) {
		$_REQUEST->bounce();
	}
}

//------------------------------------------------------------------------------
// log into repository

$aRepository = $_REQUEST->getRepository();
$crCredentials = new sbCR_Credentials($aRepository['user'], $aRepository['pass']);
$crRepository = new sbCR_Repository($aRepository['id']);
$crSession = $crRepository->login($crCredentials, $aRepository['workspace']);
System::setSession($crSession);
Registry::setSession($crSession);
User::setSession($crSession);
$_SBSESSION = new sbSession($_REQUEST->getSessionID());
//var_dumpp($_SBSESSION);

//------------------------------------------------------------------------------
// check if registry cache is current state

if (USE_REGISTRYCACHE) {
	//var_dumpp(Registry::getValue('sb.system.debug.menu.debugmode', TRUE));
	//var_dumpp(Registry::getValue('sb.system.debug.menu.debugmode'));
	//var_dumpp(Registry::getValue('sb.system.cache.registry.changedetection', TRUE));
	//var_dumpp(Registry::getValue('sb.system.cache.registry.changedetection'));
	$sCheck = Registry::getValue('sb.system.cache.registry.changedetection');
	if ($sCheck != Registry::getValue('sb.system.cache.registry.changedetection', TRUE)) {
		$cacheRegistry = CacheFactory::getInstance('registry');
		$cacheRegistry->clear();
	}
}

//------------------------------------------------------------------------------
// check fingerprint 

if (User::isLoggedIn() && Registry::getValue('sb.system.security.login.fingerprint.enabled')) {
	if (!User::checkFingerprint()) {
		User::logout();
		throw new SecurityException('fingerprint has changed, logging out!');
	}
}

//------------------------------------------------------------------------------
// check if solidMatter is in backend mode and backend access is allowed

if ($_REQUEST->getHandler() == 'backend' && User::isLoggedIn() && User::getNode()->getProperty('security_backendaccess') != 'TRUE') {
	User::logout();
	throw new SecurityException('you are not allowed to access the backend!');
}

//------------------------------------------------------------------------------
// log if necessary

// TODO: modify to work with $_REQUEST object
if (Registry::getValue('sb.system.log.access.enabled')) {
	date_default_timezone_set('GMT');
	// TODO: save extracted info instead of global vars
	$sLog = "\r\n".str_repeat('#', 80)."\r\n".strftime('%y-%m-%d %H:%M:%S', time())."\r\n";
	if (Registry::getValue('sb.system.log.access.request')) {
		$sLog .= 'REQUEST:'.var_export($_REQUEST, TRUE)."\r\n";
	}
	if (Registry::getValue('sb.system.log.access.server')) {
		$sLog .= 'SERVER:'.var_export($_SERVER, TRUE)."\r\n";
	}
	//$sLog = 'SERVER:'.var_export($_SERVER)."\r\n";
	//System::logg($sLog, System::INFO);
}


//------------------------------------------------------------------------------
// assign request handler

$aHandler = match_handler($_CONTROLLERCONFIG, $_REQUEST->getHandler());
DEBUG('Controller: Handler', $aHandler['class'].'('.$aHandler['module'].':'.$aHandler['library'].')', DEBUG::HANDLER);
import($aHandler['library'], $aHandler['module']);
$hndProcessor = new $aHandler['class']();
$hndProcessor->handleRequest($crSession);

//------------------------------------------------------------------------------
// add metadata

$_STOPWATCH->check('tier2_main', 'php');
$_RESPONSE->addSystemMeta('sessionid', $_SBSESSION->getSessionID());
$_RESPONSE->addMetadata();
//$swTier2->check('tier2_metadata');
$_STOPWATCH->stop('tier2_complete', 'php');

//------------------------------------------------------------------------------
// send back to tier one

if (TIER2_SEPARATED) {
	$_RESPONSE->addStopwatchTimes($_STOPWATCH->getTaskTimes());
	// remove temp files
	foreach ($_FILES as $sFieldname => $aField) {
		foreach ($aField as $aEntry) {
			//unlink($aEntry['tmp_name']);
		}
	}
	// send response
	$_RESPONSE->saveOutput('xml');
}

//------------------------------------------------------------------------------
// exception handling

} catch (Exception $e) {
	
	if (TIER2_SEPARATED) {
		$_RESPONSE = ResponseFactory::getInstance('global');
		$_RESPONSE->addException($e);
		$_RESPONSE->addMetadata();
		$_RESPONSE->saveOutput('xml');
	} else {
		throw $e;
	}
	
}

//------------------------------------------------------------------------------
// utility functions

function match_handler($elemCurrentRoot, $sHandlerID) {
	
	// match site root
	foreach ($elemCurrentRoot->handlers->handler as $elemHandler) {
		if ($sHandlerID == (string) $elemHandler['id']) {
			$aHandler['library'] = (string) $elemHandler->library;
			$aHandler['class'] = (string) $elemHandler->class;
			$aHandler['module'] = 'sb_system';
			if (isset($elemHandler->module)) {
				$aHandler['module'] = (string) $elemHandler->module;
			}
			return ($aHandler);
		}
	}
	
	throw new sbException('handler '.$sHandlerID.' could not be initialized');
	
}

?>