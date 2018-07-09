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
abstract class sbInput {
	
	protected $crSession = NULL;
	
	protected $sName = '';
	protected $sLabelPath = '';
	protected $domForm = NULL;
	protected $sType = '';
	protected $sHelpPath = '';
	protected $sHelpLink = '';
	protected $sHelpText = '';
	
	/**
	* array of additional attributes (name => value) to include in the generated DOM element
	* @var array 
	*/
	protected $aAttributes = array(); #PHP7 changed to "private $aAttributes = array();" instead of "private $aAttributes;", otherwise will give a warning
	
	protected $aConfig = array();
	protected $mValue = NULL;
	protected $sError = '';
	protected $sErrorLabel = '';
	protected $sErrorHint = '';
	protected $bDisabled = FALSE;
	
	//--------------------------------------------------------------------------
	//##########################################################################
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sName, $domForm, $aConfig = array()) {
		$this->sName = $sName;
		$this->domForm = $domForm;
		$this->crSession = $domForm->getSession();
		foreach ($aConfig as $sConfig) {
			// for empty config behind last semicolon
			if (trim($sConfig) == '') {
				continue;
			}
			list($sConfig, $sValue) = explode('=', $sConfig);
			$this->setConfig($sConfig, $sValue);
		}
		$this->initConfig();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setConfig($sConfig, $sValue) {
		
		if ($sConfig == 'default') {
			$this->mValue = $sValue;
		} elseif ($sConfig == 'disabled') {
			if ($sValue == 'true') {
				$this->disable();
			}
		} else {
			if (!isset($this->aConfig[$sConfig])) {
				throw new sbException('config option not supported: '.$sConfig);
			}
			$this->aConfig[$sConfig] = $sValue;
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function initConfig() { }
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setLabelPath($sPath) {
		$this->sLabelPath = $sPath;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setAttribute($sName, $sValue) {
		$aForbiddenAttributes = array('name', 'type', 'value', 'label', 'errorlabel', 'errorhint');
		if (in_array($sName, $aForbiddenAttributes)) {
			throw new sbException('attribute "'.$sName.'" can not be set, it would override a standard attribute');
		}
		if (isset($this->aConfig[$sName])) {
			throw new sbException('attribute "'.$sName.'" can not be set, it would override a standard config option');
		}
		$this->aAttributes[$sName] = htmlspecialchars($sValue);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAttribute($sName) {
		if (!isset($this->aAttributes[$sName])) {
			throw new sbException('attribute "'.$sName.'" is not set');
		}
		return ($this->aAttributes[$sName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function recieveInput() {
		
		// don't do anything if disabled (e.g. Firefox will submit nothing for disabled form elements)
		if ($this->bDisabled) {
			return;	
		}
		
		global $_REQUEST;
		$this->mValue = NULL;
		if($_REQUEST->getParam($this->sName) != NULL) {
			$this->mValue = $_REQUEST->getParam($this->sName);
		}
		// TODO: strange backwards dependency, remove
		if (isset($this->aConfig['trim']) && $this->aConfig['trim'] == 'TRUE') {
			$this->mValue = trim($this->mValue);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public abstract function checkInput();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getValue() {
		return ($this->mValue);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setValue($mValue) {
		$this->mValue = $mValue;
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
		if ($this->bDisabled) {
			$elemInput->setAttribute('disabled', 'TRUE');
		}
		foreach ($this->aConfig as $sConfig => $sValue) {
			$elemInput->setAttribute($sConfig, $sValue);
		}
		foreach ($this->aAttributes as $sName => $sValue) {
			$elemInput->setAttribute($sName, htmlspecialchars($sValue));
		}
		if ($this->sErrorLabel != '') {
			$elemInput->setAttribute('errorlabel', $this->sErrorLabel);	
		}
		if ($this->sErrorHint != '') {
			$elemInput->setAttribute('errorhint', $this->sErrorHint);
		}
		
		return ($elemInput);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setElement() {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setError($sErrorLabel) {
		$this->sErrorLabel = $sErrorLabel;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getError() {
		return ($this->sErrorLabel);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasError() {
		if ($this->sErrorLabel != '') {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function disable() {
		$this->bDisabled = TRUE;
	}
	
}



?>