<?php

//------------------------------------------------------------------------------
/**
* 	
*	@package solidBrickz
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

TODO: revamp for solidMatter

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function extract_archive($sSourceFile, $sDestinationDir, $bCreate = TRUE, $bOverwrite = FALSE) {	
	
	global $LANGUAGE;
	global $LOCALE;
	
	if (substr($sDestinationDir, strlen($sDestinationDir)-1) != '/') {
		$sDestinationDir = $sDestinationDir.'/';
	}
	
	$sRealPath = realpath($sSourceFile);
	if (!file_exists($sRealPath)) {
		echo('File not found: '.$sRealPath);
		return (FALSE);
	}
	
	if (!file_exists($sDestinationDir) || !is_dir($sDestinationDir)) {
		mkdir($sDestinationDir);
	}
	
	$aFiles = array();
	
	if (substr($sSourceFile, strlen($sSourceFile) - 4) == '.zip') {
		
		$hPackage = zip_open($sRealPath);
		
		while ($hZippedFile = zip_read($hPackage)) {
			
			zip_entry_open($hPackage, $hZippedFile, "rb");
			
			$sCurrentName = zip_entry_name($hZippedFile);
			
			$bDir = FALSE;
			if (substr($sCurrentName, strlen($sCurrentName)-1) == '/') {
				$bDir = TRUE;
			}
			
			$sCurrentObject = $sDestinationDir.$sCurrentName;
			//echo $sCurrentObject;
			if ($bDir) {
				if (!file_exists($sCurrentObject)) {
					mkdir($sCurrentObject);
				}
			} else {
				
				if (file_exists($sCurrentObject)) {
					if ($bOverwrite) {
						unlink($sCurrentObject);
					} else {
						continue;
					}
				}
				
				$sBuffer = zip_entry_read($hZippedFile, zip_entry_filesize($hZippedFile));
				$hOutput = fopen($sCurrentObject,"w");
				fwrite($hOutput, $sBuffer);
				fclose($hOutput);
				
			}
			
			zip_entry_close($hZippedFile);
			
			/*
			$aTemp = array();
		
			$aTemp['name']				= zip_entry_name($hZippedFile);
			$aTemp['filesize']			= zip_entry_filesize($hZippedFile);
			$aTemp['compressedsize']	= zip_entry_compressedsize($hZippedFile);
			$aTemp['compressionmethod']	= zip_entry_compressionmethod($hZippedFile);
			
			$aFiles[] = $aTemp;
			*/
			
			
			/*
			echo "Name:               " . zip_entry_name($hZippedFile)."\n";
			echo "Actual Filesize:    " . zip_entry_filesize($hZippedFile)."\n";
			echo "Compressed Size:    " . zip_entry_compressedsize($hZippedFile)."\n";
			echo "Compression Method: " . zip_entry_compressionmethod($hZippedFile)."\n";
			zip_entry_open($hPackage, $hZippedFile, "r");
			//echo zip_entry_name($hZippedFile)."\r\n";
			echo 'content:'.zip_entry_read($hZippedFile, 1024)."\r\n";
			
			
			if (zip_entry_name($hZippedFile) == 'package.txt') {
				zip_entry_open($hPackage, $hZippedFile, "r");
				$sContent = zip_entry_read($hZippedFile, zip_entry_filesize($hZippedFile));
				zip_entry_close($hZippedFile);
			}*/
		}
		zip_close($hPackage);
		
		//print_r($aFiles); exit();
		
		
	} elseif (substr($sRealPath, strlen($sRealPath) - 3) == '.gz') {
		die('Not implemented yet!');
	} else {
		die('Archive type not recognized/supported!');	
	}
	
	return (TRUE);
}

?>