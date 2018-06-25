<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
abstract class RequestHandler {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) { }
	
	//--------------------------------------------------------------------------
	/**
	* Returns the parsed request URI.
	* @param 
	* @return 
	*/
	public function parseURI() {
		
		// init
		$aRequest['node_uuid'] = NULL;
		$aRequest['view'] = NULL;
		$aRequest['action'] = NULL;
		
		$aURI = parse_url($_REQUEST->getURI());
		
		// compute requested node / view / action / (rest of path ignored)
		$aPath = explode('/', $_REQUEST->getRelevantPath());
		if (isset($aPath[1]) && $aPath[1] != '-' && $aPath[1] != '') {
			$aRequest['node_uuid'] = $aPath[1];
		}
		if (isset($aPath[2]) && $aPath[2] != '-' && $aPath[2] != '') {
			$aRequest['view'] = $aPath[2];
		}
		if (isset($aPath[3]) && $aPath[3] != '-' && $aPath[3] != '') {
			$aRequest['action'] = $aPath[3];
		}
		
		$aQuery = array();
		parse_str($_REQUEST->getQuery(), $aQuery);
		foreach ($aQuery as $sParam => $sValue) {
			$_REQUEST->setParam($sParam, $sValue);
		}
		
		return ($aRequest);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Generates a fully qualified request URL suitable for this handler.
	* Default: {PROTOCOL}://{DOMAIN}/{LOCATION}/{PATH}...
	* @param 
	* @return 
	*/
	public function generateRequestURL($mSubject = NULL, $sView = NULL, $sAction = NULL, $aParameters = NULL) {
		
		// TODO: completely rewrite request URL/URI handling, port and path to the site should be part of the request and response
		$sPrefix = 'http://';
		/*$sPrefix = '';
		if (!$bUseHTTPS) {
			$sPrefix = 'http://';
		} elseif ($bUseHTTPS === NULL && is_secure_connection()) {
			$sPrefix = 'https://';
		} elseif ($bUseHTTPS) {
			$sPrefix = 'https://';
		}*/
		
		$sPort = '';
		if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
			$sPort = ':'.$_SERVER['SERVER_PORT'];
		}
		$sLocation = $_REQUEST->getLocation();
		if (substr($sLocation, -1) == '/') {
			$sLocation = substr($sLocation, 0, -1).$sPort.'/';
		} else {
			$sLocation = $sLocation.$sPort;
		}
// 		var_dumpp($sPort);
// 		var_dumpp($sLocation);
		
		$sDestinationURL  = $sPrefix.$sLocation.$this->generateRequestPath($mSubject, $sView, $sAction, $aParameters);
		
// 		var_dumpp($sDestinationURL);
		
		return ($sDestinationURL);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Generates a request path suitable for this handler.
	* Default: /{NODE_UUID}/{VIEW}/{ACTION}/?{PARAM}={VALUE}&{PARAM}={VALUE}...
	* @param 
	* @return 
	*/
	public function generateRequestPath($mSubject = NULL, $sView = NULL, $sAction = NULL, $aParameters = NULL) {
		
		if ($mSubject == NULL && $sView == NULL && $sAction == NULL && $aParameters == NULL) {
			return ('');
		}
		
		if (is_string($mSubject)) {
			$sSubjectUUID = $mSubject;
		} elseif ($mSubject instanceof sbNode) {
			$sSubjectUUID = $mSubject->getProperty('jcr:uuid');
		} elseif ($mSubject === NULL) { // handler will decide on subject
			$sSubjectUUID = '-';
		} else {
			throw new sbException('only nodes and strings supported');
		}
		
		if ($sView == NULL) {
			$sView = '-';
		}
		if ($sAction == NULL) {
			$sAction = '-';
		}
		
		$sURL = '/'.$sSubjectUUID.'/'.$sView.'/'.$sAction.'/';
		
		if ($aParameters != NULL) {
			$aTemp = array();
			foreach ($aParameters as $sKey => $sValue) {
				$aTemp[] = $sKey.'='.$sValue;
			}
			$sURL .= '?'.implode('&', $aTemp);
		}
		
		return ($sURL);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* To be called if the Request fails in basic request processing.
	* 
	* @param string the message to deliver to the client
	* @param int the response code, defaults to "418 i'm a teapot"
	*/
	protected function fail($sMessage = "unspecified error", $iCode = 418) {
		header("X-sbErrorMessage: ".$sMessage, $iCode);
		die($sMessage);
	}
	
}

?>