<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

/*
	$fpSocket = fsockopen($sHost, $iPort, $iErrNo, $sErrStr);
	if (!$fpSocket) {
		throw new sbException("could not open socket '$iPort' on '$sHost'($iErrNo, $sErrStr)");	
	}
	$sMessage  = "POST $sPath HTTP/1.0\r\n";
	$sMessage .= "Host: $sHost\r\n";
	$sMessage .= "User-Agent: sbTier1\r\n";
	$sMessage .= "Content-type: text/xml; charset=utf-8\r\n";
	$sMessage .= "Content-length: ".strlen($sData)."\r\n";
	$sMessage .= "Connection: close\r\n\r\n";
	$sMessage .= $sData;
	fwrite($fpSocket, $sMessage);
	while (!feof($fpSocket)) {
		$sResponse .= fgets($fpSocket, 128);
	}
	fclose($fpSocket);
	return $sResponse;
}*/

# working vars
//$host = 'ssl.host.com';
//$service_uri = '/some/service/address';
//$local_cert_path = '/path/to/keys.pem';
//$local_cert_passphrase = 'pass_to_access_keys';
//$request_data = '<some><xml>data</xml></some>';


function sendMessage($sPayload, $sPath, $sHost, $iPort = 80) {
	
	# array with the options to create stream context
	$aOptions = array();
	# compose HTTP request header
	$sHeader  = "Host: $sHost\r\n";
	$sHeader .= "User-Agent: solidMatter Interface\r\n";
	$sHeader .= "Content-Type: text/xml\r\n";
	//$sHeader .= "Content-Type: multipart/form-data; boundary=DEADBEAF";
	$sHeader .= "Content-Length: ".strlen($sPayload)."\r\n";
	$sHeader .= "X-Message-Type: sb_controller_request\r\n";
	$sHeader .= "Connection: close";
	# define context options for HTTP request (use 'http' index, NOT 'httpS')
	$aOptions['http']['method'] = 'POST';
	$aOptions['http']['header'] = $sHeader;
	$aOptions['http']['content'] = $sPayload;
	# define context options for SSL transport
	#$opts['ssl']['local_cert'] = $local_cert_path;
	#$opts['ssl']['passphrase'] = $local_cert_passphrase;
	# create stream context
	$streamContext = stream_context_create($aOptions);
	# POST request and get response
	
	$hResponse = fopen('http://'.$sHost.$sPath, 'r', FALSE, $streamContext);
	$aMetadata = stream_get_meta_data($hResponse);
	
	// check if it is a controller response
	foreach($aMetadata['wrapper_data'] as $sHeader) {
		if ($sHeader == 'X-sbMessageType: sbControllerResponse') {
			return(stream_get_contents($hResponse));
		}
	}
	
	// no controller response, just pass through
	fpassthru($hResponse);
	
}
/*function sendPost($sPayload, $sPath, $sHost, $iPort = 80) {
	
	# array with the options to create stream context
	$aOptions = array();
	# compose HTTP request header
	$sHeader  = "Host: $sHost\r\n";
	$sHeader .= "User-Agent: solidMatter Interface\r\n";
	$sHeader .= "Content-Type: text/xml\r\n";
	//$sHeader .= "Content-Type: multipart/form-data; boundary=DEADBEAF";
	$sHeader .= "Content-Length: ".strlen($sPayload)."\r\n";
	$sHeader .= "Connection: close";
	# define context options for HTTP request (use 'http' index, NOT 'httpS')
	$aOptions['http']['method'] = 'POST';
	$aOptions['http']['header'] = $sHeader;
	$aOptions['http']['content'] = $sPayload;
	# define context options for SSL transport
	#$opts['ssl']['local_cert'] = $local_cert_path;
	#$opts['ssl']['passphrase'] = $local_cert_passphrase;
	# create stream context
	$streamContext = stream_context_create($aOptions);
	# POST request and get response
	
	$sResponse = file('http://'.$sHost.$sPath, FALSE, $streamContext);
	$sResponse = implode('', $sResponse);
	
	return($sResponse);

}*/



?>