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
*/
class TokenBasedHandler extends RequestHandler {
	
	protected $crSession = NULL;
	protected $aRequest = null;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* Request URI Format to be implemented in derived classes:
	* http://<site>/<service>/<subject>/<token>
	* 
	* @param 
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
	* 
	* @param 
	* @return 
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
	* 
	* @param 
	* @return 
	*/
	protected function refreshToken() {
		$stmtRefresh = $this->crSession->prepareKnown('sbJukebox/tokens/refresh');
		$stmtRefresh->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtRefresh->execute();
	}
	
}

?>