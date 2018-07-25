<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver MÃ¼ller]
*	@version 0.00.00
*/
//------------------------------------------------------------------------------

try {


//------------------------------------------------------------------------------
// init

if (!defined('TIER2_SEPARATED'))	define('TIER2_SEPARATED', TRUE);

// create stopwatch
require_once('modules/sb_system/scripts_php/sb.tools.stopwatch.advanced.php');
Stopwatch::check('tier_transition', 'com');

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

Stopwatch::check('tier2_load', 'load');

// globals

$_RESPONSE				= ResponseFactory::getInstance('global');

// configure
System::init();
Stopwatch::check('tier2_init', 'php');

//------------------------------------------------------------------------------
// recieve request

// if (TIER2_SEPARATED) {
// 	$_REQUEST = new sbDOMRequest();
// 	$_REQUEST->recieveData();
// 	$_REQUEST->extractFiles();
// }

// TODO: registry disabled because DB is not yet initialized
// if ($_REQUEST->getParam('bounce') !== NULL && TRUE) { //Registry::getValue('syb.system.debug.bounce.enabled')) {
// 	$_REQUEST->bounce();
// }

//------------------------------------------------------------------------------
// log into repository/workspace, acquire valid sbCR_Session

$aRepository = $_REQUEST->getRepository();
$crRepository = sbCR::getRepository($aRepository['id']);
$crCredentials = new sbCR_Credentials($aRepository['user'], $aRepository['pass']);
$crSession = $crRepository->login($crCredentials, $aRepository['workspace']);

// init various system-level objects with acquired sbCR_Session
System::setSession($crSession);
Registry::setSession($crSession);
User::setSession($crSession);
CacheFactory::setSession($crSession);
sbSession::start($_REQUEST->getSessionID());
sbSession::setTimeout(Registry::getValue('sb.system.session.timeout'));

//------------------------------------------------------------------------------
// check if registry cache is current state

if (CONFIG::USE_REGISTRYCACHE) {
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

$elemHandler = CONFIG::getHandlerConfig($_REQUEST->getHandler());
$sHandlerClass = (string) $elemHandler['class'];
DEBUG('Controller: using Handler '.$elemHandler['class'].'('.$elemHandler['module'].':'.$elemHandler['library'].')', DEBUG::HANDLER);
import((string) $elemHandler['library'], (string) $elemHandler['module']);
$hndProcessor = new $sHandlerClass();
System::setRequestHandler($hndProcessor);
$hndProcessor->handleRequest($crSession);

//------------------------------------------------------------------------------
// add metadata

Stopwatch::check('tier2_main', 'php');
$_RESPONSE->addMetadata('md_system', 'sessionid', sbSession::getID());
$_RESPONSE->finalizeMetadata();
//$swTier2->check('tier2_metadata');
Stopwatch::stop('tier2_complete', 'php');

//------------------------------------------------------------------------------
// send back to tier one

if (TIER2_SEPARATED) {
	$_RESPONSE->addStopwatchTimes(Stopwatch::getTaskTimes());
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
	if (DEBUG::EXCEPTIONS) {
		DEBUG(get_class($e).': '.$e->getMessage().' ('.$e->getFile().', '.$e->getLine().')');
	}
	if ($e instanceof SessionTimeoutException) {
		// TODO: refresh session lifespan with current registry value
		// TODO: differentiate between page-level and AJAX-requests (prototype.js does directly follow the location-header on 307 status codes)
		$sZombieRequest = sbSession::getData('last_recallable_action');
		sbSession::destroy(TRUE);
		if ($sZombieRequest != NULL) {
			sbSession::addData('last_recallable_action', $sZombieRequest);
		}
		sbSession::commit();
		if ($_REQUEST->getServerValue('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') {
			$_RESPONSE->redirect('-', 'login', NULL, NULL, 401);
		} else {
			$_RESPONSE->redirect('-', 'login', NULL, NULL, 307);
		}
	}
// 	if (TIER2_SEPARATED) {
// 		$_RESPONSE = ResponseFactory::getInstance('global');
// 		$_RESPONSE->addException($e);
// 		$_RESPONSE->addMetadata();
// 		$_RESPONSE->saveOutput('xml');
// 	} else {
		throw $e;
// 	}
}

?>