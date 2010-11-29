<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sbSystem
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.request');

//------------------------------------------------------------------------------
/**
*/
class FrontendRequestHandler {
	
	//------------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		try {
			
			global $_RESPONSE;
			
			$sPath = '/'.$_REQUEST->getSubject().$_REQUEST->getRelativePath();
			
			$nodeCurrent = $crSession->getNode($sPath);
			$nodeCurrent->callView('render');
			
			$_RESPONSE->addData($nodeCurrent);
			
		} catch (NodeNotFoundException $e) {
			
			$_RESPONSE = ResponseFactory::getInstance('global');
			$_RESPONSE->addHeader('FileNotFound: '.$sPath, TRUE, 404);
			
		}
		
	}
	
}

?>