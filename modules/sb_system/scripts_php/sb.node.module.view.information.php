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
class sbView_module_information extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$sModule = $this->nodeSubject->getProperty('name');
				
				// include properties
				$sPropertiesFile = 'modules/'.$sModule.'/properties.xml';
				$domProperties = new DOMDocument();
				$domProperties->load($sPropertiesFile);
				$_RESPONSE->addData($domProperties->firstChild);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
				
	}
	
}


?>