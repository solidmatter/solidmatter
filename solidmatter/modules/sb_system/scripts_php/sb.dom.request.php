<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @subpackage Core
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.system.com');
import('sb.dom.response');

//------------------------------------------------------------------------------
/**
* Extends sbDOMDocument class to provide additional functionality used for 
* transmitting data from solidMatter tier 1 to tier 2.
*/
class sbDOMRequest extends sbDOMDocument {
	
	private $sSessionID = NULL;
	
	public $aElementCache = array();
	
	//--------------------------------------------------------------------------
	/**
	* Constructor, initializes basic DOM structure.
	*/
	public function __construct() {
		parent::__construct('1.0', 'UTF-8');
		$elemRoot = $this->createElement('request');
		$this->appendChild($elemRoot);
		$elemMetadata = $this->createElement('metadata');
		$elemMetadata->setAttribute('xml:id', 'metadata');
		$elemRoot->appendChild($elemMetadata);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addParameter($sName, $mValue) {
		$nodeParam = new DOMNode($sName, $mValue);
		$this->appendChild($nodeParam);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function includeRequest($sSessionID, $bIncludeFiles = FALSE) {
		
		$this->firstChild->setAttribute('session_id', $sSessionID);
		
		$elemGlobals = $this->createElement('globals');
		$elemGlobals->setAttribute('xml:id', 'GLOBALS');
		
		$elemHeaders = $this->createElement('headers');
		$elemHeaders->setAttribute('xml:id', 'HEADERS');
		foreach($_SERVER as $sKey => $sValue) {
			if (substr($sKey, 0, 5) == 'HTTP_') {
				$elemHeader = $this->createElement('header');
				$sKey = str_replace('_', ' ', substr($sKey, 5));
				$sKey = str_replace(' ', '-', ucwords(strtolower($sKey)));
				$elemHeader->setAttribute('name', $sKey);
				$elemHeader->setAttribute('value', $sValue);
				$elemHeaders->appendChild($elemHeader);
			}
		}
		$elemGlobals->appendChild($elemHeaders);
		$this->aElementCache['HEADERS'] = $elemHeaders;
		
		$elemParams = $this->createElement('params');
		$elemParams->setAttribute('xml:id', 'PARAMS');
		$elemGlobals->appendChild($elemParams);
		$this->aElementCache['PARAMS'] = $elemParams;
		
		$elemGET = $this->convertArrayToElement('get', $_GET);
		$elemGET->setAttribute('xml:id', 'GET');
		$elemGlobals->appendChild($elemGET);
		$this->aElementCache['GET'] = $elemGET;
		
		$elemPOST = $this->convertArrayToElement('post', $_POST);
		$elemPOST->setAttribute('xml:id', 'POST');
		$elemGlobals->appendChild($elemPOST);
		$this->aElementCache['POST'] = $elemPOST;
		
		$elemCOOKIE = $this->convertArrayToElement('cookie', $_COOKIE);
		$elemCOOKIE->setAttribute('xml:id', 'COOKIE');
		$elemGlobals->appendChild($elemCOOKIE);
		$this->aElementCache['COOKIE'] = $elemCOOKIE;
		
		$elemSERVER = $this->convertArrayToElement('server', $_SERVER);
		$elemSERVER->setAttribute('xml:id', 'SERVER');
		$elemGlobals->appendChild($elemSERVER);
		$this->aElementCache['SERVER'] = $elemSERVER;
		
		if ($bIncludeFiles) {
			foreach ($_FILES as $sFieldname => $aField) {
				for ($i=0; $i<count($_FILES[$sFieldname]); $i++) {
					$sTempfile = $aField[$i]['tmp_name'];
					if (is_uploaded_file($sTempfile)) {
						$sContent = base64_encode(file_get_contents($sTempfile));
						$_FILES[$sFieldname][$i]['content'] = $sContent;
					}
				}
			}
			$elemFILES = $this->createElement('files', htmlentities(serialize($_FILES)));
			$elemFILES->setAttribute('xml:id', 'FILES');
			$elemGlobals->appendChild($elemFILES);
		}
		
		$this->firstChild->appendChild($elemGlobals);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function extractFiles() {
		
		$elemFILES = $this->getElementById('FILES');
		$_FILES = unserialize(html_entity_decode($elemFILES->nodeValue));
		foreach ($_FILES as $sFieldname => $aField) {
			for ($i=0; $i<count($_FILES[$sFieldname]); $i++) {
				$sTempfile = '_temp/uploads/'.uuid().'.tmp';
				file_put_contents($sTempfile, base64_decode($_FILES[$sFieldname][$i]['content']));
				$_FILES[$sFieldname][$i]['tmp_name'] = $sTempfile;
			}
			unset($_FILES[$sFieldname]['content']);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSessionID() {
		return ($this->firstChild->getAttribute('session_id'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function send($sPath, $sHost, $iPort = 80) {
		
		$sResponse = sendMessage($this->saveXML(), $sPath, $sHost, $iPort);		
		if ($sResponse == '') {
			throw new ResponseInvalidException('Response is empty');
		}
		
		$_RESPONSE = ResponseFactory::getInstance('global');
		
		if (!$_RESPONSE->loadXML($sResponse)) {
			throw new ResponseInvalidException('Response is no XML: '.$sResponse);
		}
		return ($_RESPONSE);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* FIXME: the crappy php-DOM library doesn't always get the damn elements via xml:id, so these are stored in a member array, which is not yet filled in this method in case of 2-tier solidmatter
	* @param 
	* @return 
	*/
	public function recieveData() {
		
		if (!isset($_SERVER['HTTP_X_MESSAGE_TYPE']) || $_SERVER['HTTP_X_MESSAGE_TYPE'] != 'sb_controller_request') {
			throw new RequestInvalidException('X-Message-Type is not "sb_controller_request"');
		}
		
		$sRequest = file_get_contents('php://input');
		
		if ($sRequest == '') {
			throw new RequestInvalidException('Request is empty');
		}
		
		if (!$this->loadXML($sRequest)) {
			throw new RequestInvalidException('Request is no XML: '.$sRequest);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function bounce() {
		header('Content-type: application/xml; encoding="UTF-8"');
		echo $this->saveXML();
		exit();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getParam($sName, $sSource = 'ALL') {
		
		$mValue = NULL;
		
		// TODO: support multiple fields
		switch ($sSource) {
			case 'ALL':
				$mValue = $this->getParam($sName, 'PARAMS');
				if ($mValue === NULL) {
					$mValue = $this->getParam($sName, 'GET');
				}
				if ($mValue === NULL) {
					$mValue = $this->getParam($sName, 'POST');
				}
				if ($mValue === NULL) {
					$mValue = $this->getParam($sName, 'COOKIE');
				}
				if ($mValue === NULL) {
					$mValue = $this->getParam($sName, 'SERVER');
				}
				return ($mValue);
			case 'PARAMS':
			case 'GET':
			case 'POST':
			case 'COOKIE':
			case 'SERVER':
				$elemCurrent = $this->aElementCache[$sSource];
				break;
		}
		
		// TODO: deal with non-string inputs
		$nlParams = $elemCurrent->getElementsByTagName($sName);
		foreach ($nlParams as $elemParam) {
			if ($elemParam->hasChildNodes()) {
				$mValue = array();
				foreach ($elemParam->childNodes as $elemValue) {
					$mValue[] = (string) $elemValue->nodeValue;
				}
				if (count($mValue) == 1) {
					$mValue = $mValue[0];
				}
				//import('sb.system.essentials');
				//if ($sName == 'bounce') var_dumpp($mValue);
				
			} else {
				$mValue = (string) $elemParam->nodeValue;
				// TODO: this is quite a hack - existing but empty parameters should be handled in a more robust fashion
				
// 				if ($mValue == "") {
// 					$mValue = 'true';
// 				}
			}
		}
		
		return ($mValue);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getParams($sSource = 'ALL') {
		
		$aParams = array();
		
		// TODO: support multiple fields
		if ($sSource == 'ALL') {
			
			$aParams = array_merge($aParams, $this->getParams('COOKIE'));
			$aParams = array_merge($aParams, $this->getParams('POST'));
			$aParams = array_merge($aParams, $this->getParams('GET'));
			$aParams = array_merge($aParams, $this->getParams('PARAMS'));
			
		} else {
			
			switch ($sSource) {
				case 'PARAMS':
				case 'GET':
				case 'POST':
				case 'COOKIE':
					$elemCurrent = $this->aElementCache[$sSource];
					break;
			}
			
			foreach ($elemCurrent->childNodes as $elemChild) {
				if ($elemChild->nodeType != XML_ATTRIBUTE_NODE) {
					$aParams[$elemChild->nodeName] = (string) $elemChild->nodeValue;
				}
			}
			
		}
		
		return ($aParams);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setParam($sName, $mValue) {
		$elemParams = $this->aElementCache['PARAMS'];
		$bExists = FALSE;
		/*$nlParams = $elemParams->getElementsByTagName($sName);
		foreach ($nlParams as $elemParam) {
			$bExists = TRUE;
			$elemParam->nodeValue = $mValue; // TODO: will not yet support multiple values
		}*/
		if (!$bExists) {
			if (!is_array($mValue)) {
				$elemParam = $this->createElement(htmlspecialchars($sName), htmlspecialchars($mValue));
			} else {
				$elemParam = $this->createElement(htmlspecialchars($sName));
				foreach ($mValue as $sValue) {
					$elemParamValue = $this->createElement('entry', htmlspecialchars($sValue));
					$elemParam->appendChild($elemParamValue);
				}
			}
			$elemParams->appendChild($elemParam);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getServerValue($sName) {
		$sValue = NULL;
		$elemServer = $this->aElementCache['SERVER'];
		$nlParams = $elemServer->getElementsByTagName($sName);
		foreach ($nlParams as $elemParam) {
			$sValue = (string) $elemParam->nodeValue;
		}
		return ($sValue);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getHeader($sName) {
		$elemHeaders = $this->aElementCache['HEADERS'];
		foreach ($elemHeaders->childNodes as $elemHeader) {
			if ($elemHeader->getAttribute('name') == $sName) {
				return ((string) $elemHeader->getAttribute('value'));
			}
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAllHeaders() {
		$aHeaders = array();
		$elemHeaders = $this->aElementCache['HEADERS'];
		foreach ($elemHeaders->childNodes as $elemHeader) {
			$aHeaders[$elemHeader->getAttribute('name')] = (string) $elemHeader->getAttribute('value');
		}
		return ($aHeaders);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setRepository($sRepositoryID, $sWorkspace, $sUser, $sPass) {
		$elemMetadata = $this->getElementById('metadata');
		$elemRepository = $this->createElement('repository');
		$elemRepository->setAttribute('xml:id', 'repository');
		$elemRepository->setAttribute('id', $sRepositoryID);
		$elemRepository->setAttribute('workspace', $sWorkspace);
		$elemRepository->setAttribute('user', $sUser);
		$elemRepository->setAttribute('pass', $sPass);
		$elemMetadata->appendChild($elemRepository);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getRepository() {
		
		$elemRepository = $this->getElementById('repository');
		
		if ($elemRepository == NULL) {
			return (NULL);
		}
		
		$aRepository['id'] = (string) $elemRepository->getAttribute('id');
		$aRepository['workspace'] = (string) $elemRepository->getAttribute('workspace');
		$aRepository['user'] = (string) $elemRepository->getAttribute('user');
		$aRepository['pass'] = (string) $elemRepository->getAttribute('pass');
		
		return ($aRepository);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function setURI($sURI) {
		
		$aURI = parse_url($sURI);
		
		// store 
		$this->firstChild->setAttribute('uri', $sURI);
		
		$elemMetadata = $this->getElementById('metadata');
		$elemURI = $this->createElement('uri');
		$elemURI->setAttribute('xml:id', 'uri');
		
		$sLocation = $this->getLocation();
		$sAbsolutePath = $aURI['host'].$aURI['path'];
		$aURI['relevant_path'] = str_replace($sLocation, '', $sAbsolutePath);
		
		foreach ($aURI as $sFragment => $sData) {
			$elemURI->setAttribute($sFragment, $sData);
		}
		
		// compute relevant path
		$elemMetadata->appendChild($elemURI);
		
		return;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function getURI() {
		return ((string) $this->firstChild->getAttribute('uri'));
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Returns the URI with the protocol and location part removed
	 * @param
	 * @return
	 */
	public function getRelevantPath() {
		$elemURI = $this->getElementById('uri');
		return ((string) $elemURI->getAttribute('relevant_path'));
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Returns the URI with the protocol and location part removed
	 * @param
	 * @return
	 */
	public function getQuery() {
		$elemURI = $this->getElementById('uri');
		return ((string) $elemURI->getAttribute('query'));
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	/*public function getURI() {
	
		$elemRepository = $this->getElementById('repository');
	
		if ($elemRepository == NULL) {
			return (NULL);
		}
	
		$aRepository['id'] = (string) $elemRepository->getAttribute('id');
		$aRepository['workspace'] = (string) $elemRepository->getAttribute('workspace');
		$aRepository['user'] = (string) $elemRepository->getAttribute('user');
		$aRepository['pass'] = (string) $elemRepository->getAttribute('pass');
		
		return ($aRepository);
	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setLocation($sLocation) {
		return ($this->firstChild->setAttribute('location', $sLocation));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getLocation() {
		$sLocation = $this->firstChild->getAttribute('location');
		if (substr($sLocation, -1) == '/') {
			$sLocation = substr($sLocation, 0, -1);
		}
		return ($sLocation);
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	/*public function setRelativePath($sPath) {
		return ($this->firstChild->setAttribute('relative_path', $sPath));
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	/*public function getRelativePath() {
		$sPath = $this->firstChild->getAttribute('relative_path');
		if (substr($sPath, -1) == '/') {
			$sPath = substr($sPath, 0, -1);
		}
		return ($sPath);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDomain() {
		return ($this->getServerValue('HTTP_HOST'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPath() {
		return ($this->getServerValue('REQUEST_URI'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getRelativePath() {
		$sFullPath = $this->getServerValue('HTTP_HOST').$this->getServerValue('REQUEST_URI');
		$sRelativePath = str_replace($this->getLocation(), '', $sFullPath);
		if ($sRelativePath{0} != '/') {
			$sRelativePath = '/'.$sRelativePath;
		}
		return ($sRelativePath);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setHandler($sHandler) {
		$this->firstChild->setAttribute('handler', $sHandler);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getHandler() {
		return ($this->firstChild->getAttribute('handler'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setSubject($sSubject) {
		$this->firstChild->setAttribute('subject', $sSubject);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSubject() {
		return ($this->firstChild->getAttribute('subject'));
	}
	
}

?>