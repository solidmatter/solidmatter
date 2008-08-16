<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class ResponseFactory {
	
	private static $globalResponse = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getInstance($sType) {
		
		switch ($sType) {
			
			case 'global':
				if (self::$globalResponse === NULL) {
					self::initGlobalResponse();
				}
				return (self::$globalResponse);
				//break;
				
			default:
				throw new Exception();
				break;
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private static function initGlobalResponse() {
		self::$globalResponse = new sbDOMResponse();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function createElement($sNodeName, $sValue = NULL) {
		
		if (self::$globalResponse === NULL) {
			self::initGlobalResponse();
		}
		
		if ($sValue == NULL) {
			return (self::$globalResponse->createElement($sNodeName));
		} else {
			return (self::$globalResponse->createElement($sNodeName, $sValue));
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function createElementNS($sNamespaceURI, $sNodeName, $sValue = NULL) {
		
		if (self::$globalResponse === NULL) {
			self::initGlobalResponse();
		}
		
		if ($sValue == NULL) {
			return (self::$globalResponse->createElementNS($sNamespaceURI, $sNodeName));
		} else {
			return (self::$globalResponse->createElementNS($sNamespaceURI, $sNodeName, $sValue));
		}
		
	}
	
}

?>