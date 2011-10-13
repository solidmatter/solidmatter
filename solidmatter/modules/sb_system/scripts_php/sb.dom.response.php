<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @subpackage Core
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

if (!defined('PRETTYPRINT'))		define('PRETTYPRINT', TRUE);

//------------------------------------------------------------------------------
/**
*/
class sbDOMResponse extends sbDOMDocument {
	
	private $aLocales = array();
	private $aNodeCache = array();
	private $aModules = array();
	
	//--------------------------------------------------------------------------
	// initialization
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct() {
		parent::__construct('1.0', 'UTF-8');
		$elemRoot = $this->createElement('response');
		$this->appendChild($elemRoot);
		$this->__init();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function __init() {
		
		$this->firstChild->setAttribute('xml:id', 'response');
		$this->aNodeCache['response'] = $this->firstChild;
		
		// metadata
		$elemMetadata = $this->createSection('metadata', 'metadata');
		$elemModules = $this->createSection('md_modules', 'modules', $elemMetadata);
		$this->createSection('md_system', 'system', $elemMetadata);
		$this->createSection('md_parameters', 'parameters', $elemMetadata);
		$this->createSection('md_commands', 'commands', $elemMetadata);
		$this->createSection('md_headers', 'headers', $elemMetadata);
		$this->createSection('md_stopwatch', 'stopwatch', $elemMetadata);
		
		// insert modules
		$this->aModules = System::getModules();
		foreach ($this->aModules as $sModule => $unused) {
			$this->createSection($sModule, $sModule, $elemModules);
		}
		
		// content
		$this->createSection('content', 'content');
		$this->createSection('errors', 'errors');
		$this->createSection('locales', 'locales');
		
		// default status is success
		$this->setStatus(200);
		
		set_error_handler(array($this, 'addError'), E_ALL);
		
		//$this->firstChild->setAttribute('xmlns:sbform', 'http://www.solidbytes.net/sbform');
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function createSection($sID, $sNodeName, $elemParent = NULL) {
		
		$elemSection = $this->createElement($sNodeName);
		$elemSection->setAttribute('xml:id', $sID);
		$this->aNodeCache[$sID] = $elemSection;
		
		if ($elemParent == NULL) { // top level section
			$this->firstChild->appendChild($elemSection);
		} else {
			$elemParent->appendChild($elemSection);
		}
		
		return ($elemSection);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSectionElement($sID) {
		if (!isset($this->aNodeCache[$sID])) {
			$this->aNodeCache[$sID] = $this->getElementById($sID);
			if ($this->aNodeCache[$sID] == NULL) {
				throw new exception('section element not found: '.$sID);
			}
		}
		return ($this->aNodeCache[$sID]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadXML($sData, $iOptions = 0) {
		$this->aNodeCache = array();
		return (parent::loadXML($sData));
	}
	
	
	//--------------------------------------------------------------------------
	// data
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addStopwatchTime($sLabel, $sTime) {
		$elemTime = $this->createElement($sLabel, $sTime);
		$this->addMeta('md_stopwatch', $elemTime);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addStopwatchTimes($aTimes) {
		foreach ($aTimes as $sLabel => $sTime) {
			$this->addStopwatchTime($sLabel, $sTime);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setStatus($iCode) {
		$this->firstChild->setAttribute('status', $iCode);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getStatus($sModule) {
		return ((int) $this->firstChild->getAttribute('status'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addHeader($sContent, $bOverwrite = NULL, $iStatusCode = NULL) {
		$elemHeader = $this->createElement('header', htmlspecialchars($sContent));
		if ($bOverwrite !== NULL) {
			$elemHeader->setAttribute('overwrite', (int) $bOverwrite);
		}
		if ($iStatusCode !== NULL) {
			$elemHeader->setAttribute('statuscode', $iStatusCode);
		}
		$this->addMeta('md_headers', $elemHeader);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addMetadata($sSection, $sLabel, $sValue) {
		$elemValue = $this->createElement($sLabel, $sValue);
		$this->addMeta($sSection, $elemValue);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addMeta($sID, $elemMeta) {
		$elemSubject = $this->getSectionElement($sID);
		$elemSubject->appendChild($elemMeta);
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: implement cookie functionality, primary focus for silent logins
	* @param 
	* @return 
	*/
	/*public function setCookie($sID, $sData, $iTTL) {
		$elemSubject = $this->getSectionElement($sID);
		$elemSubject->appendChild($elemMeta);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addCommand($sCommand, $aParameters = array()) {
		$elemCommand = $this->createElement('command');
		$elemCommand->setAttribute('action', $sCommand);
		foreach ($aParameters as $sParam => $sValue) {
			$elemParam = $this->createElement('param');
			$elemParam->setAttribute('name', $sParam);
			$elemParam->setAttribute('value', $sValue);
			$elemCommand->appendChild($elemParam);
		}
		$this->addMeta('md_commands', $elemCommand);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addLocale($sModule = 'sbSystem', $sLanguage = NULL, $sLocale = NULL) {
		// FIXME: dependencies are no good, rework
		$sModule = System::getFailsafeModuleName($sModule);
		if ($sLanguage === NULL) {
			$sLanguage = User::getCurrentLocale();
		}
		if ($sLocale === NULL) {
			$sLocale = 'base';
		}
		$this->aLocales[$sModule][$sLanguage][$sLocale] = TRUE;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function finalizeMetadata($aAttributes = NULL) {
		
		foreach ($this->aModules as $sModule => $aDetails) {
			$elemModule = $this->getSectionElement($sModule);
			// TODO: disabled; transport some static metadata via details?
			/*foreach ($aDetails as $sParam => $sValue) {
				$elemModule->setAttribute($sParam, $sValue);
			}*/
			if (isset($this->aLocales[$sModule])) {
				foreach ($this->aLocales[$sModule] as $sLanguage => $aLocales) {
					foreach ($aLocales as $sLocale => $unused) {
						$elemLocale = $this->createElement('locale');
						$elemLocale->setAttribute('lang', $sLanguage);
						$elemLocale->setAttribute('type', $sLocale);
					}
					$elemModule->appendChild($elemLocale);
				}
			}
			$this->addMeta('md_modules', $elemModule);
		}
		
		if (User::isLoggedIn()) {
			$elemUserID = $this->createElement('userid', User::getUUID());
			$this->addMeta('md_system', $elemUserID);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function importLocales() {
		
		// locales
		$elemLocales = $this->getSectionElement('locales');
		
		// TODO: use another way to override?
		if ($elemLocales->getAttribute('used') == 'FALSE') {
			return;
		}
		
		// use array as workaround until restructuring locales
		$aTransport = array(); 
		$elemModules = $this->getSectionElement('md_modules');
		
		foreach ($elemModules->childNodes as $elemModule) {
			if (!$elemModule->hasChildNodes()) {
				continue;
			}
			$sModule = (string) $elemModule->nodeName;
			foreach	($elemModule->childNodes as $elemChild) {
				if ((string) $elemChild->nodeName == 'locale') {
					$sLanguage = (string) $elemChild->getAttribute('lang');
					$sLocale = (string) $elemChild->getAttribute('type');
					$aTransport[$sLanguage][$sModule][$sLocale] = TRUE;
				}
			}
		}
		
		// import locales
		foreach ($aTransport as $sLanguage => $aModules) {
			$elemLanguage = $this->createElement('locale');
			$elemLanguage->setAttribute('lang', $sLanguage);
			foreach ($aModules as $sModule => $aLocales) {
				foreach ($aLocales as $sLocale => $unused) {
					$domLocale = new DOMDocument();
					$bSuccess = @$domLocale->load('interface/locales/'.$sModule.'/'.$sLocale.'_'.$sLanguage.'.xml');
					if (!$bSuccess) {
						$bSuccess = @$domLocale->load('interface/locales/'.$sModule.'/'.$sLocale.'_en.xml');
					}
					if (!$bSuccess) {
						throw new LocaleNotFoundException('locale for module "'.$sModule.'" in language "'.$sLanguage.'" not found');	
					}
					$elemLanguage->appendChild($this->importNode($domLocale->firstChild, TRUE));
				}
			}
			$elemLocales->appendChild($elemLanguage);
		}
		$this->firstChild->appendChild($elemLocales);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addRequestData($sUUID, $sView, $sAction, $aParams = array()) {
		
		$elemContent = $this->getSectionElement('content');
		$elemContent->setAttribute('uuid', $sUUID);
		$elemContent->setAttribute('view', $sView);
		$elemContent->setAttribute('action', $sAction);
		//$elemContent->setAttribute('site', $_REQUEST->getLocation());
		//$elemContent->setAttribute('relative_path', $_REQUEST->getRelativePath());
		
		foreach ($_REQUEST->getParams('ALL') as $sParam => $sValue) {
			$elemContent->setAttribute($sParam, $sValue);
		}
		
		// store all parameters from request
		$elemParams = $this->getSectionElement('md_parameters');
		$aSources = array('GET', 'POST', 'COOKIE');
		foreach ($aSources as $sSource) {
			foreach ($_REQUEST->getParams($sSource) as $sParam => $sValue) {
				$elemParam = $this->createElement('param',  htmlspecialchars($sValue));
				$elemParam->setAttribute('source', $sSource);
				$elemParam->setAttribute('id', $sParam);
				$elemParams->appendChild($elemParam);
			}
		}
		
		// TODO: move to another place?
		//$this->setLocation($_REQUEST->getLocation());
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*public function setLocation($sLocation) {
		return ($this->firstChild->setAttribute('location', $sLocation));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*public function getLocation() {
		return ($this->firstChild->getAttribute('location'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasRequestData() {
		
		$elemContent = $this->getSectionElement('content');
		if ((string) $elemContent->getAttribute('uuid') != '') {
			return (TRUE);
		}
		return (FALSE);
		
	}
	
	/*public function setLanguage($sLang) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addData($mData, $sNodeName = NULL) {
		if ($mData instanceof sbDOMForm) {
			$mData->saveDOM();
			$elemData = $mData->firstChild;
			if (!$elemData instanceof DOMElement) {
				throw new sbException('addData with DOMDocument did not give a DOMElement as firstChild');
			}
		} elseif ($mData instanceof DOMDocument) {
			$elemData = $mData->firstChild;
			if (!$elemData instanceof DOMElement) {
				throw new sbException('addData with DOMDocument did not give a DOMElement as firstChild');
			}
		} elseif($mData instanceof DOMElement) {
			$elemData = $mData;
		} elseif ($mData instanceof sbNode) {
			$elemData = $mData->getElement(TRUE);
		} elseif ($mData instanceof sbCR_NodeIterator) {
			if ($sNodeName == NULL) {
				throw new sbException('adding a nodeiterator requires an node name');	
			}
			$elemData = $this->createElement('nodes');
			foreach ($mData as $nodeCurrent) {
				$elemData->appendChild($nodeCurrent->getElement());
			}
		} elseif (is_array($mData)) {
			$elemData = $this->convertArrayToElement($sNodeName, $mData);
		} elseif (is_string($mData)) {
			if ($sNodeName == NULL) {
				throw new sbException('adding a string requires an node name');	
			}
			$elemData = $this->createElement($sNodeName, htmlspecialchars($mData));
		} else {
			throw new sbException('addData does not support: '.var_export($mData, TRUE));
		}
		$elemImported = $this->importNode($elemData, TRUE);
		
		$elemContent = $this->getSectionElement('content');
		
		if ($sNodeName == NULL) {
			$elemContent->appendChild($elemImported);
		} else {
			$elemWrapper = $this->createElement($sNodeName);
			$elemWrapper->appendChild($elemImported);
			$elemContent->appendChild($elemWrapper);
		}
	}
	
	//--------------------------------------------------------------------------
	// rendering related
	//--------------------------------------------------------------------------
	/**
	* TODO: check & implement more robust code
	* @param
	* @return
	*/
	public function getRenderMode() {
		
		$sRendermode = (string) $this->firstChild->getAttribute('rendermode');
		$sForcedRendermode = (string) $this->firstChild->getAttribute('forced_rendermode');
		
		if ($sForcedRendermode != '') {
			$sRendermode = $sForcedRendermode;
		}

		return ($sRendermode);
	}

	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setRenderMode($sMode, $sMimetype = NULL, $sStylesheet = NULL) {
		$this->alterRenderMode($sMode, $sMimetype, $sStylesheet, '');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function forceRenderMode($sMode, $sMimetype = NULL, $sStylesheet = NULL) {
		$this->alterRenderMode($sMode, $sMimetype, $sStylesheet, 'forced_');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setTheme($sTheme) {
		$this->firstChild->setAttribute('theme', $sTheme);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTheme() {
		
		$sTheme = (string) $this->firstChild->getAttribute('theme');
		if ($sTheme == '') {
			$sTheme = '_default';
		}
		return ($sTheme);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function alterRenderMode($sMode, $sMimetype, $sStylesheet, $sPrefix) {
		$sMode = strtolower($sMode);
		$this->firstChild->setAttribute($sPrefix.'rendermode', $sMode);
		if ($sMimetype != NULL) {
			$this->firstChild->setAttribute($sPrefix.'mimetype', $sMimetype);
		}
		if ($sStylesheet != NULL) {
			$this->firstChild->setAttribute($sPrefix.'stylesheet', $sStylesheet);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setLocaleMode($bUseLocales) {
		$elemLocales = $this->getSectionElement('locales');
		if (!$bUseLocales && $elemLocales->getAttribute('used') != 'TRUE') {
			$elemLocales->setAttribute('used', 'FALSE');
		} else {
			$elemLocales->setAttribute('used', 'TRUE');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function forceLocaleMode($bUseLocales) {
		$elemLocales = $this->getSectionElement('locales');
		$elemLocales->setAttribute('used', 'FALSE');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function redirect($sNodepath = NULL, $sView = NULL, $sAction = NULL, $sParameters = NULL, $iStatusCode = 303) {
		$sDestinationURI = System::getRequestURL($sNodepath, $sView, $sAction, $sParameters);
		$this->addHeader('Location: '.$sDestinationURI, TRUE, $iStatusCode);
		DEBUG('Response: redirect', $sDestinationURI, DEBUG::REDIRECT);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function redirectFixed($sDestinationURI, $iStatusCode = 303) {
		$this->addHeader('Location: '.$sDestinationURI, TRUE, $iStatusCode);
		DEBUG('Response: redirect', $sDestinationURI, DEBUG::REDIRECT);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function saveOutput($sMethod = NULL) {
		
		Stopwatch::check('end', 'php');
		
		$elemResponse = $this->getSectionElement('response');
		
		$sMimetype = $elemResponse->getAttribute('mimetype');
		
		if ($sMethod == NULL) {
			$sMethod = $elemResponse->getAttribute('rendermode');
		}
		$sTest = $elemResponse->getAttribute('forced_rendermode');
		if ($sTest != '') {
			$sMethod = $elemResponse->getAttribute('forced_rendermode');
		}
		
		switch ($sMethod) {
			
			case 'headers':
				$this->sendHeaders();
				break;
			
			case 'rendered':
				
				$this->sendHeaders();
				
				$procGenerator = new XSLTProcessor();
				
				// register PHP functions, note: support can be checked with hasExsltSupport()
				$aAllowedFunctions = array(
					'datetime_mysql2local'
				);
				$procGenerator->registerPHPFunctions($aAllowedFunctions);
				
				restore_error_handler();
				
				// set stylesheet
				$sStylesheet = $this->firstChild->getAttribute('stylesheet');
				if ($sStylesheet == '') {
					$this->setRenderMode('rendered', 'text/html', 'sb_system:global.default.xsl');
					$sStylesheet = $this->firstChild->getAttribute('stylesheet');
				}
				$sTest = $elemResponse->getAttribute('forced_mimetype');
				if ($sTest != '') {
					$sMimetype = $elemResponse->getAttribute('forced_mimetype');
					$sStylesheet = $elemResponse->getAttribute('forced_stylesheet');
				}
				
				$domXSL = $this->loadStylesheet($sStylesheet);
				$procGenerator->importStyleSheet($domXSL);
				//header('Content-type: '.$sMimetype);
				
				// TODO: find a way to prettyprint correctly
				if (PRETTYPRINT) {
					$procGenerator->formatOutput = TRUE;
					//import('sb.tools.xml');
					//echo pretty_print($procGenerator->transformToXML($this));
					$sOutput = $procGenerator->transformToXML($this);
				} else {
					$sOutput = $procGenerator->transformToXML($this);
				}
				Stopwatch::check('transform', 'php');
				header('Content-Length: '.strlen($sOutput));
				header('X-sbTransformTime: '.Stopwatch::getTaskTimes('transform'));
				echo ($sOutput);
				break;
				
			case 'xml':
				header('X-sbMessageType: sbControllerResponse');
				header('Content-Type: text/xml; charset=utf-8');
				if (PRETTYPRINT) {
					$this->formatOutput = TRUE;
				}
				echo $this->saveXML();
				break;
				
			case 'debug':
				$this->formatOutput = TRUE;
				header('Content-Type: text/html; charset=utf-8');
				echo '<html><body><pre style="font-size:9px;">'.htmlspecialchars($this->saveXML()).'</pre></body></html>';
				break;
				
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function sendHeaders() {
		$elemHeaders = $this->getElementById('md_headers');
		if ($elemHeaders->hasChildNodes()) {
			foreach ($elemHeaders->childNodes as $elemHeader) {
				if ($elemHeader->nodeType != XML_ELEMENT_NODE) {
					continue;	
				}
				$sHeader = $elemHeader->nodeValue;
				$bOverwrite = $elemHeader->getAttribute('overwrite');
				$iStatusCode = $elemHeader->getAttribute('statuscode');
				if ($bOverwrite == NULL) {
					$bOverwrite = TRUE;
				}
				//echo $sHeader.'|'.$bOverwrite.'|'.$iStatusCode.'<br/>';
				if ($iStatusCode == NULL) {
					header($sHeader, $bOverwrite);
				} else {
					header($sHeader, (bool) $bOverwrite, (int) $iStatusCode);
				}
			}
		}
	}
		
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function loadStylesheet($sStylesheet) {
		
		if (strpos($sStylesheet, 'http://') === FALSE) { // local file
			list($sModule, $sXSL) = explode(':', $sStylesheet);
			$sURI = 'interface/themes/'.$this->getTheme().'/'.$sModule.'/xsl/'.$sXSL;
			if (!file_exists($sURI)) {
				$sURI = 'interface/themes/_global/xsl/default.xsl';
			}
			$domXSL = new DOMDocument();
			$domXSL->load($sURI);
			return ($domXSL);
		} else { // remote file
			$sURI = $sStylesheet;
			$domXSL = new DOMDocument();
			$hXSL = @fopen($sURI, 'r');
			if ($hXSL === FALSE) {
				throw new sbException('Stylesheet "'.$sURI.'" not found!');
			}
			$sXSL = stream_get_contents($hXSL);
			if (!$domXSL->loadXML($sXSL)) {
				throw new sbException('Stylesheet "'.$sURI.'" has errors!');
			}
			return ($domXSL);
		}
		
	}
	
	//--------------------------------------------------------------------------
	// warnings/exception
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addException($e) {
		$elemExceptions = $this->getElementById('errors');
		$elemException = $this->createElement('exception');
		$elemException->setAttribute('message', str_replace('/', '/ ', $e->getMessage()));
		$elemException->setAttribute('code', $e->getCode());
		$elemException->setAttribute('file', $e->getFile());
		$elemException->setAttribute('line', $e->getLine());
		$elemException->setAttribute('type', get_class($e));
		$elemTrace = $this->convertArrayToElement('trace', $e->getTrace());
		$elemException->appendChild($elemTrace);
		$elemExceptions->appendChild($elemException);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addError($iErrNo, $sErrStr, $sErrFile, $iErrLine) {
		
		// TODO: remove the cheesy E_DEPRECATED workaround
		if (error_reporting() == 0 || error_reporting() == 30719) {
			return;
		}
		
		$elemErrors = $this->getElementById('errors');
		if (!$elemWarnings = $this->getElementById('warnings')) {
			$elemWarnings = $this->createElement('warnings');
			$elemWarnings->setAttribute('xml:id', 'warnings');
			$elemErrors->appendChild($elemWarnings);
		}
		$elemError = $this->createElement('error');
		$elemError->setAttribute('errno', $iErrNo);
		$elemError->setAttribute('errstr', $sErrStr);
		$elemError->setAttribute('errfile', $sErrFile);
		$elemError->setAttribute('errline', $iErrLine);
		$elemWarnings->appendChild($elemError);
	}
	
}

?>