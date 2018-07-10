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
class sbView_module_installer extends sbView {
	
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
				
			case 'activate':
				
				// load structure definition
				
				// gather actions over versions
				
				// check dependencies
				
				// perform sequential actions
				
				break;
				
			case 'deactivate':
				
				break;
				
			case 'checkUpdates':
				
				break;
				
			case 'update':
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
				
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function loadStructure() {
		
		$sModuleName = $this->nodeSubject->getXXXXXXXXXXXX();
		
	}
	
}


?>