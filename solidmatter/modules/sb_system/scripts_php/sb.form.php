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
	
	private $bDisabled = FALSE;
	
	private $sID;
	private $sLabel;
	private $sAction;
	/**
	* array of additional attributes (name => value) to include in the generated DOM element
	* @var array 
	*/
	private $aAttributes = array(); #PHP7 changed to "private $aAttributes = array();" instead of "private $aAttributes;", otherwise will give a warning
	
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
		$this->aInputs[$sName] = $ifInput;
		return ($ifInput);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function removeInput($sName) {
		unset($this->aInputs[$sName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addGroup($sName) {
		throw new LazyBastardException('not supported yet');
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
		throw new LazyBastardException('not supported yet');
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
	public function hasError($sName = NULL) {

		if ($sName != NULL) { // check only the given input
			if (!isset($this->aInputs[$sName])) {
				throw new InputNotFoundException($sName);	
			}
			return ($this->aInputs[$sName]->hasError());
		} else { // check all inputs and form itself
			foreach ($this->aInputs as $ifInput) {
				if ($ifInput->hasError()) {
					return (TRUE);
				}
			}
			if ($this->sErrorLabel != '') {
				return (TRUE);
			}
			return (FALSE);
		}
		
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
	public function &getInputs() {
		return ($this->aInputs);
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
	public function setAttribute($sName, $sValue) {
		$aForbiddenAttributes = array('id', 'action', 'label', 'disabled', 'errorlabel');
		if (in_array($sName, $this->aAttributes)) {
			throw new sbException('attribute "'.$sName.'" can not be set');
		}
		$this->aAttributes[$sName] = htmlspecialchars($sValue);
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
		if ($this->bDisabled) {
			$elemForm->setAttribute('disabled', 'TRUE');
		}
		if ($this->sErrorLabel != '') {
			$elemForm->setAttribute('errorlabel', $this->sErrorLabel);
		}
		foreach ($this->aAttributes as $sName => $sValue) {
			$elemForm->setAttribute($sName, htmlspecialchars($sValue));
		}
		foreach ($this->aInputs as $ifInput) {
			$elemForm->appendChild($ifInput->getElement());
		}
		foreach ($this->aSubmits as $sValue => $sLabelPath) {
			$elemSubmit = $this->createElement('submit');
			$elemSubmit->setAttribute('value', $sValue);
			$elemSubmit->setAttribute('label', $sLabelPath);
			if ($this->bDisabled) {
				$elemSubmit->setAttribute('disabled', 'TRUE');
			}
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
	public function disableAll() {
		foreach ($this->aInputs as $sName => $unused) {
			$this->aInputs[$sName]->disable();
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function disableForm() {
		$this->bDisabled = TRUE;
		$this->disableAll();
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