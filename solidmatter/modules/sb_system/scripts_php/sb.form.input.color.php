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
class sbInput_color extends sbInput {
	
	protected $sType = 'color';
	
	protected $aConfig = array(
		'style' => 'hex',
		'required' => 'TRUE'
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (!preg_match('/^[0-9a-f]{6}$/i', $this->mValue)) {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/no_hexcolor';
		}
		
		if ($this->sErrorLabel == '') {
			return (TRUE);
		} else {
			return (FALSE);
		}
		
	}
	
}

?>