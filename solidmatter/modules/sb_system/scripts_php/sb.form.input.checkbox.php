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
class sbInput_checkbox extends sbInput {
	
	protected $sType = 'checkbox';
	
	protected $aConfig = array(
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function recieveInput() {
		global $_REQUEST;
		$this->mValue = 'FALSE';
		if($_REQUEST->getParam($this->sName) != NULL) {
			$this->mValue = 'TRUE';
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		return (TRUE);
	}
	
}




?>