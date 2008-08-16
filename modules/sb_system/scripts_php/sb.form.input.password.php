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
class sbInput_password extends sbInput_string {
	
	protected $sType = 'password';
	
	protected $aConfig = array(
		'size' => '30',
		'minlength' => '4',
		'maxlength' => '40',
		'required' => 'FALSE',
		'trim' => 'TRUE',
		'style' => 'normal'
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if ($this->aConfig['style'] != 'normal') {
			// TODO: implement different Styles
		}
		
		return (parent::checkInput());
		
	}
	
}




?>