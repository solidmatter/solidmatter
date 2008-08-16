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
				
				// gather primary content
				$this->nodeSubject->gatherContent();
				$_RESPONSE = ResponseFactory::getInstance('global');
				$_RESPONSE->addData($this->nodeSubject->getElement(TRUE, TRUE), 'content');
				
				// gather path data
				$niAncestors = $this->nodeSubject->getAncestors();
				$elemAncestors = $niAncestors->getElement('ancestors');
				$_RESPONSE->addData($elemAncestors);
				
				// gather arbitrary data
				$sLayoutUUID = sbCR_Utilities::getPropertyFromAncestors($this->nodeSubject, 'theme_layout');
				
				if ($sLayoutUUID != FALSE) {
					
					// DISABLED: testing nodes delivering stylesheets themselves
					// set output method
					$nodeTemplate = $this->getNode($sLayoutUUID);
					$sPath = $nodeTemplate->getPath();
					//$_RESPONSE->setRenderMode('rendered', 'text/html', 'http://admin.localhost/templates/preview'.$sPath);
					
					$aPath = explode('/', $sPath);
					$sPath = str_replace('::', '/', $aPath[1]).'/edit/'.implode('/', array_slice($aPath, 2));
					$_RESPONSE->setRenderMode('rendered', 'text/html', 'http://'.$sPath);
					
					// include layout data
					$nodeTemplate->gatherContent();
					$_RESPONSE->addData($nodeTemplate->getElement(TRUE, TRUE), 'layout');
					
					/*$sStylesheetURL = 'http://'.$_REQUEST->getDomain().'/templates/'.$this->nodeSubject->getProperty('uuid').'/preview';
					$_RESPONSE->setRenderMode('rendered', 'text/html', $sStylesheetURL);
					
					// include layout data
					$nodeLayout = $this->getNode($sLayoutUUID);
					$nodeLayout->gatherContent();
					$_RESPONSE->addData($nodeLayout->getElement(TRUE, TRUE), 'layout');*/
					
				} else {
					$_RESPONSE->forceRenderMode('debug');
				}
				
				/*$this->nodeSubject->gatherContent();
				$_RESPONSE->addData($this->nodeSubject->getElement(TRUE, TRUE), 'content');
				
				$sLayoutUUID = sbCR_Utilities::getLayout($this->nodeSubject);
				
				if ($sLayoutUUID != '') { // include layout data
					$nodeLayout = $this->getNode($sLayoutUUID);
					$nodeLayout->gatherContent();
					$_RESPONSE->addData($nodeLayout->getElement(TRUE, TRUE), 'layout');
				} else { // just output xml
					$_RESPONSE->forceRenderMode('debug');
				}
				
				$sStylesheetURL = 'http://'.$_REQUEST->getDomain().'/xsl/edit/'.$this->nodeSubject->getProperty('uuid').'/';
				$_RESPONSE->setRenderMode('rendered', 'text/html', $sStylesheetURL);*/
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
}

?>