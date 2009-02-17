<?php

class XSLRequestHandler {
	
	public function handleRequest($crSession) {
		
		try {
			
			$sURI = $_REQUEST->getURI();
			$aURI = parse_url($sURI);
			$aPath = explode('/', $aURI['path']);
			if (isset($aURI['query'])) {
				$aQuery = array();
				parse_str($aURI['query'], $aQuery);
				foreach ($aQuery as $sParam => $sValue) {
					$_REQUEST->setParam($sParam, $sValue);
				}
			}
			
			//var_dumpp($aPath);
			unset($aPath[count($aPath)-1]);
			unset($aPath[0]);
			$sMode = $aPath[2];
			unset($aPath[2]);
			//var_dumpp($aPath);
			
			$sTemplatePath = '/'.$aURI['host'].'::'.implode('/', $aPath);
			//var_dumpp($sTemplatePath);
			$nodeCurrent = $crSession->getNode($sTemplatePath);
			
			if ($sMode == 'preview') {
				$sLayoutXSL = $nodeCurrent->getProperty('xsl_frontend');
			} else {
				$sLayoutXSL = $nodeCurrent->getProperty('xsl_backend');
			}
			$domLayout = new sbDOMDocument();
			$domLayout->loadXML($sLayoutXSL);
			
			if ($_REQUEST->getParam('template') != NULL) {
					
				$nodeTemplate = $crSession->getNode($_REQUEST->getParam('template'));
				if ($sMode == 'preview') {
					$sTemplateXSL = $nodeTemplate->getProperty('xsl_frontend');
				} else {
					$sTemplateXSL = $nodeTemplate->getProperty('xsl_backend');
				}
				
				$domTemplate = new sbDOMDocument();
				$domTemplate->loadXML($sTemplateXSL);
				foreach ($domTemplate->firstChild->childNodes as $elemWhatever) {
					$domLayout->firstChild->appendChild($domLayout->importNode($elemWhatever, TRUE));
				}
				
			}
			
			echo $domLayout->saveXML();
			
			exit();
			
		} catch (NodeNotFoundException $e) {
			
			global $_RESPONSE;
			$_RESPONSE->addHeader('FileNotFound: '.$_REQUEST->getURI(), TRUE, 404);
			//$_RESPONSE->setRendermode('DEBUG');
			throw $e;
		}
		
	}
	
}

?>