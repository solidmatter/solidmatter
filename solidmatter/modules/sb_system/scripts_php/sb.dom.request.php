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
	
	//--------------------------------------------------------------------------
	/**
	* Constructor, initializes basic DOM structure.
	*/
	public function __construct() {
		parent::__construct('1.0', CHARSET);
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
		
		$elemParams = $this->createElement('params');
		$elemParams->setAttribute('xml:id', 'PARAMS');
		$elemGlobals->appendChild($elemParams);
		
		$elemGET = $this->convertArrayToElement('get', $_GET);
		$elemGET->setAttribute('xml:id', 'GET');
		$elemGlobals->appendChild($elemGET);
		
		$elemPOST = $this->convertArrayToElement('post', $_POST);
		$elemPOST->setAttribute('xml:id', 'POST');
		$elemGlobals->appendChild($elemPOST);
		
		$elemCOOKIE = $this->convertArrayToElement('cookie', $_COOKIE);
		$elemCOOKIE->setAttribute('xml:id', 'COOKIE');
		$elemGlobals->appendChild($elemCOOKIE);
		
		$elemSERVER = $this->convertArrayToElement('server', $_SERVER);
		$elemSERVER->setAttribute('xml:id', 'SERVER');
		$elemGlobals->appendChild($elemSERVER);
		
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
		//var_dump ($_FILES);
		
		
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
	* 
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
				$elemCurrent = $this->getElementById('PARAMS');
				break;
			case 'GET':
				$elemCurrent = $this->getElementById('GET');
				break;
			case 'POST':
				$elemCurrent = $this->getElementById('POST');
				break;
			case 'COOKIE':
				$elemCurrent = $this->getElementById('COOKIE');
				break;
			case 'SERVER':
				$elemCurrent = $this->getElementById('SERVER');
				break;
		}
		
		// TODO: deal with non-string inputs
		$nlParams = $elemCurrent->getElementsByTagName($sName);
		foreach ($nlParams as $elemParam) {
			$mValue = (string) $elemParam->nodeValue;	
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
					$elemCurrent = $this->getElementById('PARAMS');
					break;
				case 'GET':
					$elemCurrent = $this->getElementById('GET');
					break;
				case 'POST':
					$elemCurrent = $this->getElementById('POST');
					break;
				case 'COOKIE':
					$elemCurrent = $this->getElementById('COOKIE');
					break;
			}
			
			foreach ($elemCurrent->childNodes as $elemChild) {
				$aParams[$elemChild->nodeName] = (string) $elemChild->nodeValue; 
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
	public function setParam($sName, $sValue) {
		// TODO: support multiple values
		$elemParams = $this->getElementById('PARAMS');
		$bExists = FALSE;
		$nlParams = $elemParams->getElementsByTagName($sName);
		foreach ($nlParams as $elemParam) {
			$bExists = TRUE;
			$elemParam->nodeValue = $sValue;
		}
		if (!$bExists) {
			$elemParam = $this->createElement($sName, $sValue);
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
		$elemServer = $this->getElementById('SERVER');
		$nlParams = $elemServer->getElementsByTagName($sName);
		foreach ($nlParams as $elemParam) {
			$mValue = (string) $elemParam->nodeValue;
		}
		return ($mValue);
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
		return ($this->firstChild->getAttribute('location'));
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
	public function getURL() {
		return ($this->getServerValue('HTTP_HOST').$this->getServerValue('REQUEST_URI'));
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