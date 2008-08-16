<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.form.input.string');

//------------------------------------------------------------------------------
/**
*/
class sbInput_url extends sbInput_string {
	
	protected $sType = 'url';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (!preg_match('/^([a-z]+://)?[A-Z0-9.-]+\.[A-Z]{2,10}/?[A-Z0-9._?~=/-]*$/', $this->mValue)) {
			$this->sErrorLabel = '$locale/system/formerrors/no_url';
		}
		
		return (parent::checkInput());
		
	}
	
}

?>