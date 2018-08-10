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
import('sb.tools.mime');

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
	public function __construct(string $sRelPath = NULL) {
		parent::__construct($sRelPath);
		$this->aInfo['abs_path'] = substr($this->aInfo['abs_path'], 0, strlen($this->aInfo['abs_path'])-1);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getElement(string $sContainerName, bool $bIncludeDirs = FALSE) : DOMElement {
		
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
	public function getContents() : string {
		return (file_get_contents($this->aInfo['abs_path']));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSize() : string {
		return (filesize($this->aInfo['abs_path']));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getMimetype() : string {
		return (get_mimetype($this->aInfo['abs_path']));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function delete() : bool {
		return (unlink($this->aInfo['abs_path']));
	}
	
}

?>