<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------



//------------------------------------------------------------------------------
/** 
*/
class APCCache implements sbCache {
	
	protected $sPrefix = '';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sPrefix = NULL) {
		if ($sPrefix != NULL) {
			$this->sPrefix = $sPrefix;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeData($sKey, $mData) {
		return (apc_store($this->sPrefix.$sKey, $mData));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadData($sKey) {
		return (apc_fetch($this->sPrefix.$sKey));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function exists($sKey) {
		if (apc_fetch($this->sPrefix.$sKey) != FALSE) {
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
	public function clear($sKey = NULL) {
		if ($sKey != NULL) {
			apc_delete($this->sPrefix.$sKey);
		} else {
			apc_clear_cache('user');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInfo() {
		
		$aInfo['global'] = apc_cache_info();
		$aInfo['user'] = apc_cache_info('user');
		
		return ($aInfo);
	}
	
}

?>