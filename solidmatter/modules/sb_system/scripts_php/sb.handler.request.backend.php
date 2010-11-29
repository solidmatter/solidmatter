<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.request');

//------------------------------------------------------------------------------
/**
*/
class BackendRequestHandler extends RequestHandler {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		
		global $_RESPONSE;
		
		DEBUG('ApplicationHandler: Request Path = '.$_REQUEST->getURI(), DEBUG::HANDLER);
		
		$aURI = $this->parseURI();
		
		// process request
		if ($aURI['node_uuid'] === NULL) {
			$aURI['node_uuid'] = '/';
		}
		if ($aURI['view'] === NULL && $aURI['node_uuid'] == '/') {
			$aURI['view'] = 'backend';
		}
		
		$nodeCurrent = $crSession->getNode($aURI['node_uuid']);
		$nodeCurrent->callView($aURI['view'], $aURI['action']);
		$nodeCurrent->loadAncestors();
		$nodeCurrent->setAttribute('master', 'true');
		
		$nodeCurrent->aGetElementFlags['views'] = TRUE;
		$nodeCurrent->aGetElementFlags['ancestors'] = TRUE;
		$nodeCurrent->aGetElementFlags['auth_user'] = TRUE;
		
		if ($_REQUEST->getParam('sbCommand') != NULL) {
			$_RESPONSE->addCommand($_REQUEST->getParam('sbCommand'));
		}
		
		$_RESPONSE->addData($nodeCurrent);
		$_RESPONSE->addLocale('sbSystem', User::getCurrentLocale());
		$_RESPONSE->addLocale($nodeCurrent->getModule(), User::getCurrentLocale());
		$_RESPONSE->addMetadata('md_system', 'lang', User::getCurrentLocale());
		// FIXME: the theme should be a matter of interface layer!
		$_RESPONSE->setTheme('_admin_grey');
		
		// output debug if desired 
		$bDebug = $_REQUEST->getParam('debug');
		if ($bDebug) {
			$_RESPONSE->setRenderMode('debug');
		}
		
	}
	
}

?>