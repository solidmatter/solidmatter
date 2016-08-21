<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbSystem:sb.handler.request');

//------------------------------------------------------------------------------
/**
*/
class PortalHandler extends RequestHandler {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		$sURI = $_REQUEST->getURI();
		$aURI = parse_url($sURI);
		
		DEBUG('ApplicationHandler: Request Path = '.$sURI, DEBUG::HANDLER);
		
		// compute requested node / view / action / (rest of path ignored)
		$sNodeID = NULL;
		$sView = NULL;
		$sAction = NULL;
		$aPath = explode('/', $aURI['path']);
		if (isset($aPath[1]) && $aPath[1] != '-' && $aPath[1] != '') {
			$sNodeID = $aPath[1];
		}
		if (isset($aPath[2]) && $aPath[2] != '-' && $aPath[2] != '') {
			$sView = $aPath[2];
		}
		if (isset($aPath[3]) && $aPath[3] != '-' && $aPath[3] != '') {
			$sAction = $aPath[3];
		}
		
		// store request parameters
		if (isset($aURI['query'])) {
			$aQuery = array();
			parse_str($aURI['query'], $aQuery);
			foreach ($aQuery as $sParam => $sValue) {
				$_REQUEST->setParam($sParam, $sValue);
			}
		}
		
		// process request
		if ($sNodeID === NULL) {
			$nodeCurrent = $crSession->getRootNode()->getNode($_REQUEST->getSubject());
		} else {
			$nodeCurrent = $crSession->getNode($sNodeID);
		}
		
		$nodeCurrent->callView($sView, $sAction);
		$nodeCurrent->loadAncestors();
		//$nodeCurrent->storeSupportedAuthorisations();
		//$nodeCurrent->storeUserAuthorisations();
		$nodeCurrent->setAttribute('master', 'true');
		
		if ($_REQUEST->getParam('sbCommand') != NULL) {
			$_RESPONSE->addCommand($_REQUEST->getParam('sbCommand'));
		}
		
		$_RESPONSE->addData($nodeCurrent);
		$_RESPONSE->addLocale('sb_system', User::getCurrentLocale());
		$_RESPONSE->addMetadata('sb_system', 'lang', User::getCurrentLocale());
		$_RESPONSE->setTheme('_default');
		
		// output debug if desired 
		$bDebug = $_REQUEST->getParam('debug');
		if ($bDebug) {
			$_RESPONSE->setRenderMode('debug');
		}
		
	}
	
}

?>