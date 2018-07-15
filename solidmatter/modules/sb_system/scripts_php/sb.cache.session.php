<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sb_system]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*	Provides unified methods to store data in the current session.
*	Implements the "sbCache Interface", and thus can be replaced with other
*	means of storing imformation easily. A useful capability is that it can be 
*	derived from while setting a different prefix property, so that information
*	of differend types can be stored in the session separately.
*/
class SessionCache implements sbCache {
	
	/**
	* the prefix to be used when storing data. In derived classes this property
	* should be overloaded with a sensible string.
	*/
	protected $sPrefix = '';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct(string $sPrefix = NULL) {
		if ($sPrefix != NULL) {
			$this->sPrefix = $sPrefix;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Stores data under a specific key.
	* @param string the (unique) key under which the data should be stored
	* @param multiple the data to be stored
	* @return 
	*/
	public function storeData(string $sKey, $mData) {
		
		sbSession::$aData['cache'][$this->sPrefix.$sKey] = serialize($mData);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadData(string $sKey) {
		
		$mData = NULL;
		if (isset(sbSession::$aData['cache'][$this->sPrefix.$sKey])) {
			$mData = unserialize(sbSession::$aData['cache'][$this->sPrefix.$sKey]);
		}
		return ($mData);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function exists(string $sKey) {
		
		if (isset(sbSession::$aData['cache'][$this->sPrefix.$sKey])) {
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
	public function clear(string $sKey = '') {
		
		if ($sKey == '') {
			unset(sbSession::$aData['cache']);
		} else {
			foreach	(array_keys(sbSession::$aData['cache']) as $sCacheKey) {
				if (strpos($this->sPrefix.$sKey, $this->sPrefix.$sCacheKey) === 0) {
					unset(sbSession::$aData['cache'][$this->sPrefix.$sCacheKey]);
				}
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInfo() {
		return (array('info' => 'not yet implemented'));	
	}
	
}

?>