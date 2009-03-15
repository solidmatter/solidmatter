<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_jukebox_config extends sbJukeboxView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$formConfig = $this->buildForm();
				$formConfig->saveDOM();
				$_RESPONSE->addData($formConfig);
				break;
				
			case 'save':
				
				$formConfig = $this->buildForm();
				$formConfig->recieveInputs();
				
				if ($formConfig->checkInputs()) {
					if ($formConfig->getValue('new_password1') == $formConfig->getValue('new_password2')) {
						$nodeUser = User::getNode();
						$nodeUser->getProperties(); // FIXME: otherwise all aux values are empty
						$nodeUser->setProperty('security_password', $formConfig->getValue('new_password1'));
						$nodeUser->save();
//						var_dumpp($formConfig->getValue('new_password1'));
//						var_dumppp($formConfig->getValue('new_password2'));
					} else {
						$formConfig->setFormError('$locale/sbSystem/formerrors/not_identical');
					}
				} else {
					// do nothing, errors are set
				}
				$formConfig->saveDOM();
				$_RESPONSE->addData($formConfig);
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
	protected function buildForm() {
		
		$formConfig = new sbDOMForm(
			'config',
			'$locale/sbJukebox/labels/change_password',
			System::getURL('-', 'config', 'save'),
			$this->crSession
		);
			
		$formConfig->addInput('new_password1;password;minlength=4;maxlength=30;required=true;', '$locale/sbSystem/labels/password');
		$formConfig->addInput('new_password2;password;minlength=4;maxlength=30;required=true;', '$locale/sbSystem/labels/password_repeat');
		$formConfig->addSubmit('$locale/sbSystem/actions/apply');
		
		return ($formConfig);
		
	}
	
}

?>