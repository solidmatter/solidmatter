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
*/
interface sbCache {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	//public function storeData($sKey, $mData, $sSubject = NULL, $sModifier = NULL);
	public function storeData($sKey, $mData);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	//public function loadData($sKey, $sSubject = NULL, $sModifier = NULL);
	public function loadData($sKey);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function exists($sKey);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clear($sKey = '');
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInfo();
	
}

?>