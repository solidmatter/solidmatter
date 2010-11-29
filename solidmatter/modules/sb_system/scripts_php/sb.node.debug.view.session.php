<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.system.debug');

//------------------------------------------------------------------------------
/**
*/
class sbView_debug_session extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		global $_REQUEST;
		
		switch ($sAction) {
			
			case 'display':
				// TODO: check why sbSession is empty
				$elemSession = $_RESPONSE->convertArrayToItems('sbSession', sbSession::$aData);
				$_RESPONSE->addData($elemSession);
				/*
				$elemSession = $_RESPONSE->convertArrayToItems('$_SESSION', $_SESSION);
				$_RESPONSE->addData($elemSession);
				$elemSession = $_RESPONSE->convertArrayToItems('$_SERVER', $_SERVER);
				$_RESPONSE->addData($elemSession);
				$elemSession = $_RESPONSE->convertArrayToItems('$_ENV', $_ENV);
				$_RESPONSE->addData($elemSession);
				$elemSession = $_RESPONSE->convertArrayToItems('$_REQUEST', $_REQUEST);
				$_RESPONSE->addData($elemSession);*/
				$_RESPONSE->addData($_REQUEST);
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
}

?>