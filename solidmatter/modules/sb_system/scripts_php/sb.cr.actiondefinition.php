<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/** TODO: complete separate this from sbCR and put into sbSystem?
*/
class sbCR_ActionDefinition {
	
	protected $aActionInformation = array(
		'NodeTypeName' => '',
		'ViewName' => '',
		'ActionName' => '',
		'Class' => '',
		'ClassFile' => '',
		'Priority' => 0,
		'Outputtype' => '',
		'Stylesheet' => '',
		'Mimetype' => '',
		'UseLocale' => FALSE,
		'IsRecallable' => FALSE,
	);
	
	protected $crRepositoryStructure = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crRepositoryStructure, $sNodeTypeName, $sViewName, $sActionName, $sClass, $sClassFile, $iPriority, $sOutputtype, $sStylesheet, $sMimetype, $bUseLocale, $bIsRecallable) {
		
		// store basic info
		$this->crRepositoryStructure = $crRepositoryStructure;
		$this->aActionInformation['NodeTypeName'] = $sNodeTypeName;
		$this->aActionInformation['ViewName'] = $sViewName;
		$this->aActionInformation['ActionName'] = $sActionName;
		$this->aActionInformation['Class'] = $sClass;
		$this->aActionInformation['ClassFile'] = $sClassFile;
		$this->aActionInformation['Priority'] = $iPriority;
		$this->aActionInformation['Outputtype'] = $sOutputtype;
		$this->aActionInformation['Stylesheet'] = $sStylesheet;
		$this->aActionInformation['Mimetype'] = $sMimetype;
		$this->aActionInformation['UseLocale'] = $bUseLocale;
		$this->aActionInformation['IsRecallable'] = $bIsRecallable;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getNodeTypeName() {
		return ($this->aActionInformation['NodeTypeName']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getViewName() {
		return ($this->aActionInformation['ViewName']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getName() {
		return ($this->aActionInformation['ActionName']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getClass() {
		return ($this->aActionInformation['Class']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getClassFile() {
		return ($this->aActionInformation['ClassFile']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getPriority() {
		return ($this->aActionInformation['Priority']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function usesLocale() {
		return ($this->aActionInformation['UseLocale']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function isRecallable() {
		return ($this->aActionInformation['IsRecallable']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getOutputtype() {
		return ($this->aActionInformation['Outputtype']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getStylesheet() {
		return ($this->aActionInformation['Stylesheet']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getMimetype() {
		return ($this->aActionInformation['Mimetype']);
	}
	
}

?>