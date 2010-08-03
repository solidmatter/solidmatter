<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbCR_Utilities {

	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getLayout($nodeCurrent) {
		return (sbCR_Utilities::getPropertyFromAncestors($nodeCurrent, 'theme_layout'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getPropertyFromAncestors($nodeCurrent, $sProperty, $bFailIfNonexistent = FALSE) {
		
		$mProperty = NULL;
		if ($nodeCurrent->hasProperty($sProperty)) {
			try {
				$mProperty = $nodeCurrent->getProperty($sProperty);
			} catch (PathNotFoundException $e) {
				if ($bFailIfNonexistent) {
					throw $e;
				}
			}
		}
		if ($mProperty == NULL) {
			try {
				$nodeParent = $nodeCurrent->getParent();
				$mProperty = sbCR_Utilities::getPropertyFromAncestors($nodeParent, $sProperty, $bFailIfNonexistent);
			} catch (ItemNotFoundException $e) {
				// ignore
			}
		}
		
		return ($mProperty);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function removeLastLevelFromPath($sPath) {
		
		if (substr_count($sPath, '/') <=1) {
			throw new sbException('the path is already top level');
		}
		
		return (substr($sPath, 0, strrpos($sPath, '/') - 1));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function serializeBranch($nodeBranchRoot) {
		
		$sTempFilename = Registry::getValue('sb.system.temp.dir').'/'.uuid().'.xml';
		
		$xwOutput = new XMLWriter();
		//$xwOutput->openURI('file://'.$sTempFilename);
		$xwOutput->openURI('php://output');
		//$xwOutput->openMemory();
		$xwOutput->startDocument('1.0','UTF-8');
		
		sbCR_Utilities::serializeSlave($nodeBranchRoot, $xwOutput);
		
		$xwOutput->endDocument();
		//$xwOutput->outputMemory(true);
		
		//echo 'output done?';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected static function serializeSlave($nodeCurrent, $xwOutput) {
		
		$xwOutput->startElement('sbnode');
		
		foreach ($nodeCurrent->getProperties() as $sName => $mValue) {
			$xwOutput->startElement('property');
			$xwOutput->writeAttribute('name', $sName);
			$xwOutput->text($mValue);
			$xwOutput->endElement();
		}
		
		foreach ($nodeCurrent->getVotes() as $aVote) {
			$xwOutput->startElement('vote');
			$xwOutput->writeAttribute('voter', $aVote['user_uuid']);
			$xwOutput->text($aVote['vote']);
			$xwOutput->endElement();
		}
		
		foreach ($nodeCurrent->getTags() as $sTag) {
			$xwOutput->startElement('tag');
			$xwOutput->text($sTag);
			$xwOutput->endElement();
		}
		
		$niChilren = $nodeCurrent->getNodes();
		foreach ($niChilren as $nodeChild) {
			sbCR_Utilities::serializeSlave($nodeChild, $xwOutput);
		}
		
		$xwOutput->endElement();
	
	}
	
}

?>