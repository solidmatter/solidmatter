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
		
		// TODO: is the encoding always the same, system-wide?
		$this->aInfo['encoding'] = System::getFilesystemEncoding();
		
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
		
		if (!file_exists($sRelPath)) {
			throw new Exception('filesystem object "'.$sRelPath.'" does not exist');
		}
		
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
	* Returns the fully qualified path of the object, including the object name itself. 
	* @return string the absulute object path
	*/
	public function getAbsPath() {
		return ($this->aInfo['abs_path']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the relative path of the object, including the object name itself.
	* If a path is given as argument, the objects path relative to this path is 
	* returned, otherwise the relative path that applied when this class was
	* instanciated.
	* @param string absolute path that can be used for calculating a relative path
	* @return string the relative path
	*/
	public function getRelPath($sRelativeToPath = NULL) {
		//echo $sRelativeToPath;
		if ($sRelativeToPath !== NULL) {
			$sRelativeToPath = $this->normalize($sRelativeToPath);
			if (mb_strpos($this->aInfo['abs_path'], $sRelativeToPath) != 0 ) {
				throw new sbException('filesystem object path ('.$this->aInfo['abs_path'].') is not within root path ('.$sRelativeToPath.')');	
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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getParentDir() {
		if (is_file($this->getAbsPath())) {
			$aPathInfo = pathinfo($this->getAbsPath());
			return (normalize_path($aPathInfo['dirname']));
		} elseif (is_dir($this->getAbsPath())) {
			return (normalize_path($this->getAbsPath().'..'));
		} else {
			die('what kind of object is this if it\'s not a directory nor a file?');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function rename($sNewName) {
		if (is_file($this->getAbsPath()) || is_dir($this->getAbsPath())) {
			if (rename($this->getAbsPath(), $this->getParentDir().$sNewName)) {
				$this->aInfo['object_name'] = $sNewName;
				return (TRUE);
			} else {
				die ('could not rename '.$this->getAbsPath());
			}
		} else {
			die('what kind of object is this if it\'s not a directory nor a file?');
		}
	}
	
}

?>