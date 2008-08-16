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
class sbInput_domain extends sbInput_string {
	
	protected $sType = 'domain';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (!preg_match('/^[a-z][0-9a-z]*\.([-0-9a-z]+\.)*([a-z]){2,8}$/', $this->mValue)) {
			$this->sErrorLabel = '$locale/system/formerrors/no_domain';
		}
		
		return (parent::checkInput());
		
	}
	
}




?>