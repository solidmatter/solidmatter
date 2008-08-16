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
class DEACIVATEDDBFactory {
	
	private static $dbSystem = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getInstance($sType) {
		
		switch ($sType) {
			
			case 'system':
				if (self::$dbSystem === NULL) {
					import('sb.pdo.sysdb');
					self::$dbSystem = new sbPDOSystem();	
				}
				return (self::$dbSystem);
				//break;
				
			default:
				throw new sbException(__CLASS__.': instance type not recognized ('.$sType.')');
				break;
			
		}	
		
	}
	
}

?>