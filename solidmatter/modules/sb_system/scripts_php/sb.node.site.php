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
class sbNode_site extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function extendForm($formCurrent, $sMode) {
		
		parent::extendForm($formCurrent, $sMode);
		$formCurrent->setConfig('name', 'siteformat', 'TRUE');
		
	}
	
	
}

	

?>