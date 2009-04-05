<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.authentication');
import('sb.tools.datetime');

//------------------------------------------------------------------------------
/**
*/
class DefaultAuthenticationHandler extends AuthenticationHandler {
	
	protected $crSession;
	
	protected $sUserName;
	protected $sUserUUID;
	protected $aUserInfo;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crSession) {
		$this->crSession = $crSession;		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getUserInfo($sUserName) {
		
		$this->sUserName = $sUserName;
		$stmtUserdata = $this->crSession->prepareKnown('sb_system/root/view/login/loadUserdata');
		$stmtUserdata->bindParam('login', $sUserName, PDO::PARAM_STR);
		$stmtUserdata->execute();
		$aRow = $stmtUserdata->fetch();
		$stmtUserdata->closeCursor();
		
		if ($aRow != NULL) {
			$this->sUserUUID = $aRow['uuid'];
			$this->aUserInfo = $aRow;
		}
		
		return ($aRow);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function login($sUserName, $sPassword) {
		
		$aResult = array(
			'login_successful' => FALSE,
			'failure_reason' => 'none',
			'failure_details' => NULL,
			'user_uuid' => NULL
		);
		
		// does user exist? if not return immediately, errors occur otherwise!
		if (!$this->checkUser($sUserName)) {
			$aResult['failure_reason'] = 'inexistent_user';
			return ($aResult);
		}
		
		// TODO: remove these references and just use methods to improve customization?
		$aInfo = $this->aUserInfo;
		
		// does he have to change his password?
		if ($this->mustChangePassword()) {
			$aResult['failure_reason'] = 'password_expired';
		}
		
		// check lock time and reset if necessary
		if ($this->tracksFailedLogins()) {
			$tsDeactivated	= datetime_mysql2timestamp($aInfo['dt_failedlogin']);
			$tsNow			= time();
			$tsDifference	= ($tsDeactivated + (Registry::getValue('sb.system.security.login.failed.locktime') * 60)) - $tsNow;
			if ($tsDifference > 0) {
				$aResult['failure_details']['minutes_remaining'] = ceil($tsDifference / 60);
			} else {
				$this->resetFailedLogins();
				$aInfo['n_failedlogins'] = 0;
			}
		}
		
		// does the password match?
		if (!$this->checkPassword($sPassword)) {
			$aResult['failure_reason'] = 'wrong_password';
			if ($this->tracksFailedLogins()) {
				$this->increaseFailedLogins();
				$aInfo['n_failedlogins']++;
				$iLoginsRemaining =  - $aInfo['n_failedlogins'];
			}
		}
		
		// too many failed login attempts and other events 
		if ($this->tracksFailedLogins() && $aInfo['n_failedlogins'] > $this->getAllowedFailedLogins()) {
			$aResult['failure_reason'] = 'locked_temporarily';
			$aResult['failure_details']['num_failedlogins'] = $aInfo['n_failedlogins'];
		} elseif ($aInfo['b_locked'] == 'TRUE') {
			$aResult['failure_reason'] = 'locked_manually';
		}  elseif ($aInfo['b_activated'] == 'FALSE') {
			$aResult['failure_reason'] = 'account_inactive';
		}
		
		// is the account expired?
		if ($aInfo['dt_expires'] != NULL && datetime_mysql2timestamp($aInfo['dt_expires']) < time()) {
			$aResult['failure_reason'] = 'account_expired';	
		}
		
		// everything ok? then tell the client!
		if ($aResult['failure_reason'] == 'none') {
			$aResult['login_successful'] = TRUE;
			$aResult['user_uuid'] = $aInfo['uuid'];
		}
		
		return ($aResult);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function checkUser($sUserName) {
		$aInfo = $this->getUserInfo($sUserName);
		if ($aInfo == NULL) {
			return (FALSE);	
		}
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function checkPassword($sPassword) {
		if ($this->aUserInfo['s_password'] != $sPassword) {
			return (FALSE);
		}
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function mustChangePassword() {
		// TODO: implement password expiration
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*protected function getFailedLogins() {
		$stmtUserdata = $crSession->prepareKnown('sb_system/root/view/login/loadUserdata');
		$stmtUserdata->bindParam('login', $this->sUserName, PDO::PARAM_STR);
		$stmtUserdata->execute();
		$aRow = $stmtUserdata->fetch();
		$stmtUserdata->closeCursor();
		return ($aRow);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function tracksFailedLogins() {
		if ($this->getAllowedFailedLogins() == 0) {
			return (FALSE);
		}
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function increaseFailedLogins() {
		$stmtIncrease = $this->crSession->prepareKnown('sb_system/root/view/login/increaseFailedLogins');
		$stmtIncrease->bindParam('user_id', $this->sUserUUID,  PDO::PARAM_INT);
		$stmtIncrease->execute();
		$stmtIncrease->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getAllowedFailedLogins() {
		return (Registry::getValue('sb.system.security.login.failed.numallowed'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function resetFailedLogins() {
		$stmtReset = $this->crSession->prepareKnown('sb_system/root/view/login/resetFailedLogins');
		$stmtReset->bindParam('user_id', $this->sUserUUID,  PDO::PARAM_INT);
		$stmtReset->execute();
		$stmtReset->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeSuccessfulLogin() {
		$stmtSuccess = $this->crSession->prepareKnown('sb_system/root/view/login/successfulLogin');
		$stmtSuccess->bindParam('user_id', $this->sUserUUID, PDO::PARAM_INT);
		$stmtSuccess->execute();
		$stmtSuccess->closeCursor();				
	}
	
}

?>