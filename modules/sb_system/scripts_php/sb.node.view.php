<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbCR]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
abstract class sbView {
	
	protected $bUseLocale = TRUE;
	protected $bLoginRequired = TRUE;
	
	protected $nodeSubject = NULL;
	protected $crSession = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
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
	* 
	* @param 
	* @return 
	*/
	public abstract function execute($sAction);
	
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
	* 
	* @param 
	* @return 
	*/
	public function requiresLogin() {
		return ($this->bLoginRequired);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function usesLocale() {
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