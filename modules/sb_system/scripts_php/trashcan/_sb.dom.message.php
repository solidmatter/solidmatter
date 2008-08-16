<?php

import('sb.system.com');
import('sb.dom.response');

class sbDOMMessage extends sbDOMDocument {
	
	public function addParameter($sName, $mValue) {
		
		$nodeParam = new DOMNode($sName, $mValue);
		$this->appendChild($nodeParam);
		
	}
	
	public function includeRequest() {
		$elemGlobals = $this->createElement('globals');
		$elemGlobals->setAttribute('session_id', session_id());
		$elemGET = $this->createElement('get', serialize($_GET));
		$elemGlobals->appendChild($elemGET);
		$elemPOST = $this->createElement('post', serialize($_POST));
		$elemGlobals->appendChild($elemPOST);
		$elemCOOKIE = $this->createElement('cookie', serialize($_COOKIE));
		$elemGlobals->appendChild($elemCOOKIE);
		$this->appendChild($elemGlobals);
	}
	
	public function extractRequest() {
		
	}
	
	public function send($sPath, $sHost, $iPort = 80) {
		
		//$this->includeGlobals();
		
		$sResponse = sendPost($this->saveXML(), $sPath, $sHost, $iPort);
		//echo ($sResponse);
		$domResponse = new sbDOMMessage();
		$domResponse->loadXML($sResponse);
		
		return ($domResponse);
		
	}
	
	public function recieveData() {
		$this->loadXML(file_get_contents('php://input'));
	}
	
	
	
}






?>