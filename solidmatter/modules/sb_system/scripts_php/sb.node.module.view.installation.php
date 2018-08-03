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
class sbView_module_installation extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'showStatus':
				// do nothing, data is added after switch statement
				break;
				
			case 'install':
				$this->nodeSubject->install($_REQUEST->getParam('version'));
				$this->logEvent(System::MAINTENANCE, 'MODULE_INSTALLED', $this->nodeSubject->getName());
				
				$cacheCurrent = CacheFactory::getInstance('repository');
				$cacheCurrent->clear();
				$this->logEvent(System::MAINTENANCE, 'CACHE_CLEARED', 'repository');
				$_RESPONSE->addCommand('reloadTree');
				
				break;
				
			case 'uninstall':
				$this->nodeSubject->uninstall();
				$this->logEvent(System::MAINTENANCE, 'MODULE_UNINSTALLED', $this->nodeSubject->getName());
				$cacheCurrent = CacheFactory::getInstance('repository');
				$cacheCurrent->clear();
				$this->logEvent(System::MAINTENANCE, 'CACHE_CLEARED', 'repository');
				$_RESPONSE->addCommand('reloadTree');
				break;
				
			case 'checkUpdates':
				
				break;
				
			case 'update':
				
				// gather actions over versions
				
				// check dependencies
				
				// perform sequential actions
				$this->nodeSubject->update($_REQUEST->getParam('to'));
				$this->logEvent(System::MAINTENANCE, 'MODULE_UPDATED', $this->nodeSubject->getName());
				
				$cacheCurrent = CacheFactory::getInstance('repository');
				$cacheCurrent->clear();
				$this->logEvent(System::MAINTENANCE, 'CACHE_CLEARED', 'repository');
				$_RESPONSE->addCommand('reloadTree');
				
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
		// node info is already included, always add structure info
		$_RESPONSE->addData($this->nodeSubject->getStructure());
				
	}
	
}

?>