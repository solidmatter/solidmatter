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
class JBDownloadHandler extends TokenBasedHandler {
	
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
		
		
		
		$this->nodeSubject = $this->crSession->getNode($this->aRequest['subject']);
		if ($this->nodeSubject->getPrimaryNodeType() != 'sbJukebox:Track') {
			$this->fail('the adressed node is not a track, it\'s a '.$this->nodeTrack->getPrimaryNodeType(), 400);
		}
		exit();
		
	}
	
}

?>