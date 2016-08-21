<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.request');
import('sb.tools.filesystem');

//------------------------------------------------------------------------------
/**
* Handles requests based on a session-token instead of a session-ID.
* This allows external applications and third parties to use userspecific content without compromising the session itself.
* The access domain is constrained via a service identifier and must be enforced in derived classes in method fulfilRequest().
*/
class TokenBasedHandler extends RequestHandler {
	
	protected $crSession = NULL;
	protected $aRequest = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* The handler method called from outside scope.
	* Request URI Format to be implemented in derived classes:
	* http://<site>/<service>/<subject>/<token>
	* @param crSession the current Content Repository's session
	* @return 
	*/
	public function handleRequest($crSession) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		$this->crSession = $crSession;
		$sTokenID = NULL;
		
		// parse request
		$aStuff = explode('/', $_REQUEST->getPath());
		if (!isset($aStuff[1]) || !isset($aStuff[2]) || !isset($aStuff[3])) {
			$this->fail('service, subject or token missing', 400);
		}
		
		$this->aRequest['service'] = $aStuff[1]; // fixed for the appropriate handler, store anyways
		$this->aRequest['subject'] = $aStuff[2];
		$this->aRequest['token'] = $aStuff[3];
		$sUserID = $this->getTokenOwner($this->aRequest['token']);
		if (!$sUserID) {
			$this->fail('token is invalid', 401);
		}
		User::setUUID($sUserID);
		
		$this->fulfilRequest();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Determines the token's user's UUID.
	* @param sTokenID the token ID retrieved from the requested URL
	* @return sUserUUID the UUID of the user who owns the token
	*/
	protected function getTokenOwner($sTokenID) {
		$stmtClear = $this->crSession->prepareKnown('sbJukebox/tokens/clear');
		$stmtClear->execute();
		$stmtGetOwner = $this->crSession->prepareKnown('sbJukebox/tokens/get/byToken');
		$stmtGetOwner->bindValue('token', $sTokenID, PDO::PARAM_STR);
		$stmtGetOwner->execute();
		$sUserUUID = FALSE;
		foreach ($stmtGetOwner as $aRow) {
			$sUserUUID = $aRow['user_uuid'];
		}
		return ($sUserUUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Refreshes the timeout of the used token by setting the timeout start date/time to the moment of the method's calling.
	*/
	protected function refreshToken() {
		$stmtRefresh = $this->crSession->prepareKnown('sbJukebox/tokens/refresh');
		$stmtRefresh->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtRefresh->execute();
	}
	
}

?>