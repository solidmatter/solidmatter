<?php

class XSLRequestHandler {
	
	public function handleRequest($crSession) {
		
		try {
			
			$sURI = $_REQUEST->getURI();
			$aURI = parse_url($sURI);
			$aPath = explode('/', $aURI['path']);
			
			//var_dumpp($_REQUEST->getLocation());
			//var_dump($sPath);
			//var_dumpp($aPath);
			unset($aPath[0]);
			$sMode = $aPath[2];
			unset($aPath[2]);
			
			$sTemplatePath = '/'.$aURI['host'].'::'.implode('/', $aURI['path']);
			//var_dumpp($sTemplatePath);
			$nodeCurrent = $crSession->getNode($sTemplatePath);
			
			if ($sMode == 'preview') {
				$sXSL = $nodeCurrent->getProperty('xsl_frontend');
			} else {
				$sXSL = $nodeCurrent->getProperty('xsl_backend');
			}
			echo $sXSL;
			exit();
			
		} catch (NodeNotFoundException $e) {
			
			global $_RESPONSE;
			$_RESPONSE->addHeader('FileNotFound: '.$_REQUEST->getURL(), TRUE, 404);
			$_RESPONSE->setRendermode('DEBUG');
			
		}
		
	}
	
}

?>