<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.system.session');

//------------------------------------------------------------------------------
/**
* 
*/
class User {
	
	private static $crSession = NULL;
	private static $sbAuthManager = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function setSession($crSession) {
		self::$crSession = $crSession;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getUUID() {
		if (isset(sbSession::$aData['userdata']['user_id'])) {
			return (sbSession::$aData['userdata']['user_id']);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getGroupUUIDs() {
		if (isset(sbSession::$aData['userdata']['groups'])) {
			return (sbSession::$aData['userdata']['groups']);
		} else {
			return (array());
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getNode() {
		return (self::$crSession->getNodeByIdentifier(self::getUUID()));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected static function getAuthenticationManager() {
		
		if (self::$sbAuthManager != NULL) {
			return (self::$sbAuthManager);
		}
		
		switch (Registry::getValue('sb.system.security.login.authentication.method')) {
			case 'default': // solidMatter internal storage
				import('sb.system.authentication.default');
				return (new DefaultAuthenticationManager(self::$crSession));
				//break;
			default:
				throw new sbException('unrecognized auth method: "'.Registry::getValue('sb.system.security.login.auth.method').'"');
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function attemptLogin($sUser, $sPassword) {
		return (self::getAuthenticationManager()->login($sUser, $sPassword));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function isLoggedIn() {
		if (isset(sbSession::$aData['userdata']['user_id'])) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function isAdmin() {
		if (isset(sbSession::$aData['userdata']['is_admin']) && sbSession::$aData['userdata']['is_admin'] == TRUE) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function isAuthorised($sAuthorisation, $mSubject) {
		if (is_string($mSubject)) { // should be uuid
			$nodeSubject = self::$crSession->getNodeByIdentifier($mSubject);
			return (self::isAuthorised($sAuthorisation, $nodeSubject));
		} else { // should be node
			return ($mSubject->isAuthorised($sAuthorisation, self::getUUID()));
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getCurrentLocale() {
		return (Registry::getValue('sb.system.language'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function logout() {
		sbSession::destroy();
		//$nodeRoot = self::$crSession->getRootNode();
		//$nodeRoot->callView('login', 'logout');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function checkFingerprint() {
		$sCurrentFP = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
		if (sbSession::$aData['userdata']['fingerprint'] == $sCurrentFP) {
			return (TRUE);
		}
		return (FALSE);
	}
	
}

?>