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
class sbView_module_documentation extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'list':
				
				$sModule = $this->nodeSubject->getProperty('name');
				
				// count files
				import('sb.tools.filesystem.directory');
				$dirDocumentation = new sbDirectory('modules/'.$sModule.'/documentation/');
				$dirDocumentation->read();
				$dirDocumentation->filterFiles('/\.xml$/');
				$elemDocumentation = $dirDocumentation->getElement('documentation');
				$_RESPONSE->addData($elemDocumentation);
				break;
			
			case 'render':
				$sModule = $this->nodeSubject->getProperty('name');
				$sFile = $_REQUEST->getParam('file');
				// TODO: check file name for malicious content
				$sFileName = 'modules/'.$sModule.'/documentation/'.$sFile;
				$domDocu = new DOMDocument('1.0', 'UTF-8');
				$domDocu->load($sFileName);
				$_RESPONSE->addData($domDocu->firstChild);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
				
	}
	
}


?>