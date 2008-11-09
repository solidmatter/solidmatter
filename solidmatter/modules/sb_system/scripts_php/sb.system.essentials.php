<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
abstract class DEBUG {
	
	const BASIC			= TRUE;
	const CLIENT		= FALSE;
	const IMPORT		= FALSE;
	const SESSIONID		= FALSE;
	const REQUEST		= TRUE;
	const HANDLER		= FALSE;
	const REDIRECT		= TRUE;
	const EXCEPTIONS	= FALSE;
		
}

//--------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function DEBUG($sTitle, $mData, $bInUse = TRUE) {
	if (!$bInUse) {
		return;	
	}
	static $oDebugger = NULL;
	if (!$oDebugger) {
		$oDebugger = new Debugger();
	}
	if ($mData instanceof DOMDocument) {
		$mData = $mData->saveXML();
	}
	$sLogText = var_export($mData, TRUE);
	$oDebugger->addText('---------[ '.$sTitle.' ]---------'."\r\n".$sLogText."\r\n");
}

//--------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
class Debugger {
	protected $sCWD;
	protected $sRequestID;
	protected $sContent;
	public function __construct() {
		// TODO: improve timezone handling (dirty hack to avoid strict warning below)
		date_default_timezone_set('Europe/Berlin');
		$this->sCWD = getcwd();
		$this->sRequestID = substr(uuid(), 0, 5);
		$this->sContent = "--------------------------------------------------------------------------------\r\n";
		$this->sContent .= "-------------------------------------------------------- ".strftime('%y-%m-%d %H:%M:%S', time())." -----\r\n";
		$this->sContent .= "--------------------------------------------------------------------------------\r\n";
	}
	public function __destruct() {
		error_log($this->sContent, 3, $this->sCWD.'/_logs/debug.txt');
	}
	public function addText($sText) {
		//$this->sContent .= $this->sRequestID.'|'.$sText;
		$this->sContent .= $sText;
	}
}


//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function import($sLibrary, $bRequired = TRUE) {
	
	global $_STOPWATCH;
	
	static $iGoddamnWhiteScreenOfDeathDebugCounter = 0;
	static $aAlreadyLoaded = array();
	
	$aComponents = explode(':', $sLibrary);
	switch (count($aComponents)) {
		case 1:
			$sModule = 'sb_system';
			$sLibrary = $sLibrary;
			break;
		case 2:
			$sModule = $aComponents[0];
			$sLibrary = $aComponents[1];
			break;
		case 3:
			$sModule = $aComponents[0];
			$sLibrary = $aComponents[1].'/'.$aComponents[2];
			break;
	}
	
	// check if already imported
	if (isset($aAlreadyLoaded[$sModule][$sLibrary])) {
		return (TRUE);
	}
	
	if (--$iGoddamnWhiteScreenOfDeathDebugCounter == 0) {
		die($sLibrary);
	}
	
	// include script
	$sFilename = "modules/$sModule/scripts_php/$sLibrary.php";
	if (file_exists($sFilename)) {
		include_once($sFilename);
		$aAlreadyLoaded[$sModule][$sLibrary] = TRUE;
		$bSuccess = TRUE;
		DEBUG('import()', $sModule.':'.$sLibrary, DEBUG::IMPORT);
	} else {
		// TODO: $bRequired disabled for now - always complain!
		die ('import(): FILE_NOT_FOUND ('.$sLibrary.')');
		
		if ($bRequired) {
			throw new LibraryNotFoundException('Library not found: '.$sLibrary.' in '.$sModule);
		} else {
			$aAlreadyLoaded[$sModule][$sLibrary] = FALSE;
			$bSuccess = FALSE;
		}
	}
	/*$sFilename = "modules/$sModule/scripts_php/$sLibrary.php";
	if ($bRequired) {
		$bSuccess = @include_once($sFilename);
		if (!$bSuccess) {
			throw new LibraryNotFoundException('Library not found: "'.$sLibrary.'" in "'.$sModule.'"');
		}
	} else {
		if (!file_exists($sFilename)) {
			return (FALSE);	
		} else {
			$bSuccess = @include_once($sFilename);
		}
	}*/
	
	if ($bSuccess) {
		$aAlreadyLoaded[$sModule][$sLibrary] = TRUE;
	}
	
	$_STOPWATCH->checkGroup('load');
	
	return ($bSuccess);
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function __autoload($sClassName) {
	
	static $aKnownClasses = array(
		
		// DOM
		'sbDOMDocument'			=> 'sb.dom.document',
		'sbDOMRequest'			=> 'sb.dom.request',
		'sbDOMResponse'			=> 'sb.dom.response',
		
		// request/response
		'ResponseFactory'		=> 'sb.factory.response',
		'RequestFactory'		=> 'sb.factory.request',
		'RequestHandlerFactory' => 'sb.factory.handler',
		
		// database
		'DBFactory'				=> 'sb.factory.db',
		'sbPDO'					=> 'sb.pdo',
		'sbPDOStatement'		=> 'sb.pdo.statement',
		'sbPDOSystem'			=> 'sb.pdo.sysdb',
		
		// content repository
		'sbCR_Workspace'		=> 'sb.cr.workspace',
		'sbCR_Utilities'		=> 'sb.cr.utilities',
		/*'sbCR_Node'				=> 'sb.cr.node',
		'sbCR_Credentials'		=> 'sb.cr.credentials',
		'sbCR_Repository'		=> 'sb.cr.repository',
		'sbCR_Session'			=> 'sb.cr.session',
		'sbCR_NodeIterator'		=> 'sb.cr.nodeiterator',
		'sbCR_NodeTypeManager'	=> 'sb.cr.nodetypemanager',
		'sbCR_Utilities'		=> 'sb.cr.utilities',
		'sbCR_Query'			=> 'sb.cr.query',
		'sbCR_QueryManager'		=> 'sb.cr.querymanager',
		'sbCR_QueryResult'		=> 'sb.cr.queryresult',
		'sbCR_Row'				=> 'sb.cr.row',
		'sbCR_RowIterator'		=> 'sb.cr.rowiterator',
		'sbView'				=> 'sb.node.view',
		'sbNode'				=> 'sb.node',*/
		
		// system
		'System'				=> 'sb.system',
		'sbSession'				=> 'sb.system.session',
		'Registry'				=> 'sb.system.registry',
		'User'					=> 'sb.system.user',
		
		// caching
		'CacheFactory'			=> 'sb.factory.cache',
		'sbCache'				=> 'sb.cache',
		'PathCache'				=> 'sb.cache.paths',
		'SessionCache'			=> 'sb.cache.session',
		
		// forms
		'sbDOMForm'				=> 'sb.form',
		'sbInput'				=> 'sb.form.input',
		'InputFactory'			=> 'sb.factory.input',
		
		// other objects
		'Image'					=> 'sb.image',
		'RSSFeed'				=> 'sb.dom.rss'
		
	);
	
	if (isset($aKnownClasses[$sClassName])) {
		import($aKnownClasses[$sClassName]);
	} else {
		die('__autoload: UNKNOWN CLASS ('.$sClassName.')');	
	}
	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function uuid() {
	//return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		mt_rand( 0, 0x0fff ) | 0x4000,
		mt_rand( 0, 0x3fff ) | 0x8000,
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) 
	);
}



//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
/*function log2file($sText, $sLogfile, $bUseStandardHeader = FALSE, $iSize) {
	// TODO: implement size and splitting, check why it's not created
	static $sCWD = NULL;
	
	if ($sCWD == NULL) {
		$sCWD = getcwd().'/';
	}
	
	if ($bUseStandardHeader) {
		$sText = "\r\n".str_repeat('#', 80)."\r\n".strftime('%y-%m-%d %H:%M:%S', time())."\r\n".$sText;
	}
	
	$hLogfile = fopen($sCWD.$sLogfile, 'a');
	if (!$hLogfile) {
		echo $sText.'-'.$sLogfile.'|';
	}
	fwrite($hLogfile, $sText);
	fclose($hLogfile);
	//file_put_contents(realpath($sLogfile), $sText, FILE_APPEND);
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
/*function LOG2DB($sText, $sLogID = 'default', $sSubjectUUID = NULL, $sUserUUID = NULL) {
	throw new LazyBastardException('LOG2DB() not implemented yet');
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function var_dumpp($mVar) {
	echo '<pre>';
	var_dump($mVar);
	echo '</pre>';
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function headers($sType, $aOptions = NULL) {
	
	switch ($sType) {
		case 'cache':
			if (!isset($aOptions['seconds'])) {
				$aOptions['seconds'] = 60 * 60 * 24 * 30; // 30 days
			}
			header('Pragma:');
			header('Expires: '.gmdate('D, d M Y H:i:s', time() + $aOptions['seconds']).' GMT');
			header('Cache-Control: public; max-age='.$aOptions['seconds']);
			break;
		case 'no_cache':
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			break;
		case 'm3u':
		case 'xspf':
			$sFilename = 'dummy';
			$sDisposition = 'inline';
			if (isset($aOptions['filename'])) {
				$sFilename = $aOptions['filename'];
			}
			if (isset($aOptions['download']) && $aOptions['download']) {
				$sDisposition = 'attachment';
			}
			header('Content-Disposition: '.$sDisposition.'; filename="'.$aOptions['filename'].'"');
			if ($sType == 'm3u') {
				//header('Content-type: audio/x-mpegurl; encoding="UTF-8"');
				header('Content-type: audio/mpegurl; encoding="UTF-8"');
			} elseif ($sType == 'xspf') {
				header('Content-type: application/xspf+xml; encoding="UTF-8"');
			}
			if (isset($aOptions['size'])) {
				header('Content-Length: '.$aOptions['size']);
			}
			break;
		default:
			die('unknown header type "'.$sType.'"');
	}
	
	header('X-Generated-By: headers()');
	
}

?>