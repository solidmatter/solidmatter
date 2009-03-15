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
class sbInput_hidden extends sbInput_string {
	
	protected $sType = 'hidden';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		// input validation needs to be done on application level for now (what data is processed here?)
		parent::checkInput();
	}
	
}

?>