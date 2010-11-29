<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbCR]
* @author	()((() [Oliver M�ller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* Base class for all views.
*/
abstract class sbView {
	
	protected $bUseLocale = TRUE;
	protected $bLoginRequired = TRUE;
	// TODO: move this information to the database/repository?
	protected $aRequiredAuthorisations = array();
	
	protected $nodeSubject = NULL;
	protected $crSession = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* the constructor expects the 
	* @param sbNode the subject node on which this view acts
	*/
	public function __construct($nodeSubject) {
		
		$this->nodeSubject = $nodeSubject;
		$this->crSession = $nodeSubject->getSession();
		
		$this->__init();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __init() {
		if (__CLASS__ != 'sbView') {
			parent::__init();
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Executes an action of this view on the subject node. 
	* @param string the action id
	*/
	public function execute($sAction) {
		throw new sbException('action "'.$sAction.'" not supported');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*public function getSubject() {
		return ($this->nodeSubject->getSubject());
	}
	
	//--------------------------------------------------------------------------
	/**
	* Checks if this view requires the user to be logged in.
	* @return boolean true if this view requires the user to be logged in, false otherwise
	*/
	public function requiresLogin() {
		return ($this->bLoginRequired);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Checks if this view uses 
	* @param 
	* @return 
	*/
	/*public function usesLocale() {
		return ($this->bUseLocale);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function requireParam($sName) {
		
		global $_REQUEST;
		$mValue = $_REQUEST->getParam($sName);
		
		if ($mValue === NULL) {
			throw new MissingParameterException('parameter missing: '.$sName);
		}
		
		return ($mValue);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function requireAuthorisation($sAuthorisation, $nodeSubject = null) {
		
		if ($nodeSubject == null) {
			$nodeSubject = $this->nodeSubject;
		}
		
		if (!User::isAuthorised($sAuthorisation, $nodeSubject)) {
			throw new SecurityException('you are not granted the necessary authorisation: '.$sAuthorisation.' on '.$nodeSubject->getProperty('label'));
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkRequirements($sAction) {
		
		if (!isset($this->aRequiredAuthorisations[$sAction])) {
			return;
		}
		
		foreach ($this->aRequiredAuthorisations[$sAction] as $sRequirement) {
			$this->requireAuthorisation($sRequirement, $this->nodeSubject);
		}
		
	}		
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getParam($sName) {
		global $_REQUEST;
		return ($_REQUEST->getParam($sName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function logEvent($eType, $sUID, $sText = NULL, $sSubjectUUID = NULL) {
		$sModule = $this->nodeSubject->getModule();
		if ($sSubjectUUID == NULL) {
			$sSubjectUUID = $this->nodeSubject->getProperty('jcr:uuid');
		}
		if ($sText == NULL) {
			$sText = 'no additional info provided';
		}
		System::logEvent($eType, $sModule, $sUID, $sText, $sSubjectUUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getNode($sQuery) {
		return ($this->crSession->getNode($sQuery));
	}
	
}

?>