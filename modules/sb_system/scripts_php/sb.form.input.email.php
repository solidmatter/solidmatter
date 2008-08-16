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
class sbInput_email extends sbInput_string {
	
	protected $sType = 'email';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (!preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,8}$/i', $this->mValue)) {
			$this->sErrorLabel = '$locale/system/formerrors/no_email';
		}
		
		return (parent::checkInput());
		
	}
	
}

?>