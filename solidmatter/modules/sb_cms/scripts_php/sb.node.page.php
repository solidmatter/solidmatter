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
class sbNode_page extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getStylesheetURL($sView, $sAction = NULL, $sMode = NULL) {
		
		$sURL = FALSE;
		$sLayoutUUID = sbCR_Utilities::getPropertyFromAncestors($this, 'theme_layout');
		
		if ($sLayoutUUID != FALSE) {
			
			$nodeLayout = $this->crSession->getNodeByIdentifier($sLayoutUUID);
			$sPath = $nodeLayout->getPath();
			$aPath = explode('/', $sPath);
			//var_dumpp($aPath);
			switch ($sView) {
				
				case 'preview':
					$sPath = $_REQUEST->getDomain().'/templates/preview/'.implode('/', array_slice($aPath, 2));
					break;
					
				case 'edit':
					$sPath = $_REQUEST->getDomain().'/templates/edit/'.implode('/', array_slice($aPath, 2));
					break;
				
			}
			
			$sURL = 'http://'.$sPath;
			
		}
		
		$sTemplateUUID = $this->getProperty('theme_template');
		
		if ($sTemplateUUID != FALSE) {
			
			$sURL .= '/?template='.$sTemplateUUID;
			
		}
		
		return ($sURL);
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getStylesheet($sMode = NULL) {
		
		/*$sStylesheet = '<?xml version="1.0" ?>
		<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
			{IMPORTS}			
			<xsl:template match="/">
		  		<xsl:apply-imports />
			</xsl:template>
		</xsl:stylesheet>';
		
		
		$sImports = '';
		$sLayoutUUID = sbCR_Utilities::getPropertyFromAncestors($this, 'theme_layout');
		$nodeLayout = $this->crSession->getNode($sLayoutUUID);
		$sLayoutPath = sbCR_Utilities::getPath();
		
		$aPath = explode('/', $sLayoutPath);
		$sPath = str_replace('::', '/', $aPath[1]).'/preview/'.implode('/', array_slice($aPath, 2));
		$_RESPONSE->setRenderMode('rendered', 'text/html', 'http://'.$sPath);
		
		
		$sTemplateUUID = $this->getProperty('theme_template');
		if ($sTemplateUUID != NULL) {
			$nodeTemplate = $this->nodeSubject->crSession->getNode($sTemplateUUID);
		}*/
		
		
		/*if ($sMode == 'preview') {
			
			// init layout
			$sLayoutUUID = sbCR_Utilities::getPropertyFromAncestors($this, 'theme_layout');
			$nodeLayout = $this->crSession->getNode($sLayoutUUID);
			$sLayoutXSL = $nodeLayout->getProperty('xsl_frontend');
			$domLayout = new DOMDocument();
			$domLayout = DOMDocument::loadXML($sLayoutXSL);
			
			// init template
			$sTemplateUUID = $this->getProperty('theme_template');
			if ($sTemplateUUID != NULL) {
				
				$nodeTemplate = $this->crSession->getNode($sTemplateUUID);
				$sTemplateXSL = $nodeTemplate->getProperty('xsl_frontend');
				$domTemplate = DOMDocument::loadXML($sTemplateXSL);
				
				// merge stylesheets
				$nlXSLTemplates = $domTemplate->firstChild->getElementsByTagNameNS('http://www.w3.org/1999/XSL/Transform', 'template');
				foreach ($nlXSLTemplates as $elemXSLTemplate) {
					$elemImported = $domLayout->importNode($elemXSLTemplate, TRUE);
					$domLayout->firstChild->appendChild($elemImported);	
				}
				//var_dumpp(htmlentities($domLayout->saveXML()));
			}
			
			return ($domLayout);
			
		}*/
		
	}
	
	
}

?>