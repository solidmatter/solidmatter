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
class sbView_module_scripts extends sbView {
	
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
				
				// count files
				import('sb.tools.filesystem.scriptreader');
				$srScripts = new ScriptReader('modules/'.$sModule.'/scripts_php/');
				$srScripts->readInfos();
				$elemScripts = $srScripts->getElement('scripts');
				$_RESPONSE->addData($elemScripts);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
				
	}
	
}


?>