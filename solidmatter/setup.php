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

//------------------------------------------------------------------------------
// display form

$_REQUEST = new sbDOMRequest();
$_REQUEST->includeRequest('NO SESSION SUPPORTED');

$_RESPONSE = ResponseFactory::getInstance('global');
$_RESPONSE->setTheme('_admin');
$_RESPONSE->setRenderMode('RENDERED', 'text/html', "sb_system:setup.xsl");

$_RESPONSE->addData(sbCR::getSimpleXML());
$_RESPONSE->addRequestData('SETUP', '', '');

$formCreate = new sbDOMForm('create', 'Create Repository', $_REQUEST->getLocation());
$formCreate->addInput('db;string;', 'DB');
$formCreate->addInput('dbuser;string;', 'User');
$formCreate->addInput('dbpass;string;', 'Pass');
$formCreate->addInput('repository;string;', 'Repository');
$formCreate->addInput('repository_prefix;string;', 'Prefix');
$formCreate->addInput('workspace;string;', 'Workspace');
$formCreate->addInput('workspace_prefix;string;', 'Prefix');
$formCreate->addSubmit('Create');

$_RESPONSE->addData($formCreate);

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

?>
