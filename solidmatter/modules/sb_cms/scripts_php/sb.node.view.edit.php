<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.utilities');

//------------------------------------------------------------------------------
/**
*/
class sbView_edit extends sbView {
	
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
			
			case 'display':
				break;
				
			case 'render':
				
				case 'render':
				
				// gather primary content
				$this->nodeSubject->gatherContent();
				$_RESPONSE->addData($this->nodeSubject->getElement(TRUE, TRUE), 'page_content');
				
				// gather path data
				$niAncestors = $this->nodeSubject->getAncestors();
				$elemAncestors = $niAncestors->getElement('ancestors');
				$_RESPONSE->addData($elemAncestors);
				
				// include layout data
				$sLayoutUUID = sbCR_Utilities::getPropertyFromAncestors($this->nodeSubject, 'theme_layout');
				$nodeTemplate = $this->getNode($sLayoutUUID);
				$nodeTemplate->gatherContent();
				$_RESPONSE->addData($nodeTemplate->getElement(TRUE, TRUE), 'page_layout');
				
				$sTemplateURL = $this->nodeSubject->getStylesheetURL('edit');
				if ($sTemplateURL !== FALSE) {
					$_RESPONSE->setRenderMode('rendered', 'text/html', $sTemplateURL);
				} else {
					$_RESPONSE->forceRenderMode('debug');
				}
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
}

?>