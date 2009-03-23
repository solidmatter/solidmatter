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
class sbInput_select extends sbInput {
	
	protected $sType = 'select';
	
	protected $aConfig = array(
		'size' => '1',
		'multiple' => 'FALSE',
		'maxselected' => 'unlimited'
	);
	
	protected $aOptions = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setConfig($sConfig, $sValue) {
		
		if ($sConfig == 'options') {
			$aOptions = array();
			foreach (explode('|', $sValue) as $sOption) {
				$aOptions[$sOption]	= $sOption;
			}
			$this->setOptions($aOptions);
		} else {
			parent::setConfig($sConfig, $sValue);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (!isset($this->aOptions[$this->mValue])) {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/not_in_options';
		}
		
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
	public function setOptions($aValues) {
		$this->aOptions = $aValues;
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
		$elemInput->setAttribute('value', $this->mValue);
		$elemInput->setAttribute('label', $this->sLabelPath);
		foreach ($this->aConfig as $sConfig => $sValue) {
			$elemInput->setAttribute($sConfig, $sValue);
		}
		foreach ($this->aOptions as $sValue => $sLabel) {
			$elemOption	= $this->domForm->createElement('option');
			$elemOption->setAttribute('value', $sValue);
			// TODO: think of another way to seperate labels and text options?
			if (substr($sLabel, 0, 1) == '$') {
				$elemOption->setAttribute('label', $sLabel);
			} else {
				$elemOption->setAttribute('text', $sLabel);	
			}
			$elemInput->appendChild($elemOption);
		}
		if ($this->sErrorLabel != '') {
			$elemInput->setAttribute('errorlabel', $this->sErrorLabel);	
		}
		
		return ($elemInput);
		
	}
	
}




?>