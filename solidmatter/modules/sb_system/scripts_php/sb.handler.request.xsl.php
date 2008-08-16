<?php

class XSLRequestHandler {
	
	public function handleRequest($crSession) {
		
		try {
			
			$sPath = $_REQUEST->getPath();
			$sDomain = $_REQUEST->getDomain();
			$aPath = explode('/', $sPath);
			
			//var_dumpp($_REQUEST->getLocation());
			//var_dump($sPath);
			//var_dumpp($aPath);
			unset($aPath[0]);
			$sMode = $aPath[2];
			unset($aPath[2]);
			
			$sTemplatePath = '/'.$sDomain.'::'.implode('/', $aPath);
			//var_dumpp($sTemplatePath);
			$nodeCurrent = $crSession->getNode($sTemplatePath);
			
			if ($sMode == 'preview') {
				$sXSL = $nodeCurrent->getProperty('xsl_frontend');
			} else {
				$sXSL = $nodeCurrent->getProperty('xsl_backend');
			}
			echo $sXSL;
			exit();
			
			/*$sPath = $_REQUEST->getPath();
			$aPath = explode('/', $sPath);
			
			$sUUID = $aPath[2];
			$sMode = $aPath[3];
			
			$nodeCurrent = $crSession->getNode($sUUID);
			
			$domLayoutXSL = $nodeCurrent->getStylesheet($sMode);
			echo $domLayoutXSL->saveXML();
			exit();*/
			
		} catch (NodeNotFoundException $e) {
			
			global $_RESPONSE;
			$_RESPONSE->addHeader('FileNotFound: '.$_REQUEST->getURL(), TRUE, 404);
			$_RESPONSE->setRendermode('DEBUG');
			
		}
		
	}
	
}

?>