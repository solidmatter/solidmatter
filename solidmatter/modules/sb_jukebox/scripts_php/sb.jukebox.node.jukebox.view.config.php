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
		
		$formPassword = $this->buildPasswordForm();
		$formSettings = $this->buildSettingsForm();
		
		switch ($sAction) {
			
			case 'display':
				// everything happens outside of switch
				break;
				
			case 'savePassword':
				
				$formPassword = $this->buildPasswordForm();
				$formPassword->recieveInputs();
				
				if ($formPassword->checkInputs()) {
					if ($formPassword->getValue('new_password1') == $formPassword->getValue('new_password2')) {
						$nodeUser = User::getNode();
						$nodeUser->getProperties(); // FIXME: otherwise all aux values are empty
						$nodeUser->setProperty('security_password', $formPassword->getValue('new_password1'));
						$nodeUser->save();
					} else {
						$formPassword->setFormError('$locale/sbSystem/formerrors/not_identical');
					}
				} else {
					// do nothing, errors are set
				}
				break;
			
			case 'saveSettings':
				
				$formSettings = $this->buildSettingsForm();
				$formSettings->recieveInputs();
				
				if ($formSettings->checkInputs()) {
					
					Registry::setValue('sb.jukebox.voting.style',					$formSettings->getValue('votingstyle'),				User::getUUID());
					Registry::setValue('sb.jukebox.voting.scale.min',				0-$formSettings->getValue('minstars'),				User::getUUID());
					Registry::setValue('sb.jukebox.voting.scale.max',				$formSettings->getValue('maxstars'),				User::getUUID());
					Registry::setValue('sb.jukebox.latestalbums.amount.default',	$formSettings->getValue('latestalbums'),			User::getUUID());
					Registry::setValue('sb.jukebox.latestalbums.amount.expanded',	$formSettings->getValue('latestalbumsexpanded'),	User::getUUID());
					Registry::setValue('sb.jukebox.latestcomments.amount.default',	$formSettings->getValue('latestcomments'),			User::getUUID());
					Registry::setValue('sb.jukebox.latestcomments.amount.expanded',	$formSettings->getValue('latestcommentsexpanded'),	User::getUUID());
					Registry::setValue('sb.jukebox.charts.amount.default',			$formSettings->getValue('charts'),					User::getUUID());
					Registry::setValue('sb.jukebox.charts.amount.expanded',			$formSettings->getValue('chartsexpanded'),			User::getUUID());
					Registry::setValue('sb.jukebox.adminmode.enabled',				constant($formSettings->getValue('adminmode')),		User::getUUID());
					
				} else {
					// do nothing, errors are set
				}
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
		$formPassword->saveDOM();
		$_RESPONSE->addData($formPassword);
		$formSettings->saveDOM();
		$_RESPONSE->addData($formSettings);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildPasswordForm() {
		
		$formConfig = new sbDOMForm(
			'password',
			'$locale/sbJukebox/labels/change_password',
			System::getRequestURL('-', 'config', 'savePassword'),
			$this->crSession
		);
			
		$formConfig->addInput('new_password1;password;minlength=4;maxlength=30;required=true;', '$locale/sbSystem/labels/password');
		$formConfig->addInput('new_password2;password;minlength=4;maxlength=30;required=true;', '$locale/sbSystem/labels/password_repeat');
		$formConfig->addSubmit('$locale/sbSystem/actions/apply');
		
		return ($formConfig);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildSettingsForm() {
		
		$formConfig = new sbDOMForm(
			'settings',
			'$locale/sbSystem/labels/settings',
			System::getRequestURL('-', 'config', 'saveSettings'),
			$this->crSession
		);
		
		$formConfig->addInput('votingstyle;select;options=HOTEL|MARKED|RELATIVE;',			'$locale/sbJukebox/labels/votingstyle');
		$formConfig->addInput('minstars;select;options=1|2|3|4|5;',							'$locale/sbJukebox/labels/minstars');
		$formConfig->addInput('maxstars;select;options=1|2|3|4|5;',							'$locale/sbJukebox/labels/maxstars');
		$formConfig->addInput('latestalbums;integer;minvalue=1;maxvalue=100',				'$locale/sbJukebox/labels/amount_latestalbums');
		$formConfig->addInput('latestalbumsexpanded;integer;minvalue=10;maxvalue=1000;',	'$locale/sbJukebox/labels/amount_latestalbums_expanded');
		$formConfig->addInput('latestcomments;integer;minvalue=1;maxvalue=100;',			'$locale/sbJukebox/labels/amount_latestcomments');
		$formConfig->addInput('latestcommentsexpanded;integer;minvalue=10;maxvalue=1000;',	'$locale/sbJukebox/labels/amount_latestcomments_expanded');
		$formConfig->addInput('charts;integer;minvalue=1;maxvalue=100;',					'$locale/sbJukebox/labels/amount_charts');
		$formConfig->addInput('chartsexpanded;integer;minvalue=10;maxvalue=1000;',			'$locale/sbJukebox/labels/amount_charts_expanded');
		$formConfig->addInput('adminmode;checkbox;',										'$locale/sbJukebox/labels/adminmode');
		$formConfig->addSubmit('$locale/sbSystem/actions/apply');
		
		$formConfig->setValue('votingstyle',			Registry::getValue('sb.jukebox.voting.style'));
		$formConfig->setValue('minstars',				0-Registry::getValue('sb.jukebox.voting.scale.min'));
		$formConfig->setValue('maxstars',				Registry::getValue('sb.jukebox.voting.scale.max'));
		$formConfig->setValue('latestalbums',			Registry::getValue('sb.jukebox.latestalbums.amount.default'));
		$formConfig->setValue('latestalbumsexpanded',	Registry::getValue('sb.jukebox.latestalbums.amount.expanded'));
		$formConfig->setValue('latestcomments',			Registry::getValue('sb.jukebox.latestcomments.amount.default'));
		$formConfig->setValue('latestcommentsexpanded',	Registry::getValue('sb.jukebox.latestcomments.amount.expanded'));
		$formConfig->setValue('charts',					Registry::getValue('sb.jukebox.charts.amount.default'));
		$formConfig->setValue('chartsexpanded',			Registry::getValue('sb.jukebox.charts.amount.expanded'));
		$formConfig->setValue('adminmode',				Registry::getValue('sb.jukebox.adminmode.enabled'));
		
		return ($formConfig);
		
	}
	
	
}

?>