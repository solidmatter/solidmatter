<?php

//------------------------------------------------------------------------------
/**
* Request URI Format:
* http://<site>/rss/<type>/<tokenid>
* 
* <type> = comments|albums|mostplayed
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.request.tokenbased');

//------------------------------------------------------------------------------
/**
*/
class JBPlaylistHandler extends TokenBasedHandler {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* 
	* 
	* @param 
	* @return 
	*/
	public function fulfilRequest() {
		
		// TODO: check permissions
		
		$domFeed = $this->getRSS($this->aRequest['subject']);
		$this->refreshToken();
		$domFeed->outputXML();
		exit();
		
	}
	
}

?>