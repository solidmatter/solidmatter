<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.strings.conversion');
import('sb.form.input.string');

//------------------------------------------------------------------------------
/**
*/
class sbInput_urlsafe extends sbInput_string {
	
	protected $sType = 'urlsafe';
	
	protected $aConfig = array(
		'size' => '30',
		'minlength' => '0',
		'maxlength' => '40',
		'required' => 'FALSE',
		'trim' => 'TRUE',
		'regex' => '',
		'siteformat' => 'FALSE',
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if ($this->aConfig['siteformat'] == 'TRUE') {
			if (!preg_match('/^[a-z0-9_\.]+::[a-z0-9_\.]+$/', $this->mValue)) {
				$this->sErrorLabel = '$locale/system/formerrors/no_siteformat';
			}
		} else {
			if (!preg_match('/^[a-z0-9_\.]+$/', $this->mValue)) {
				$this->sErrorLabel = '$locale/system/formerrors/not_urlsafe';
			}
		}
		
		return (parent::checkInput());
		
	}
	
	
}




?>