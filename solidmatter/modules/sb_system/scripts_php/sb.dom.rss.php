<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @subpackage Core
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.datetime');
import('sb.tools.forms');
import('sb.tools.xml');

//------------------------------------------------------------------------------
/**
*/
class RSSFeed extends sbDOMDocument {
	
	private $domFeed;
	
	private $aRequiredChannelNodes = array(
		'title' => FALSE, 
		'link' => FALSE,
		'description' => FALSE
	);
	
	private $aOptionalChannelNodes = array(
		'language' => FALSE,
		'copyright' => FALSE,
		'managingEditor' => FALSE,
		'webMaster' => FALSE,
		'pubDate' => FALSE,
		'lastBuildDate' => FALSE,
		'category' => FALSE,
		'generator' => FALSE,
		'docs' => FALSE,
		'cloud' => FALSE,
		'ttl' => FALSE,
		'image' => FALSE,
		'rating' => FALSE,
		'textInput' => FALSE,
		'skipHours' => FALSE,
		'skipDays' => FALSE
	);
	
	private $aOptionalItemNodes = array(
		'title' => FALSE,
		'link' => FALSE,
		'description' => FALSE,
		'author' => FALSE,
		'category' => FALSE,
		'comments' => FALSE,
		'enclosure' => FALSE,
		'guid' => FALSE,
		'pubDate' => FALSE,
		'source' => FALSE
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($aChannelInfo) {
		
		$this->domFeed = new DOMDocument('1.0', 'UTF-8');
		//$this->domFeed = new DOMDocument('1.0');
		$this->domFeed->substituteEntities = TRUE;
		$eRoot = $this->domFeed->createElement('rss');
		$eRoot->setAttribute('version', '2.0');
		$eChannel = $this->domFeed->createElement('channel');
		foreach ($aChannelInfo as $sNode => $sValue) {
			
			// check if the node is valid for RSS2.0
			if (!isset($this->aRequiredChannelNodes[$sNode]) && !isset($this->aOptionalChannelNodes[$sNode])) {
				die ('Invalid Channel Child for RSS2.0: '.$sNode.' ('.$sValue.')');	
			}
			
			if (isset($this->aRequiredChannelNodes[$sNode])) {
				$this->aRequiredChannelNodes[$sNode] = TRUE;
			}
			
			$eNode = $this->domFeed->createElement($sNode, $sValue);
			$eChannel->appendChild($eNode);
			
		}
		
		foreach ($this->aRequiredChannelNodes as $sNode => $bExists) {
			if (!$bExists) {
				die ('Required Channel Child missing: '.$sNode);	
			}
		}
		
		$eRoot->appendChild($eChannel);
		$this->domFeed->appendChild($eRoot);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addItem($aItemInfo) {
		
		if (!isset($aItemInfo['title']) && !isset($aItemInfo['description'])) {
			die ('Missing both title- and description-child for an item!');
		}
		
		$eChannel = $this->domFeed->documentElement->firstChild;
		$eItem = $this->domFeed->createElement('item');
		
		foreach ($aItemInfo as $sNode => $mValue) {
			
			if (!isset($this->aOptionalItemNodes[$sNode])) {
				die ('Invalid Item Child for RSS2.0: '.$sNode.' ('.print_r($mValue).')');	
			}
			
			switch ($sNode) {
				
				case 'category':
					if (!is_array($mValue)) {
						die ('Item Node not an array: '.$sNode);
					}
					foreach ($mValue as $sURL => $sTitle) {
						$eNode = $this->domFeed->createElement('category', $sTitle);
						if (!is_numeric($sURL)) {
							$eNode->setAttribute('domain', $sURL);
						}
						$eItem->appendChild($eNode);
					}
					break;
					
				case 'pubDate':
					if (is_mysqldatetime($mValue)) {
						$mValue = datetime_mysql2rfc822($mValue);
					}
					$eNode = $this->domFeed->createElement('pubDate', $mValue);
					$eItem->appendChild($eNode);
					break;
					
				default:
					if (!is_string($mValue)) {
						die ('Item Node not a string value: '.$sNode);
					}
					$eNode = $this->domFeed->createElement($sNode, $mValue);
					$eItem->appendChild($eNode);
					break;
			}
			
			$eChannel->appendChild($eItem);
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function saveXML($sFilename) {
		file_put_contents($sFilename, pretty_print($this->domFeed->saveXML()));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function outputXML() {
		header('Content-type: application/rss+xml');
		echo pretty_print($this->domFeed->saveXML());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadXML($sSourceURL) {
		$sFeed = file_get_contents($sSourceURL);
		$bSuccess = $this->domFeed->loadXML($sFeed);
		if ($sFeed === FALSE || !$bSuccess) {
			return (FALSE);
		} else {
			return (TRUE);
		}
	}
	

}

?>