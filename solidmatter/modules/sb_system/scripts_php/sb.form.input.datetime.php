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
import('sb.tools.forms');

//------------------------------------------------------------------------------
/**
*/
class sbInput_datetime extends sbInput_string {
	
	protected $sType = 'datetime';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (!is_mysqldatetime($this->mValue)) {
			$this->sErrorLabel = '$locale/system/formerrors/no_datetime';
		}
		
		return (parent::checkInput());
		
	}
	
}

?>