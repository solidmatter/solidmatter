<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbCR_PropertyDefinitionCache implements Iterator {
	
	private $aPropertyDefinitions = array();
	private $aPropertyStorageInfo = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($aPropertyData = NULL) {
		//var_dumpp($aPropertyData);
		if ($aPropertyData != NULL) {
			$this->fill($aPropertyData['definitions'], $aPropertyData['storage']);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function fill($aPropertyDefinitions = NULL, $aPropertyStorageInfo = NULL) {
		if (!is_array($aPropertyDefinitions)) {
			throw new RepositoryException('no array');
		} else {
			$this->aPropertyDefinitions = $aPropertyDefinitions;
			if ($aPropertyStorageInfo == NULL) {
				foreach ($this->aPropertyDefinitions as $aDetails) {
					$this->aPropertyStorageInfo[$aDetails['e_storagetype']] = TRUE;
				}
			} else {
				$this->aPropertyStorageInfo = $aPropertyStorageInfo;	
			}
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function rewind() {
		reset($this->aPropertyDefinitions);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function current() {
		return (current($this->aPropertyDefinitions));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function key() {
		return (key($this->aPropertyDefinitions));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function next() {
		return (next($this->aPropertyDefinitions));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function valid() {
		if (is_null(key($this->aPropertyDefinitions))) {
			reset($this->aPropertyDefinitions);
			return (FALSE);
		}
		return (TRUE);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function usesStorage($sStorageType) {
		return ($this->aPropertyStorageInfo[$sStorageType]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasProperty($sName) {
		return (isset($this->aPropertyDefinitions[$sName]));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getStorageType($sName) {
		return ($this->aPropertyDefinitions[$sName]['e_storagetype']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function isProtected($sName, $bNewNode) {
		if (!$bNewNode) {
			if ($this->aPropertyDefinitions[$sName]['b_protected'] == 'FALSE') {
				return (FALSE);
			}
		} else {
			if ($this->aPropertyDefinitions[$sName]['b_protectedoncreation'] == 'FALSE') {
				return (FALSE);
			}
		}
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDefaultValue($sName) {
		return ($this->aPropertyDefinitions[$sName]['s_defaultvalues']);
	}
	
}

?>