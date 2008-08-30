<?php

//------------------------------------------------------------------------------
/**
* 
* @package	solidBrickz
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

TODO: revamp for solidMatter

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function xmlbatch($xmlBatch, $sSourceDir, $sDestinationDir = '', $aOptions = NULL) {
	
	foreach ($xmlBatch->children() as $sName => $xmlAction) {
		
		switch ($sName) {
			
			// execute db-query
			case 'query':
				$sQuery = (string) $xmlAction;
				$sQuery = str_replace('{TABLE_PREFIX}', TABLE_PREFIX, $sQuery);
				$DB->QUERY($sQuery);
				break;
				
			// copy file / dir
			case 'copy':
				$sSource = (string) $xmlAction;
				$sDestination = (string) $xmlAction['to'];
				if (!file_exists($sSourceDir.'/'.$sSource)) {
					throw new SBException();
				}
				if (is_dir($sSourceDir.'/'.$sSource)) {
					copydir($sSourceDir.'/'.$sSource, $sDestination, TRUE);
				} else {
					if (file_exists($sDestination)) {
						unlink($sDestination);
					}
					copy($sSourceDir.'/'.$sSource, $sDestination, TRUE);
				}
				break;
				
			// delete file / dir
			case 'delete':
				$sFSObject = (string) $xmlAction;
				if (!file_exists($sFSObject)) {
					throw new SBException();
				}
				if (is_dir($sFSObject)) {
					rmdirr($sFSObject);
				} else {
					unlink($sFSObject);
				}
				break;
				
			// execute PHP-script
			case 'script':
				$sScript = (string) $xmlAction;
				require_once($sSourceDir.'/'.$sScript);
				break;
				
		}
	}
}




?>