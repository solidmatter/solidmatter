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

if (!defined('PRETTYPRINT'))	define('PRETTYPRINT', TRUE);
if (!defined('DEBUG'))			define('DEBUG', TRUE);

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
require_once('modules/sb_system/scripts_php/sb.form.php');

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


$sxmlConfig = sbCR::loadRepositoryDefinitions();

//------------------------------------------------------------------------------
// display form

$_REQUEST = new sbDOMRequest();
$_REQUEST->includeRequest('NO SESSION SUPPORTED');

$_RESPONSE = ResponseFactory::getInstance('global');
$_RESPONSE->setTheme('_admin');
$_RESPONSE->setRenderMode('RENDERED', 'text/html', "sb_system:setup.xsl");

$_RESPONSE->addData($sxmlConfig);
$_RESPONSE->addRequestData('SETUP', '', '');



$formCreateDB = buildForm_Database();
$formCreateRepo = buildForm_Repository();
$formCreateWS = buildForm_Workspace();

if ($_REQUEST->hasParam('create_db')) {
	$formCreateDB->recieveInputs();
	$formCreateDB->checkInputs();
}
if ($_REQUEST->hasParam('create_repo')) {
	$formCreateRepo->recieveInputs();
	$formCreateRepo->checkInputs();
}
if ($_REQUEST->hasParam('create_ws')) {
	$formCreateWS->recieveInputs();
	$formCreateWS->checkInputs();
}

$_RESPONSE->addData($formCreateDB);
$_RESPONSE->addData($formCreateRepo);
$_RESPONSE->addData($formCreateWS);




if (false) {
	// $aRepository = $_REQUEST->getRepository();
	$crCredentials = new sbCR_Credentials($aRepository['user'], $aRepository['pass']);
	$crRepository = new sbCR_Repository($aRepository['id']);
	$crSession = $crRepository->login($crCredentials, $aRepository['workspace']);
}

if ($_REQUEST->getParam('action') == 'init_repository') {
	$sRepositoryID = $_REQUEST->getParam('repo_id');
	sbCR::initRepository($sRepositoryID);
}

//------------------------------------------------------------------------------
// process request

// if (isset($elemSite['theme'])) {
// 	$_RESPONSE->setTheme('_admin');
// }
if (DEBUG) {
	if ($_REQUEST->getParam('debug') !== NULL) {
		$_RESPONSE->forceRenderMode('debug');
	} elseif ($_REQUEST->getParam('xml') !== NULL) {
		$_RESPONSE->forceRenderMode('xml');
	}
}
// $_RESPONSE->forceRenderMode('debug');
$_RESPONSE->saveOutput();





function buildForm_Database() {
	
	$formCreateDB = new sbDOMForm('create_database', 'Create Database', $_REQUEST->getLocation().'?create_db');
	
	$formCreateDB->addInput('dbid;string;required=TRUE', 'ID');
	$formCreateDB->addInput('host;string;required=TRUE', 'Host');
	$formCreateDB->addInput('port;integer;required=TRUE;minvalue=1;maxvalue=65535', 'Port');
	$formCreateDB->addInput('schema;string;required=TRUE', 'Schema');
	$formCreateDB->addInput('dbuser;string;required=TRUE', 'User');
	$formCreateDB->addInput('dbpass;string;required=TRUE', 'Pass');
	$formCreateDB->addSubmit('Create');
	
	return ($formCreateDB);
	
}



function buildForm_Repository() {
	
	$formCreateRepo = new sbDOMForm('create_repository', 'Create Repository', $_REQUEST->getLocation().'?create_repo');
	$formCreateRepo->addInput('repository_database;select;', 'Database');
	$formCreateRepo->addInput('repository;string;', 'Repository');
	$formCreateRepo->addInput('repository_prefix;string;', 'Prefix');
	$formCreateRepo->addSubmit('Create');
	
	global $sxmlConfig;
	foreach ($sxmlConfig->databases->children() as $elemDB) {
		$sID = $elemDB->getName();
		$aOptions[$sID] = $sID;
	}
	$formCreateRepo->setOptions('repository_database', $aOptions);
	
	return ($formCreateRepo);
	
}



function buildForm_Workspace() {
	
	$formCreateWS = new sbDOMForm('create_workspace', 'Create Workspace', $_REQUEST->getLocation().'?create_ws');
	$formCreateWS->addInput('workspace_repository;select;', 'Repository');
	$formCreateWS->addInput('workspace;string;', 'Workspace');
	$formCreateWS->addInput('workspace_prefix;string;', 'Prefix');
	$formCreateWS->addSubmit('Create');
	
	global $sxmlConfig;
	foreach ($sxmlConfig->repositories->children() as $elemRepo) {
		$sID = $elemRepo->getName();
		$aOptions[$sID] = $sID;
	}
	$formCreateWS->setOptions('workspace_repository', $aOptions);
	
	return ($formCreateWS);
	
}

?>
