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
class sbInput_fileupload extends sbInput {
	
	protected $sType = 'fileupload';
	
	protected $aConfig = array(
		'required' => 'FALSE',
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		parent::checkInput();
		
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
	public function getElement() {
		$elemInput = $this->domForm->createElement('sbinput');
		$elemInput->setAttribute('name', $this->sName);
		$elemInput->setAttribute('type', $this->sType);
		//$elemInput->setAttribute('value', $this->mValue);
		$elemInput->setAttribute('label', $this->sLabelPath);
		foreach ($this->aConfig as $sConfig => $sValue) {
			$elemInput->setAttribute($sConfig, $sValue);
		}
		if ($this->sErrorLabel != '') {
			$elemInput->setAttribute('errorlabel', $this->sErrorLabel);	
		}
		
		return ($elemInput);
		
	}
	
}




?>