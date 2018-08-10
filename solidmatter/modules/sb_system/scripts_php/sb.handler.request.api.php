<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.request.application');

//------------------------------------------------------------------------------
/**
*/
class APIRequestHandler extends ApplicationRequestHandler {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		global $_RESPONSE;
		
		DEBUG('APIRequestHandler: Request Path = '.$_REQUEST->getURI(), DEBUG::HANDLER);
		
		parent::handleRequest($crSession);
		
		// FIXME: won't be very stable with 2-tier setups because of streams, part of the tier overhaul - it's currently broken anyway
		$_RESPONSE->forceRenderMode('xml');
		$_RESPONSE->forceLocaleMode(FALSE);
		
	}
	
}

?>