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
			
			DEBUG('XSLHandler: Request Path = '.$sURI, DEBUG::HANDLER);
			
			//var_dumpp($aPath);
			// remove empty last part (because of trailing slash)
			unset($aPath[count($aPath)-1]);
			// remove empty first part (because of leading slash)
			unset($aPath[0]);
			// remove fixed 'templates' dir
			unset($aPath[1]);
			// get template mode (preview|edit) and remove the part
			$sMode = $aPath[2];
			unset($aPath[2]);
			//var_dumpp($aPath);
			
			$sTemplatePath = '/'.$_REQUEST->getSubject().'/'.implode('/', $aPath);
			//var_dumpp($sTemplatePath);
			$nodeCurrent = $crSession->getNode($sTemplatePath);
			
			if ($sMode == 'edit') {
				$sLayoutXSL = $nodeCurrent->getProperty('xsl_backend');
			} else {
				$sLayoutXSL = $nodeCurrent->getProperty('xsl_frontend');
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