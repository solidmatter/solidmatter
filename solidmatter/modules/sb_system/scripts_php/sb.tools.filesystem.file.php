<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');
import('sb.tools.filesystem.object');

//------------------------------------------------------------------------------
/**
*/
class sbFile extends sbFilesystemObject {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sRelPath = NULL) {
		parent::__construct($sRelPath);
		$this->aInfo['abs_path'] = substr($this->aInfo['abs_path'], 0, strlen($this->aInfo['abs_path'])-1);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getElement($sContainerName, $bIncludeDirs = FALSE) {
		
		$domFiles = new DOMDocument();
		$elemContainer = $domFiles->createElement($sContainerName);
		foreach ($this->aInfo as $sKey => $sValue) {
			$elemContainer->setAttribute($sKey, $sValue);
		}
		
		return ($elemContainer);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getContents() {
		$sFileName = $this->aInfo['abs_path'];
		//var_dumpp($sFileName);
		return (file_get_contents($sFileName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function delete() {
		$sFileName = $this->aInfo['abs_path'];
		//var_dumpp($sFileName);
		return (unlink($sFileName));
	}
	
}

?>