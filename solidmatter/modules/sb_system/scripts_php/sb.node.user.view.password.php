<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_user_password extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		$formPassword = $this->buildForm();
		
		switch ($sAction) {
			
			case 'display':
				// everything happens outside of this switch()
				break;
				
			case 'save':
				
				/*if (!Registry::getValue('sb.system.security.users.password.change.allowed')) {
					logEvent(System::SECURITY, 'sbJukebox', 'UNAUTHORIZED_PASSWORD_CHANGE', 'user tried to change password while registry entry says "no"', $this->nodeSubject->getProperty('jcr:uuid'));
					throw new SecurityException('users are not allowed to change passwords - how did you get here?');
				}*/
					
				$formPassword = $this->buildForm();
				$formPassword->recieveInputs();
				
				if ($formPassword->checkInputs()) {
					if ($formPassword->getValue('new_password1') == $formPassword->getValue('new_password2')) {
						$nodeUser = $this->nodeSubject;
						$nodeUser->getProperties(); // FIXME: otherwise all aux values are empty
						$nodeUser->setProperty('security_password', $formPassword->getValue('new_password1'));
						$nodeUser->save();
						$this->logEvent(System::SECURITY, 'PASSWORD_SET', $nodeUser->getProperty('label').' ('.$nodeUser->getName().')');
					} else {
						$formPassword->setError('new_password1', '$locale/sbSystem/formerrors/not_identical');
						$formPassword->setError('new_password2', '$locale/sbSystem/formerrors/not_identical');
					}
				} else {
					// do nothing, errors are set
				}
				break;
			
		}
		
		$formPassword->saveDOM();
		$_RESPONSE->addData($formPassword);
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function buildForm() {
	
		$formConfig = new sbDOMForm(
				'password',
				'$locale/sbSystem/labels/change_password',
				System::getRequestURL($this->nodeSubject->getIdentifier(), 'password', 'save'),
				$this->crSession
		);
			
		$formConfig->addInput('new_password1;password;minlength=4;maxlength=30;required=true;', '$locale/sbSystem/labels/password_new');
		$formConfig->addInput('new_password2;password;minlength=4;maxlength=30;required=true;', '$locale/sbSystem/labels/password_repeat');
		$formConfig->addSubmit('$locale/sbSystem/actions/apply');
		
		return ($formConfig);
	
	}
	
}

?>