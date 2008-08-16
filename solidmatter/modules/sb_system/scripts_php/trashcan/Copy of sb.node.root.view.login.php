<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.datetime');

//------------------------------------------------------------------------------
/**
*/
class sbView_root_login extends sbView {
	
	protected $bLoginRequired = FALSE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		global $_REQUEST;
		
		if(DEBUGG){var_dumpp($_REQUEST);}
		if(DEBUGG){var_dumpp($_RESPONSE);}
		
		switch ($sAction) {
			
			// just display the login screen
			case 'display':
				
				$formLogin = $this->buildForm();
				$formLogin->saveDOM();
				
				$_RESPONSE->addData($formLogin);
				
				return ($this->nodeSubject);
				
			// attempt to login
			case 'login':
				
				$formLogin = $this->buildForm();
				$formLogin->recieveInputs();
				$aInputs = $formLogin->getValues();
				
				if ($formLogin->checkInputs()) { // form is filled correctly
					
					$crSession = $this->nodeSubject->getSession();
					$stmtUserdata = $crSession->prepareKnown('sb_system/root/view/login/loadUserdata');
					$stmtUserdata->bindParam('login', $aInputs['login'], PDO::PARAM_STR);
					$stmtUserdata->execute();
					$aRow = $stmtUserdata->fetch();
					$stmtUserdata->closeCursor();
					
					// wrong user or password
					if ($aRow == NULL || $aRow['s_password'] != $aInputs['password']) {
						// log security event
						$this->logEvent(System::SECURITY, 'LOGIN_FAILED', '"'.$aInputs['login'].'" => "'.$aInputs['password'].'"');
						// tell form what happened
						$formLogin->setFormError('$locale//formerrors/wrong_logindata');
						return($this->returnForm($formLogin));
					}
					
					// account locked for various reasons
					$iNumFailedLogins = $aRow['n_failedlogins'];
					// too many failed login attempts
					if (Registry::getValue('sb.system.security.login.failed.numallowed') != 0 && $iNumFailedLogins >= Registry::getValue('sb.system.security.login.failed.numallowed') - 1) {
						
						$tsDeactivated	= datetime_mysql2timestamp($aRow['dt_failedlogin']);
						$tsNow			= gmmktime();
						$tsDifference	= ($tsDeactivated + (get_config('sb_system/failed_logins/locktime') * 60)) - $tsNow;
						
						if ($tsDifference > 0) {
							$iMinutes = ceil($tsDifference / 60);
							$formLogin->setFormError('$locale//formerrors/account_locked_temp');
							return($this->returnForm($formLogin));
						} else {
							$stmtReset = $crSession->prepareKnown('sb_system/root/view/login/resetFailedLogins');
							$stmtReset->bindParam('user_id', $aRow['uuid'],  PDO::PARAM_INT);
							$stmtReset->execute();
							$stmtReset->closeCursor();
							$iNumFailedLogins = 0;
						}
					// account locked
					} elseif ($aRow['b_locked'] == 'TRUE') {
						$formLogin->setFormError('$locale//formerrors/account_locked');
						return($this->returnForm($formLogin));
					// account not (yet) activated
					} elseif ($aRow['b_activated'] == 'FALSE') {
						$formLogin->setFormError('$locale//formerrors/account_inactive');
						return($this->returnForm($formLogin));
					}
					
					// check password
					if ($aRow['s_password'] != $aInputs['password']) {
						
						if (Registry::getValue('sb.system.security.login.failed.numallowed') != 0) {
							$stmtIncrease = $crSession->prepareKnown('sb_system/root/view/login/increaseFailedLogins');
							$stmtIncrease->bindParam('user_id', $aRow['uuid'],  PDO::PARAM_INT);
							$stmtIncrease->execute();
							$stmtIncrease->closeCursor();
							$iNumFailedLogins++;
							$iLoginsRemaining = get_config('sb_system/failed_logins/num_allowed') - $iNumFailedLogins;
							$formLogin->setFormError('$locale//formerrors/wrong_logindata');
						} else {
							$formLogin->setFormError('$locale//formerrors/wrong_logindata');
						}
						return($this->returnForm($formLogin));
						
					} else {
						
						$stmtSuccess = $crSession->prepareKnown('sb_system/root/view/login/successfulLogin');
						$stmtSuccess->bindParam('user_id', $aRow['uuid'], PDO::PARAM_INT);
						$stmtSuccess->execute();
						$stmtSuccess->closeCursor();
						
						// FIXME: use real remote adress and not the one from TIER1!
						$aData['user_id']			= $aRow['uuid'];
						$aData['user_ip']			= $_REQUEST->getServerValue('REMOTE_ADDR');
						$aData['secure_login']		= TRUE;
						$aData['is_admin']			= FALSE;
						$aData['fingerprint']		= md5($_REQUEST->getServerValue('REMOTE_ADDR').$_REQUEST->getServerValue('HTTP_USER_AGENT'));
						
						$nodeUser = $crSession->getNode($aRow['uuid']);
						$niParents = $nodeUser->getParents();
						$aParentUUIDs = array();
						foreach ($niParents as $nodeParent) {
							//echo $nodeParent->getProperty('uid');
							$aParentUUIDs[] = $nodeParent->getProperty('jcr:uuid');
							if ($nodeParent->getProperty('uid') == 'sb_system:admins') {
								$aData['is_admin'] = TRUE;
							}
						}
						$aData['groups'] = $aParentUUIDs;
						
						sbSession::addData('userdata', $aData);
						
						//$_SESSION['system']['permissions']	= get_permissions($rsLogin->Column('id'));
						//$_SESSION['system']['permissions']['hash'] = $PERMISSIONSHASH;
						
						/*if (isset($aInputs['stayloggedin'])
							&& $aInputs['stayloggedin'] == 'TRUE'
							&& get_config('system', 'SECURITY_STAY_LOGGEDIN_ALLOWED') == 'TRUE'
							) {
							if (is_permitted('SYSTEM_STAY_LOGGEDIN')) {
								setcookie('solidBrickz', $rsLogin->Column('id').':'.md5($rsLogin->Column('s_password')), time()+60*60*24*100);
							} else {
								$this->ThrowError('ERROR_STAY_LOGGEDIN_NOT_ALLOWED');
								return (FALSE);
							}
						}*/
						
						// log successful login
						$this->logEvent(System::INFO, 'LOGIN_SUCCESSFUL', '"'.$aInputs['login'].'" with fingerprint "'.$aData['fingerprint'].'" from "'.$aData['user_ip'].'"');
						
						$_RESPONSE->addHeader('Location: http://'.TIER1_HOST, TRUE, 302);
						
					}
					
				} else { // form isn't even filled like it should be
					
					// log security event if captcha was incorrect
					if (Registry::getValue('sb.system.security.login.captcha.enabled')) {
						if ($formLogin->hasError('captcha')) {
							$ifCaptcha = $formLogin->getInput('captcha');
							$this->logEvent(System::SECURITY, 'CAPTCHA_INCORRECT', '"'.$aInputs['captcha'].'" given, required "'.$ifCaptcha->getSequence().'"');
						}
					}
					
				}
				$formLogin->saveDOM();
				$_RESPONSE->addData($formLogin);
				return ($this->nodeSubject);
				//break;
			
			// logout and redirect to login screen
			case 'logout':
				sbSession::destroy();
				$_RESPONSE->addHeader('Location: http://'.TIER1_HOST, TRUE, 302);
				break;
			
			// used to access all generated captchas
			case 'getCaptcha':
				import('sb.image.captcha');
				if ($_GET['uid'] == 'login_backend') {
					$sType = Registry::getValue('sb.system.security.login.captcha.type');
					$imgChallenge = new CaptchaImage(200, 80, 4);
					$imgChallenge->generate();
					sbSession::$aData['captcha']['login_backend'] = $imgChallenge->getSequence();
					$imgChallenge->output(GIF);
				}
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function buildForm() {
		
		//$this->elemSubject->loadAttributes('basic');
		
		$formLogin = new sbDOMForm(
			'login_backend',
			'$locale/system/general/labels/login',
			//'/backend.view=login&action=login'
			'/-/login/login',
			$this->crSession
		);
		$formLogin->addInput('login;string;required=TRUE', '$locale/system/general/labels/login');
		$formLogin->addInput('password;password;required=TRUE', '$locale/system/general/labels/password');
		if (Registry::getValue('sb.system.security.login.captcha.enabled')) {
			$formLogin->addInput('captcha;captcha;required=TRUE;uid=login_backend', '$locale/system/general/labels/captcha');
		}
		
		$formLogin->addSubmit('$locale/system/general/actions/login');
		
		return ($formLogin);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*private function returnForm($formLogin) {
		$formLogin->saveDOM();
		$_RESPONSE->addData($formLogin);
		return ($this->nodeSubject);
	}*/
	
	
	
}


?>