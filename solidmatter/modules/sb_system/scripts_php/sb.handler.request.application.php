<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class ApplicationRequestHandler {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		$sNodeID = NULL;
		$sView = NULL;
		$sAction = NULL;
		
		$sPath = $_REQUEST->getPath();
		
		DEBUG('ApplicationHandler: Request Path', $sPath, DEBUG::HANDLER);
		
		$aStuff = explode('/', $sPath, 5);
		
		//var_dumpp($aStuff);
		if (isset($aStuff[1]) && $aStuff[1] != '-' && $aStuff[1] != '') {
			$sNodeID = $aStuff[1];
		}
		if (isset($aStuff[2]) && $aStuff[2] != '-' && $aStuff[2] != '') {
			$sView = $aStuff[2];
		}
		if (isset($aStuff[3]) && $aStuff[3] != '-' && $aStuff[3] != '') {
			$sAction = $aStuff[3];
		}
		// extract parameters
		if (isset($aStuff[4]) && $aStuff[4] != '') {
			// TODO: rework this hack intended to ignore filenames
			if (substr_count($sPath, '?')) {
				$aStuff[4] = substr($aStuff[4], strpos($aStuff[4], '?') + 1);
			}
			$aParams = explode('&', $aStuff[4]);
			foreach ($aParams as $sParam) {
				if (substr_count($sParam, '=') > 0) {
					list($sKey, $sValue) = explode('=', $sParam);
					//var_dump($sKey.'|'.$sValue);
					$_REQUEST->setParam($sKey, $sValue);
				} else {
					$_REQUEST->setParam($sParam, TRUE);
				}
			}
		}
		
		if ($sNodeID === NULL) {
			$nodeCurrent = $crSession->getRootNode()->getNode($_REQUEST->getSubject());
		} else {
			$nodeCurrent = $crSession->getNode($sNodeID);
		}
		
		$nodeCurrent->callView($sView, $sAction);
		$nodeCurrent->loadAncestors();
		$nodeCurrent->loadUserAuthorisations();
		$nodeCurrent->storeAncestors(TRUE, TRUE);
		$nodeCurrent->setAttribute('master', 'true');
		
		if ($_REQUEST->getParam('sbCommand') != NULL) {
			$_RESPONSE->addCommand($_REQUEST->getParam('sbCommand'));
		}
		
		$_RESPONSE->addData($nodeCurrent);
		$_RESPONSE->addLocale('sb_system', 'de');
		$_RESPONSE->addSystemMeta('lang', 'de');
		$_RESPONSE->setTheme('_default');
		
		// output debug if desired 
		$bDebug = $_REQUEST->getParam('debug');
		if ($bDebug) {
			$_RESPONSE->setRenderMode('debug');
		}
		
	}
	
}

?>