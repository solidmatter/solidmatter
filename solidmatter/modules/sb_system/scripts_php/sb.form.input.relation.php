<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.form.input.select');

//------------------------------------------------------------------------------
/**
*/
class sbInput_relation extends sbInput_select {
	
	protected $sType = 'relation';
	
	protected $aConfig = array(
		'required' => 'TRUE',
		'url' => '',
		'minchars' => 3
	);
	
	//--------------------------------------------------------------------------
	/**
	* TODO: implement nodetype, and duplicate checking
	* @param 
	* @return 
	*/
	public function checkInput() {
		
//		if (mb_strlen($this->mValue) < $this->aConfig['minlength']) {
//			$this->sErrorLabel = '$locale/sbSystem/formerrors/string_too_short';
//		}
//		if (mb_strlen($this->mValue) > $this->aConfig['maxlength']) {
//			$this->sErrorLabel = '$locale/sbSystem/formerrors/string_too_long';
//		}
//		if (mb_strlen($this->mValue) == 0 && $this->aConfig['required'] == 'TRUE') {
//			$this->sErrorLabel = '$locale/sbSystem/formerrors/not_null';
//		}
//		
//		$this->additionalChecks();
		
		if ($this->sErrorLabel == '') {
			return (TRUE);
		} else {
			return (FALSE);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function additionalChecks() { }
	
	
}




?>