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
class sbInput_multifileupload extends sbInput {
	
	protected $sType = 'multifileupload';
	
	protected $aConfig = array(
		'maxfiles' => '5',
		'required' => 'FALSE',
	);
	
	protected $aSelectValues = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		/*if (!in_array($this->mValue)) {
			$this->sErrorLabel = '$locale/system/formerrors/not_in_options';
		}*/
		
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
	public function setSelectValues($aValues) {
		$this->aSelectValues = $aValues;
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