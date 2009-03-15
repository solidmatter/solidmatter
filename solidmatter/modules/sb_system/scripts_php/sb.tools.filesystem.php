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
* Prepares a filesize for display by adding a unit of measurement.
* Below 1024 bytes just ' B' is appended. If the size is larger than 1024 bytes,
* it is divided by 1024 and ' KB' is appended, and if it's larger than 1048576,
* it is divided by 1048576 and ' MB' is appended. So this works similar to any
* OS' file browser.
* @access public
* @param integer the filesize (in bytes)
* @return string contains the fitting filsize to diplay
*/
function filesize2display($iFilesize) {
	
	$sFilesize = '';
	if ($iFilesize < 1024) {
		$sFilesize = $iFilesize.' B';
	} elseif ($iFilesize < 1048576) {
		$sFilesize = number_format($iFilesize / 1024, 0, '', '').' KB';
	} else {
		$sFilesize = number_format($iFilesize / 1048576, 2, '.', '').' MB';
	}
	
	return ($sFilesize);

}

//------------------------------------------------------------------------------
/**
* 
* @access public
* @param 
* @return 
*/
function flat_dirtree(&$sRoot, &$aDirs, $sSkipRelative = '', $sRelativePath = '') {
	
	if ($aDirs == NULL) {
		$aDirs = array('/' => '/');
	}
	
	$aFiles = scandir($sRoot.$sRelativePath);
	
	foreach ($aFiles as $sFile) {
		if (substr($sFile, 0, 1) != '.' && is_dir($sRoot.$sRelativePath.'/'.$sFile)) {
			if ($sRelativePath == '') {
				$sValue = '/'.$sFile;
			} else {
				$sValue = $sRelativePath.'/'.$sFile;
			}
			if ($sRelativePath.'/'.$sFile != $sSkipRelative) {
				$aDirs[$sValue] = $sValue;
				flat_dirtree($sRoot, $aDirs, &$sSkipRelative, $sValue);
			}
		}
	}
	
	return ($aDirs);

}


//------------------------------------------------------------------------------
/**
* Copies a directory recursively, which means including all it's files and
* subdiretories. All contained data will be placed in the destination directory,
* so you can also rename the dir on the fly.
* @access public
* @param string the source directory
* @param string the destination directory
* @param boolean when set to TRUE, existing files will be overwritten
* @return boolean TRUE on success, FALSE otherwise
*/
function copydir($sSource, $sDestination, $bOverwrite = FALSE) {
	
	//echo $sSource.'|'.$sDestination.'||';
	
	$aEntries = scandir($sSource);
	
	if (substr($sDestination, strlen($sDestination)-1) == '/') {
		$sDestination = substr($sDestination, 0 , strlen($sDestination)-1);
	}
	
	if (!file_exists($sDestination)) {
		mkdirr($sDestination);
	}
	
	foreach ($aEntries as $sEntry) {
		
		if ($sEntry == '.' || $sEntry == '..' || $sEntry == '.svn') {
			continue;
		}
			
		if (is_dir($sSource.'/'.$sEntry)) {
			
			copydir($sSource.'/'.$sEntry, $sDestination.'/'.$sEntry, $bOverwrite);
			
		} else {
			
			if (file_exists($sDestination.'/'.$sEntry) && !$bOverwrite) {
				continue;
			}
			
			copy($sSource.'/'.$sEntry, $sDestination.'/'.$sEntry);
			
		}
	}
	
	return (TRUE);
}

//------------------------------------------------------------------------------
/**
* Creates a directory recursively, that means inexistent higher level dirs are
* also created. Example:
* If the dir 'foo/' exists, and mkdirr('foo/bar/fubar') is called, it
* first creates 'foo/bar' and afterwards 'foo/bar/fubar'.
* @access public
* @param string the path (resp. directories) to be created
* @return boolean TRUE on success, FALSE otherwise
*/
function mkdirr($sPath) {
	
	if (substr($sPath, strlen($sPath)-1) == '/') {
		$sPath = substr($sPath, 0 , strlen($sPath)-1);
	}
	
	if (file_exists($sPath)) {
		return (FALSE);
	}
	
	if (substr_count($sPath, '/') > 0) {
		mkdirr(substr($sPath, 0, strrpos($sPath, '/')));
	}
	
	if (!mkdir($sPath)) {
		return (FALSE);
	}
	
	return (TRUE);
}

//------------------------------------------------------------------------------
/**
* Removes a directory recursively, which means the directory itself and all
* it's content.
* @access public
* @param string the path of the dir to be removed
* @return TRUE on success, FALSE otherwise
*/
function rmdirr($sPath) {
	
	$hDir = opendir($sPath) ;
	
	while ($sEntry = readdir($hDir)) {
		
		if ($sEntry == '.' || $sEntry == '..') {
			continue;
		}
		
		if (is_dir($sPath.'/'.$sEntry)) {
			if (!rmdirr($sPath.'/'.$sEntry)) {
				return (FALSE);
			}
		} elseif (is_file($sPath.'/'.$sEntry)) {
			if (!unlink($sPath.'/'.$sEntry)) {
				return (FALSE);
			}
		}
	}
	
	closedir($hDir);
	rmdir($sPath);
	
	return (TRUE);
}

//------------------------------------------------------------------------------
/**
* Normalizes a directory path to use "/" instead of "\" (on Windows) and ending in '/'.
* CAUTION: ONLY use real path in combination with paths that are already in filesystem encoding, otherwise results are crap!!! 
* @param 
* @return 
*/
function normalize_path($sDirectory, $bUseRealpath = TRUE) {
	if ($bUseRealpath) {
		$sDirectory = realpath($sDirectory);
	}
	$sDirectory = str_replace('\\', '/', $sDirectory);
	if (substr($sDirectory, -1) != '/') {
		$sDirectory .= '/';	
	}
	return ($sDirectory);
}

?>