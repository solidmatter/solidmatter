<?php

class FrontendRequestHandler {
	
	//------------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		try {
			
			$_GET['debug'] = TRUE;
			$_RESPONSE = ResponseFactory::getInstance('global');
			
			$sPath = '/'.$_SERVER['HTTP_HOST'].'::'.substr($_SERVER['REQUEST_URI'], 1);
			
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