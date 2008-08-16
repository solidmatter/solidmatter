<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.forms');

//------------------------------------------------------------------------------
/** TODO: implement missing stuff
*/
class sbInput_integer extends sbInput {
	
	protected $sType = 'integer';
	
	protected $aConfig = array(
		'size' => NULL,
		'minlength' => NULL,
		'maxlength' => NULL,
		'minvalue' => 0,
		'maxvalue' => 1000000,
		'required' => 'FALSE',
		'trim' => 'TRUE',
		'regex' => ''
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function initConfig() {
		if ($this->aConfig['minlength'] === NULL) {
			// TODO: doesn't work right with negative values!
			//$this->aConfig['minlength']	= min(count_integerdigits($this->aConfig['minvalue']), count_integerdigits($this->aConfig['maxvalue']));
			$this->aConfig['minlength']	= 0;
		}
		if ($this->aConfig['maxlength'] === NULL) {
			$this->aConfig['maxlength']	= max(count_integerdigits($this->aConfig['minvalue']), count_integerdigits($this->aConfig['maxvalue']));
		}
		if ($this->aConfig['size'] === NULL) {
			$this->aConfig['size'] = $this->aConfig['maxlength'];
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (mb_strlen($this->mValue) == 0 && $this->aConfig['required'] == 'FALSE') {
			return (TRUE);
		}
		
		if (mb_strlen($this->mValue) > $this->aConfig['maxlength']) {
			$this->sErrorLabel = '$locale/system/formerrors/string_too_long';
		}
		if (mb_strlen($this->mValue) < $this->aConfig['minlength']) {
			$this->sErrorLabel = '$locale/system/formerrors/string_too_short';
		}
		if (!is_numeric($this->mValue)) {
			$this->sErrorLabel = '$locale/system/formerrors/not_a_number';
		}
		if (mb_strlen($this->mValue) == 0 && $this->aConfig['required'] == 'TRUE') {
			$this->sErrorLabel = '$locale/system/formerrors/not_null';
		}
		if ($this->mValue < $this->aConfig['minvalue'] || $this->mValue > $this->aConfig['maxvalue']) {
			$this->sErrorLabel = '$locale/system/formerrors/not_in_range';
			$this->sErrorHint = $this->aConfig['minvalue'].' - '.$this->aConfig['maxvalue'];
		}
		
		if ($this->sErrorLabel == '') {
			return (TRUE);
		} else {
			return (FALSE);
		}
		
	}
		
}

?>