<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.dom.document');

//------------------------------------------------------------------------------
/**
* 
*/
class sbDOMForm extends sbDOMDocument {
	
	const RULE_ISTRUE = 1;
	const RULE_MATCHREGEX = 2;
	const RULE_NOTEMPTY = 3;
	
	private $aInputs;
	private $aSubmits;
	
	private $sID;
	private $sLabel;
	private $sAction;
	
	private $crSession = NULL;
	
	private $sErrorLabel = '';
	
	//--------------------------------------------------------------------------
	//##########################################################################
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sID, $sLabel, $sAction, $crSession) {
		parent::__construct();
		$this->sID = $sID;
		$this->sLabel = $sLabel;
		$this->sAction = $sAction;
		$this->crSession = $crSession;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setAction($sAction) {
		$this->sAction = $sAction;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Adds an input field to the form
	* @param string the configuration string (see input classes for details)
	* @param string a XPath to the localized string
	* @param string INACTIVE
	* @return 
	*/
	public function addInput($sConfig, $sLabelPath = '', $sGroup = NULL) {
		$aConfig = explode(';', $sConfig);
		$sName = $aConfig[0];
		//unset($aConfig[0]);
		//echo ($sConfig);
		$ifInput = InputFactory::getInstance($sConfig, $this);
		$ifInput->setLabelPath($sLabelPath);
		$this->aInputs[$sName] = &$ifInput;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addGroup($sName) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addSubmit($sLabelPath, $sValue='submit') {
		$this->aSubmits[$sValue] = $sLabelPath;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function bindInputByRule($sBoundInput, $sObservedInput, $eRule, $aParams = NULL) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function recieveInputs() {
		foreach ($this->aInputs as $ifInput) {
			$ifInput->recieveInput();
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInputs() {
		$bValid = TRUE;
		foreach ($this->aInputs as $sName => $ifInput) {
			$bValid = $ifInput->checkInput() && $bValid;
			//echo $sName.'=';
			//var_dump($bValid);
		}
		return ($bValid);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setValue($sName, $mValue) {
		$this->aInputs[$sName]->setValue($mValue);	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setError($sName, $sErrorLabel) {
		$this->aInputs[$sName]->setError($sErrorLabel);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getError($sName) {
		return ($this->aInputs[$sName]->getError());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setConfig($sName, $sConfig, $sValue) {
		$this->aInputs[$sName]->setConfig($sConfig, $sValue);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setOptions($sName, $aOptions) {
		$this->aInputs[$sName]->setOptions($aOptions);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setFormError($sErrorLabel) {
		$this->sErrorLabel = $sErrorLabel;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getValues() {
		$aValues = array();
		foreach ($this->aInputs as $sName => $ifInput) {
			$aValues[$sName] = $ifInput->getValue();
		}
		return ($aValues);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasError($sName) {
		if (!isset($this->aInputs[$sName])) {
			throw new InputNotFoundException($sName);	
		}
		return ($this->aInputs[$sName]->hasError());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInput($sName) {
		if (!isset($this->aInputs[$sName])) {
			throw new InputNotFoundException($sName);	
		}
		return ($this->aInputs[$sName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getValue($sName) {
		if (!isset($this->aInputs[$sName])) {
			throw new InputNotFoundException($sName);	
		}
		return ($this->aInputs[$sName]->getValue());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function saveDOM() {
		$elemForm = $this->createElement('sbform');
		//$elemForm = ResponseFactory::createElement('sbform');
		$elemForm->setAttribute('id', $this->sID);
		$elemForm->setAttribute('action', $this->sAction);
		$elemForm->setAttribute('label', $this->sLabel);
		if ($this->sErrorLabel != '') {
			$elemForm->setAttribute('errorlabel', $this->sErrorLabel);
		}
		foreach ($this->aInputs as $ifInput) {
			$elemForm->appendChild($ifInput->getElement());
		}
		foreach ($this->aSubmits as $sValue => $sLabelPath) {
			$elemSubmit = $this->createElement('submit');
			$elemSubmit->setAttribute('value', $sValue);
			$elemSubmit->setAttribute('label', $sLabelPath);
			$elemForm->appendChild($elemSubmit);
		}
		$this->appendChild($elemForm);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function disable($sName) {
		$this->aInputs[$sName]->disable();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSession() {
		return ($this->crSession);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function debug() {
		$this->saveDOM();
		debug('DOMForm: '.$this->sID, $this);
		
		
	}
	
}

?>