<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbInput_text extends sbInput {
	
	protected $sType = 'text';
	
	protected $aConfig = array(
		'columns' => '70',
		'rows' => '6',
		'minlength' => '0',
		'maxlength' => '10000',
		'required' => 'FALSE',
		'trim' => 'TRUE',
		'regex' => ''
	);
	
	public function checkInput() {
		
		if (strlen($this->mValue) < $this->aConfig['minlength']) {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/string_too_short';
		}
		if (strlen($this->mValue) > $this->aConfig['maxlength']) {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/string_too_long';
		}
		if (strlen($this->mValue) == 0 && $this->aConfig['required'] == 'TRUE') {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/not_null';
		}
		
		if ($this->sErrorLabel == '') {
			return (TRUE);
		} else {
			return (FALSE);
		}
		
	}	
	
}

?>