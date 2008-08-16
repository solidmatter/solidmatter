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

//------------------------------------------------------------------------------
/**
*/
class sbFilesystemObject {
	
	// information on this directory
	protected $aInfo = array(
		'working_dir' => NULL, // working directory on creating this object
		'encoding' => NULL, // filesystem encoding
		'rel_path' => NULL, // relative path on creating this object (may also be absolute!)
		'abs_path' => NULL, // absolute path to the object incl. name
		'object_name' => NULL,	// just the name
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sRelPath = NULL) {
		
		$this->aInfo['working_dir'] = getcwd();
		
		// TODO: actually check encoding
		if (true) {
			$this->aInfo['encoding'] = 'Windows-1252';
			//$this->aInfo['encoding'] = 'UTF-8';
		}
		
		if ($sRelPath != NULL) {
			$this->__init($sRelPath);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __destruct() {
		chdir($this->aInfo['working_dir']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __init($sRelPath) { 
		
		$this->aInfo['rel_path'] = $sRelPath;
		
		$sAbsPath = $this->normalize($sRelPath);
		$this->aInfo['abs_path'] = $sAbsPath;
		$aPath = explode('/', $sAbsPath);
		$this->aInfo['object_name'] = $aPath[count($aPath)-2];
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInfo() {
		return ($this->aInfo);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getName() {
		return ($this->aInfo['object_name']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getEncoding() {
		return ($this->aInfo['encoding']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAbsPath() {
		return ($this->aInfo['abs_path']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getRelPath($sRelativeToPath = NULL) {
		//echo $sRelativeToPath;
		if ($sRelativeToPath !== NULL) {
			$sRelativeToPath = $this->normalize($sRelativeToPath);
			if (mb_strpos($this->aInfo['abs_path'], $sRelativeToPath) != 0 ) {
				throw new sbException(__CLASS__.': directory path ('.$this->aInfo['abs_path'].') is not within root path ('.$sRelativeToPath.')');	
			}
			return (mb_substr($this->aInfo['abs_path'], mb_strlen($sRelativeToPath)));
		} else {
			return ($this->aInfo['rel_path']);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Normalizes a directory path to be absolute and ending in '/'.
	* Additionally exchanges all '\' with '/'
	* @param 
	* @return 
	*/
	public function normalize($sDirectory) {
		return (normalize_path($sDirectory));
	}
	
}

?>