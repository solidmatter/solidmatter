<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

$_AUTOLOAD = array(
	
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
	
	// forms
	'sbDOMForm'				=> 'sb.form',
	'sbInput'				=> 'sb.form.input',
	'InputFactory'			=> 'sb.factory.input',
	
	// other objects
	'Image'					=> 'sb.image',
	'RSSFeed'				=> 'sb.dom.rss'
	
);

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
abstract class DEBUG {
	
	const ENABLED		= TRUE;
	
	const LOG_ALL		= FALSE;
	
	const BASIC			= TRUE;
	const CLIENT		= FALSE;
	const IMPORT		= FALSE;
	const SESSION		= FALSE;
	const REQUEST		= FALSE;
	const HANDLER		= FALSE;
	const NODE			= FALSE;
	const REDIRECT		= FALSE;
	const EXCEPTIONS	= TRUE;
	const PDO			= TRUE;
	
	protected static $aTimes = array();
	
	public static function STARTCLOCK($sClockname) {
		self::$aTimes[$sClockname] = microtime(TRUE);
	}
	
	public static function STOPCLOCK($sClockname) {
		return(number_format((microtime(TRUE) - self::$aTimes[$sClockname]) * 1000, 3));
	}
	
}



//--------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function DEBUG($sText, $bInUse = TRUE) {
	if (DEBUG::ENABLED && ($bInUse || DEBUG::LOG_ALL)) {
		static $oDebugger = NULL;
		if (!$oDebugger) {
			$oDebugger = new Debugger($_SERVER['REQUEST_URI']);
		}
		$oDebugger->addText($sText."\r\n");
	}
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
	public function __construct($sRequestID) {
		// TODO: improve timezone handling (dirty hack to avoid strict warning below)
		date_default_timezone_set('Europe/Berlin');
		$this->sCWD = getcwd();
		$this->sRequestID = substr(uuid(), 0, 5);
		$this->sContent .= "\r\n----- [ ".strftime('%y-%m-%d %H:%M:%S', time())." ] ----- [ $sRequestID ]\r\n";
	}
	public function __destruct() {
		error_log($this->sContent, 3, $this->sCWD.'/_logs/debug.txt');
	}
	public function addText($sText) {
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
	
	//DEBUG('Import: requested library '.$sLibrary, DEBUG::IMPORT);
	
	static $aAlreadyLoaded = array();
	
	// check if already imported
	if (isset($aAlreadyLoaded[$sLibrary])) {
		return (TRUE);
	}
	
	Stopwatch::checkGroup('php');
	
	$aComponents = explode(':', $sLibrary);
	switch (count($aComponents)) {
		case 1:
			$sModule = 'sb_system';
			$sFile = $sLibrary;
			break;
		case 2:
			$sModule = $aComponents[0];
			$sFile = $aComponents[1];
			break;
		case 3:
			$sModule = $aComponents[0];
			$sFile = $aComponents[1].'/'.$aComponents[2];
			break;
	}
	
	// TODO: remove this workaround
	if (class_exists('System')) {
		$sModule = System::getFailsafeModuleName($sModule);
	}
	
	// include script
	$sFilename = "modules/$sModule/scripts_php/$sFile.php";
	if (file_exists($sFilename)) {
		include_once($sFilename);
		$aAlreadyLoaded[$sLibrary] = TRUE;
		$bSuccess = TRUE;
		DEBUG('Import: loading library '.$sLibrary, DEBUG::IMPORT);
	} else {
		if ($bRequired) {
			throw new LibraryNotFoundException('lib "'.$sLibrary.'" is unknown');
		} else {
			$aAlreadyLoaded[$sLibrary] = FALSE;
			$bSuccess = FALSE;
		}
	}
	
	Stopwatch::checkGroup('load');
	
	return ($bSuccess);
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function __autoload($sClassName) {
	
	global $_AUTOLOAD;
	
	if (isset($_AUTOLOAD[$sClassName])) {
		import($_AUTOLOAD[$sClassName]);
	} else {
		die('__autoload: UNKNOWN CLASS ('.$sClassName.')');	
	}
	
}

//------------------------------------------------------------------------------
/**
* Generates a UUID, hyphens not included!
* @return string a 16 byte UUID
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
function var_dumpp($mVar) {
	echo '<pre>';
	var_dump($mVar);
	echo '</pre>';
}

function var_dumppp($mVar) {
	var_dumpp($mVar);
	die();
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
			header('Content-Disposition: '.$sDisposition.'; filename="'.$sFilename.'"');
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
		case 'download':
			$sFilename = 'dummy';
			if (isset($aOptions['filename'])) {
				$sFilename = $aOptions['filename'];
			}
			header('Content-Disposition: attachment; filename="'.$sFilename.'"');
			//header('Content-type: ');
			if (isset($aOptions['size'])) {
				header('Content-Length: '.$aOptions['size']);
			}
			if (isset($aOptions['mime'])) {
				header('Content-type: '.$aOptions['mime']);
			}
			break;
		default:
			die('unknown header type "'.$sType.'"');
	}
	
	header('X-Generated-By: headers()');
	
}

?>