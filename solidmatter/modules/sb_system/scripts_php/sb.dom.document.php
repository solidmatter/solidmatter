<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @subpackage Core
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//import('sb.node');

//------------------------------------------------------------------------------
/**
* Extends the DOMDocument class to implement additional features.
* 
*/
class sbDOMDocument extends DOMDocument {
	
	// FIXME: libxml 2.6.11 fucks up with xml:id (BUT STILL DOES NOT WORK!!!!)
	/*public function getElementById($sID) {
		//var_dumpp($sID);
		//var_dumpp(htmlspecialchars($this->saveXML()));
		$xpWorkaround = new DOMXPath($this);
		//$tbody = $doc->getElementsByTagName('tbody')->item(0);
		$sQuery = '//*[@xml:id="'.$sID.'"]';
		//$sQuery = '//*';
		$nlEntries = $xpWorkaround->evaluate($sQuery);
		//var_dumpp($nlEntries);
		//var_dumpp($nlEntries->item(0));
		return ($nlEntries->item(0));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Converts an array to an element tree.
	* Walks through the input array to convert it to a tree of DOMElements.
	* Array values of scalar or null type are converted to elements (null will 
	* result in an empty element). Array values of arrays will be run through 
	* recursively, all other types will throw an error.
	* @param string name of the resulting element
	* @param array the array to convert
	* @return DOMElement root of the resulting tree
	*/
	public function convertArrayToElement($sName, $aSubject) {
		
		if (!is_array($aSubject)) {
			throw new Exception('no array: '.var_export($aSubject));
		}
		if (!is_string($sName) || is_numeric($sName)) {
			throw new Exception('no string: '.var_export($sName));
		}
		
		//var_dumpp($sName);
		$elemArray = $this->createElement($sName);
		
		foreach($aSubject as $sNodeName => $mValue) {
			if (is_numeric($sNodeName) || is_numeric($sNodeName[0])) {
				$sNodeName = 'entry';
			}
			if (is_array($mValue)) {
				$elemCurrent = $this->convertArrayToElement($sNodeName, $mValue);
			} elseif (is_scalar($mValue)) {
				$elemCurrent = $this->createElement($sNodeName, htmlspecialchars($mValue));
			} elseif (is_null($mValue)) {
				$elemCurrent = $this->createElement($sNodeName);
			} else {
				// TODO: REMOVE!!!!!
				$elemCurrent = $this->createElement('ERROR', __CLASS__.': inner array element is wrong type: '.gettype($mValue));
			}
			$elemArray->appendChild($elemCurrent);
		}
		
		return($elemArray);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Converts an array to an element tree with fixed element names.
	* Walks through the input array to convert it to a tree of DOMElements, all 
	* of which will be named "item". Array values of scalar or null type are 
	* converted to elements (null will result in an empty element). Array values
	* of arrays will be run through recursively, all other types will throw an 
	* error.
	* @param string name of the resulting element
	* @param array the array to convert
	* @return DOMElement root of the resulting tree
	*/
	public function convertArrayToItems($sName, $aSubject) {
		
		if (!is_array($aSubject)) {
			throw new Exception('no array: '.var_export($aSubject));
		}
		
		$elemArray = $this->createElement('array');
		$elemArray->setAttribute('name', htmlspecialchars($sName));
		
		foreach($aSubject as $sNodeName => $mValue) {
			if (is_array($mValue)) {
				$elemCurrent = $this->convertArrayToItems($sNodeName, $mValue);
			} elseif (is_scalar($mValue)) {
				$elemCurrent = $this->createElement('item');
				$elemCurrent->setAttribute('name', htmlspecialchars($sNodeName));
				$elemCurrent->setAttribute('value', htmlspecialchars($mValue));
			} elseif (is_null($mValue)) {
				$elemCurrent = $this->createElement('item');
				$elemCurrent->setAttribute('name', htmlspecialchars($sNodeName));
			} else {
				// TODO: REMOVE!!!!!
				echo ('inner array element is wrong type: '.gettype($mValue)); 
			}
			$elemArray->appendChild($elemCurrent);
		}
		
		return($elemArray);
		
	}
	
	
}



?>