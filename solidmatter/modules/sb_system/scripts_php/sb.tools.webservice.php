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
* @param 
* @return 
*/
function get_xml_via_url($sURL) {
	
	# array with the options to create stream context
	$aOptions = array();
	# compose HTTP request header
	//$sHeader .= "User-Agent: sbJukebox\r\n";
	$sHeader .= "Connection: close";
	# define context options for HTTP request (use 'http' index, NOT 'httpS')
	$aOptions['http']['method'] = 'GET';
	$aOptions['http']['header'] = $sHeader;
	# define context options for SSL transport
	#$opts['ssl']['local_cert'] = $local_cert_path;
	#$opts['ssl']['passphrase'] = $local_cert_passphrase;
	# create stream context
	$streamContext = stream_context_create($aOptions);
	# POST request and get response
	
	$hResponse = fopen($sURL, 'r', FALSE, $streamContext);
	$aMetadata = stream_get_meta_data($hResponse);
	$sResponse = stream_get_contents($hResponse);
	
	return ($sResponse);
	
}

?>