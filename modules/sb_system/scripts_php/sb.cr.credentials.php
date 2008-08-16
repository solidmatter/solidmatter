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
class sbCR_Credentials {
	
	private $sUserID;
	private $sPassword;
	private $aAttributes = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sUserID, $sPassword) {
		$this->sUserID = $sUserID;
		$this->sPassword = $sPassword;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the user ID.
	* @param 
	* @return 
	*/
	public function getUserID() {
		return($this->sUserID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the user password.
	* @return 
	*/
	public function getPassword() {
		return($this->sPassword);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Stores an attribute in this credentials instance.
	* @param 
	* @return 
	*/
	public function setAttribute($sName, $sValue) {
		$this->aAttributes[$sName] = $sValue;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the value of the named attribute as an Object, or null if no 
	* attribute of the given name exists.
	* @param 
	* @return 
	*/
	public function getAttribute($sName) {
		if (isset($this->aAttributes[$sName])) {
			return($this->aAttributes[$sName]);
		}
		return (NULL);
	}
	
	//--------------------------------------------------------------------------
	/**
	*  Removes an attribute from this credentials instance.
	* @param 
	* @return 
	*/
	public function removeAttribute($sName) {
		unset($this->aAttributes[$sName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the names of the attributes available to this credentials 
	* instance.
	* @param 
	* @return 
	*/
	public function getAttributeNames() {
		return(array_keys($this->aAttributes));
	}
	
}

?>