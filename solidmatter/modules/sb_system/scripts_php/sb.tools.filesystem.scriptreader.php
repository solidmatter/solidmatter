<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem.directory');

//------------------------------------------------------------------------------
/**
*/
class ScriptReader extends sbDirectory {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function read($bSkipParents = TRUE) {
		
		parent::read($bSkipParents);
		$this->filterScripts();
		$this->readSizes();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function filterScripts() {
		$this->filterFiles('/^.*\.php$/');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function readInfos() {
		
		$iTotalLinesOfCode = 0;
		
		foreach ($this->aFiles as $iIndex => $aFile) {
			$iNumLinesOfCode = 0;
			$bVersionFound = FALSE;
			$hFile = fopen($this->aInfo['abs_path'].$aFile['name'], 'r');
			if (!$hFile) {
				throw new FileNotFoundException(__CLASS__.': cannot open file '.$aFile['name']);	
			}
			while (!feof($hFile)) {
			    $sLine = fgets($hFile, 4096);
			    if (strlen(trim($sLine)) != 0) {
			    	$iNumLinesOfCode++;
			    }
			    $aMatches = array();
			    if (!$bVersionFound && preg_match('/@version\s+([0-9\.]{7})/', $sLine, $aMatches)) {
			    	$this->aFiles[$iIndex]['version'] = $aMatches[1];
			    	$bVersionFound = TRUE;
			    }
			}
			if (!$bVersionFound) {
				$this->aFiles[$iIndex]['version'] = 'N/A';
			}
			$this->aFiles[$iIndex]['codelines'] = $iNumLinesOfCode;
			$iTotalLinesOfCode += $iNumLinesOfCode;
		}
		
		$this->aInfo['totalcodelines'] = $iTotalLinesOfCode;
		
	}
	
}

?>