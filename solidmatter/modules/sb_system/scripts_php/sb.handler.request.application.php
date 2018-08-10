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
class ApplicationRequestHandler extends RequestHandler {
	
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
		if ($aURI['node'] === NULL) {
			$nodeCurrent = $crSession->getRootNode()->getNode($_REQUEST->getSubject());
		} else {
			$nodeCurrent = $crSession->getNode($aURI['node']);
		}
		
		$nodeCurrent->callView($aURI['view'], $aURI['action']);
		$nodeCurrent->loadAncestors();
		$nodeCurrent->aGetElementFlags['ancestors'] = TRUE;
		$nodeCurrent->setAttribute('master', 'true');
		$nodeCurrent->aGetElementFlags['auth_user'] = TRUE;
		
		if ($_REQUEST->getParam('sbCommand') != NULL) {
			$_RESPONSE->addCommand($_REQUEST->getParam('sbCommand'));
		}
		
		$_RESPONSE->addData($nodeCurrent);
		$_RESPONSE->addLocale('sb_system', User::getCurrentLocale());
		$_RESPONSE->addMetadata('md_system', 'lang', User::getCurrentLocale());
		$_RESPONSE->setTheme('_default');
		
		// output debug if desired 
		$bDebug = $_REQUEST->getParam('debug');
		if ($bDebug) {
			$_RESPONSE->setRenderMode('debug');
		}
		
	}
	
}

?>