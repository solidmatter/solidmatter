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
* TODO: implement custom Mimetypes
* @param 
* @return 
*/
function get_mimetype($sFilename, $bConsiderExtension = FALSE, $bAllowCustom = FALSE, $sRealFilename = NULL) {
	
	// set to TRUE to enable debug
	$mmdbg = FALSE;
	
	$sMimetype = '';
	$sDefaultMimetype = 'unknown/unknown';
	$bSuccess = FALSE;
	$hSubject = fopen($sFilename, 'r');
	if (!$hSubject) {
		die(__FUNCTION__.': cannot open '.$sFilename);	
	}
	$hMimetypesLibrary = fopen('modules/sb_system/mimetypes.txt', 'rb');
	
	if ($mmdbg) echo '<pre>'."$sFilename:\r\n";
	
	while (!feof($hMimetypesLibrary) && !$bSuccess) {
		
		$sBuffer = fgets($hMimetypesLibrary, 4096);
		if (strlen(trim($sBuffer)) == 0 || substr($sBuffer, 0, 1) == '#') {
			continue;
		}
		$aCurrentType = explode('|', $sBuffer);
		$iPosition	= trim($aCurrentType[0]);
		$sValueType	= trim($aCurrentType[1]);
		$mValue		= trim($aCurrentType[2]);
		$sMimetype	= trim($aCurrentType[3]);
		$mCompare	= NULL;
		
		//echo $iPosition.'|'.$sValueType.'|'.$mValue.'|'.$sMimetype.'|';
		if ($mmdbg) echo $sValueType.'|'.$sMimetype.'|';
		
		fseek($hSubject, $iPosition);
		
		if ($sValueType != 'string') {
			if (substr($mValue, 0, 2) === '0x') {
				$sValue = substr($mValue, 2);
				//$mValue = hexdec($mValue);
			} elseif (substr($mValue, 0, 1) === '0') {
				$mValue = substr($mValue, 1);
				$mValue = octdec($mValue);
				$sValue = dechex($mValue);
			}
		} else {
			$sTemp = '';
			for ($i=0; $i<strlen($mValue); $i++) {
				if ($mValue[$i] == '\\' && $i < strlen($mValue)-1) {
					if ($mValue[$i+1] == '\\') {
						$sTemp .= '\\';
						$i += 1;
					} elseif ($mValue[$i+1] == ' ') {
						$sTemp .= ' ';
						$i += 1;
					} elseif ($mValue[$i+1] == 'x') {
						$sTemp .= chr(hexdec(substr($mValue, $i+2, 2)));
						$i += 3;
					} else {
						$sTemp .= chr(octdec(substr($mValue, $i+1, 3)));
						$i += 3;
					}
				} else {
					$sTemp .= $mValue[$i];
				}
			}
			$sValue = bin2hex($sTemp);
		}
		
		if ($mmdbg) echo $mValue.'|';
		
		switch ($sValueType) {
			case 'string':
				$iLength = strlen($sValue) / 2;
				$mValue = bin2hex((string) $mValue);
				break;
			case 'long':
				$iLength = 4;
				$mValue = pack('V', $mValue);
				break;
			case 'belong':
				$iLength = 4;
				$mValue = pack('N', $mValue);
				break;
			case 'lelong':
				$iLength = 4;
				$mValue = pack('N', $mValue);
				break;
			case 'short':
				$iLength = 2;
				$mValue = pack('v', $mValue);
				break;
			case 'beshort':
				$iLength = 2;
				$mValue = pack('n', $mValue);
				break;
			case 'leshort':
				$iLength = 2;
				$mValue = pack('n', $mValue);
				break;
			case 'byte':
				$iLength = 1;
				$mValue = chr((int) $mValue);
				break;
		}
		
		$mCompare = fread($hSubject, $iLength);
		$sCompare = bin2hex($mCompare);
		
		if ($sValue == $sCompare) {
			$bSuccess = TRUE;
			if ($mmdbg) echo '--- !!!!!!! MATCH !!!!!!! ---|';
		}
		
		//echo $sValue.'('.$mValue.')'.'='.$sCompare.'('.$mCompare.')'.'?'.'<br>'."\r\n";
		if ($mmdbg) echo $sValue.'='.$sCompare.'?'."\r\n";
		
	}
	
	if ($mmdbg) echo '</pre>';
	
	fclose($hMimetypesLibrary);
	fclose($hSubject);
	
	if (!$bSuccess) {
		$sMimetype = $sDefaultMimetype;
	}
	
	if ($bConsiderExtension && $sMimetype == $sDefaultMimetype) {
		if ($sRealFilename != NULL) {
			$sFilename = $sRealFilename;	
		}
		$sCheck = get_mimetype_by_extension($sFilename, $bAllowCustom);
		if ($sCheck) {
			$sMimetype = $sCheck;
		}
	}
	
	
	return ($sMimetype);
	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function get_mimetype_by_extension($sFilename, $bAllowCustom = FALSE) {
	
	$aOfficialExtensions = array(
		'txt' => 'text/plain',
		'log' => 'text/plain',
		'conf' => 'text/plain',
		'nfo' => 'text/plain',
		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'js' => 'text/javascript',
		'css' => 'text/css',
		'svg' => 'image/svg+xml',
	);
	
	$aCustomExtensions = array(
		'asp' => 'custom/text/asp',
		'nfo' => 'custom/text/nfo',
		'sfv' => 'custom/',
		'p00' => 'custom/application/c64-diskimage',
		'd64' => 'custom/application/c64-diskimage',
	);
	
	$sExtension = substr($sFilename, strrpos($sFilename, '.')+1);
	//echo $sExtension;
	if (isset($aOfficialExtensions[$sExtension])) {
		return ($aOfficialExtensions[$sExtension]);
	}
	if ($bAllowCustom && isset($aCustomExtensions[$sExtension])) {
		return ($aCustomExtensions[$sExtension]);
	}
	
	return (FALSE);
	
}


/*
function get_Mimetype($sFile) {
	//$sMimetype = mime_content_type($sFile);
	$sMimetype =  exec(trim('modules/sb_system/bin/file -bi '.escapeshellarg($sFile))) ;
	if ($sMimetype == FALSE) {
		$sMimetype = 'unknown/unknown';
	}
	return ($sMimetype);	
}*/

/*
if (!function_exists('mime_content_type')) {
	
	function mime_content_type($sFilename) {
		
		$mmdbg = FALSE;
		
		$sMimetype = '';
		$bSuccess = FALSE;
		$hSubject = fopen($sFilename, 'r');
		$hMimetypesLibrary = fopen('modules/system/Mimetypes.txt', 'rb');
		
		if ($mmdbg) echo '<pre>'."$sFilename:\r\n";
		
		while (!feof($hMimetypesLibrary) && !$bSuccess) {
			
			$sBuffer = fgets($hMimetypesLibrary, 4096);
			if (strlen(trim($sBuffer)) == 0 || substr($sBuffer, 0, 1) == '#') {
				continue;
			}
			$aCurrentType = explode('|', $sBuffer);
			$iPosition	= trim($aCurrentType[0]);
			$sValueType	= trim($aCurrentType[1]);
			$mValue		= trim($aCurrentType[2]);
			$sMimetype	= trim($aCurrentType[3]);
			$mCompare	= NULL;
			
			//echo $iPosition.'|'.$sValueType.'|'.$mValue.'|'.$sMimetype.'|';
			if ($mmdbg) echo $sValueType.'|'.$sMimetype.'|';
			
			fseek($hSubject, $iPosition);
			
			if ($sValueType != 'string') {
				if (substr($mValue, 0, 2) === '0x') {
					$sValue = substr($mValue, 2);
					//$mValue = hexdec($mValue);
				} elseif (substr($mValue, 0, 1) === '0') {
					$mValue = substr($mValue, 1);
					$mValue = octdec($mValue);
					$sValue = dechex($mValue);
				}
			} else {
				$sTemp = '';
				for ($i=0; $i<strlen($mValue); $i++) {
					if ($mValue[$i] == '\\' && $i < strlen($mValue)-1) {
						if ($mValue[$i+1] == '\\') {
							$sTemp .= '\\';
							$i += 1;
						} elseif ($mValue[$i+1] == ' ') {
							$sTemp .= ' ';
							$i += 1;
						} elseif ($mValue[$i+1] == 'x') {
							$sTemp .= chr(hexdec(substr($mValue, $i+2, 2)));
							$i += 3;
						} else {
							$sTemp .= chr(octdec(substr($mValue, $i+1, 3)));
							$i += 3;
						}
					} else {
						$sTemp .= $mValue[$i];
					}
				}
				$sValue = bin2hex($sTemp);
			}
			
			if ($mmdbg) echo $mValue.'|';
			
			switch ($sValueType) {
				case 'string':
					$iLength = strlen($sValue) / 2;
					//$mValue = bin2hex((string) $mValue);
					break;
				case 'long':
					$iLength = 4;
					//$mValue = pack('V', $mValue);
					break;
				case 'belong':
					$iLength = 4;
					//$mValue = pack('N', $mValue);
					break;
				case 'lelong':
					$iLength = 4;
					//$mValue = pack('N', $mValue);
					break;
				case 'short':
					$iLength = 2;
					//$mValue = pack('v', $mValue);
					break;
				case 'beshort':
					$iLength = 2;
					//$mValue = pack('n', $mValue);
					break;
				case 'leshort':
					$iLength = 2;
					//$mValue = pack('n', $mValue);
					break;
				case 'byte':
					$iLength = 1;
					$mValue = chr((int) $mValue);
					break;
			}
			
			$mCompare = fread($hSubject, $iLength);
			$sCompare = bin2hex($mCompare);
			
			if ($sValue == $sCompare) {
				$bSuccess = TRUE;
				if ($mmdbg) echo '--- !!!!!!! MATCH !!!!!!! ---|';
			}
			
			//echo $sValue.'('.$mValue.')'.'='.$sCompare.'('.$mCompare.')'.'?'.'<br>'."\r\n";
			if ($mmdbg) echo $sValue.'='.$sCompare.'?'."\r\n";
			
		}
		
		if ($mmdbg) echo '</pre>';
		
		fclose($hMimetypesLibrary);
		fclose($hSubject);
		
		if (!$bSuccess) {
			$sMimetype = 'unknown/unknown';
		}
		
		return ($sMimetype);
		
	}
	
}
*/

?>