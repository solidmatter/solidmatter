<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* Stores a row to a file in Excel CSV format.
* The row is an array of preferably string or number values. The used field
* delimiter and quote character can be changed.
* (original by twebb@boisecenter.com, taken from PHP documentation)
* @param handle A writable handle to a previously opened file.
* @param array The row data
* @param string the field delimiter to be used
* @param string the quote character to be used
* @return integer Length of the row string
*/
function fputcsv($hFile, $aRow, $sFieldDelimiter = ';', $sQuote = '"') {
	
	$sValue = '';
	
	foreach ($aRow as $sCell) {
		
		$sCell = str_replace("\r\n", "\n", $sCell);
		$sCell = str_replace($sQuote, $sQuote.$sQuote, $sCell);
		
		if (strchr($sCell, $sFieldDelimiter) !== FALSE || strchr($sCell, $sQuote) !== FALSE || strchr($sCell, "\n") !== FALSE) {
			$sValue .= $sQuote.$sCell.$sQuote.$sFieldDelimiter;
		} else {
			$sValue .= $sCell.$sFieldDelimiter;
		}
		
	}
	
	fputs($hFile, substr($sValue, 0, -1)."\n");
	
	return strlen($sValue);
}

//------------------------------------------------------------------------------
/**
* 
* @access public
* @param 
* @return 
*/
function file_get_csv($sFilename, $bUseFirstLineAsHeaders = FALSE, $sFieldDelimiter = ';') {
	
	$aCSV = array();
	$hFile = fopen($sFilename, 'r');
	
	if ($bUseFirstLineAsHeaders) {
		$aHeader = fgetcsv($hFile, 4096, $sFieldDelimiter);
	}
	
	while (($aRow = fgetcsv($hFile, 4096, $sFieldDelimiter)) !== FALSE) {
		if ($bUseFirstLineAsHeaders AND isset($aHeader)) {
			foreach ($aHeader as $mKey => $sHeading) {
				$aNamedRow[$sHeading] = (isset($aLine[$mKey])) ? $aLine[$mKey] : '';
			}
			$aCSV[] = $aNamedRow;
		} else {
			$aCSV[] = $aRow;
		}
	}
	
	fclose($hFile);
	
	return ($aCSV);
	
}

//------------------------------------------------------------------------------
/**
* 
* @access public
* @param 
* @return 
*/
function file_put_csv($sFilename, $aArray, $sFieldDelimiter = ';', $sQuote = '"') {
	
	$hFile = fopen($sFilename, 'w');
	
	foreach ($aArray as $mKey => $aRow) {
		fputcsv($hFile, $aRow, $sFieldDelimiter, $sQuote);
	}
	
	fclose($hFile);
	
	return (TRUE);
	
}

?>