<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_modules_overview extends sbView {
	
	private $aQueries = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		$stmtModules = $this->crSession->prepareKnown('sbSystem/modules/getInfo');
		$stmtModules->execute();
		$_RESPONSE->addData($stmtModules->fetchDOM('modules'));
		
		$domInfo = new DOMDocument();
		$domInfo->load('modules/sb_system/properties.xml');
		$_RESPONSE->addData($domInfo);
		
		return (NULL);
		
	}
	
}


?>