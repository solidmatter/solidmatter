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
/** This view renders a page node preview based on the current state in the repository. 
*/
class sbView_preview extends sbView {
	
	protected $bLoginRequired = FALSE;
	
	//--------------------------------------------------------------------------
	/**
	* Executes the given action.
	* @param string the action 
	* @return multiple data added to response if necessary
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			// just display the layout XSL
			case 'display':
				break;
			
			// render the page preview
			case 'render':
				
				// gather primary content
				$this->nodeSubject->gatherContent();
				$_RESPONSE->addData($this->nodeSubject->getElement(TRUE, TRUE), 'content');
				
				// gather path data
				$niAncestors = $this->nodeSubject->getAncestors();
				$elemAncestors = $niAncestors->getElement('ancestors');
				$_RESPONSE->addData($elemAncestors);
				
				// include layout data
				$sLayoutUUID = sbCR_Utilities::getPropertyFromAncestors($this->nodeSubject, 'theme_layout');
				$nodeTemplate = $this->getNode($sLayoutUUID);
				$nodeTemplate->gatherContent();
				$_RESPONSE->addData($nodeTemplate->getElement(TRUE, TRUE), 'layout');
				
				$sTemplateURL = $this->nodeSubject->getStylesheetURL('preview');
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