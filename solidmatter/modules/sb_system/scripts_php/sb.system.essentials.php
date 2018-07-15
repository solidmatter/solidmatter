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
	
	const ENABLED		= CONFIG::DEBUG['ENABLED'];
	
	const LOG_ALL		= CONFIG::DEBUG['LOG_ALL'];
	
	const BASIC			= CONFIG::DEBUG['BASIC'];
	const CLIENT		= CONFIG::DEBUG['CLIENT'];
	const IMPORT		= CONFIG::DEBUG['IMPORT'];
	const SESSION		= CONFIG::DEBUG['SESSION'];
	const REQUEST		= CONFIG::DEBUG['REQUEST'];
	const HANDLER		= CONFIG::DEBUG['HANDLER'];
	const NODE			= CONFIG::DEBUG['NODE'];
	const REDIRECT		= CONFIG::DEBUG['REDIRECT'];
	const EXCEPTIONS	= CONFIG::DEBUG['EXCEPTIONS'];
	const PDO			= CONFIG::DEBUG['PDO'];
	
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
function DEBUG(string $sText, bool $bInUse = FALSE) {
	if (DEBUG::ENABLED && ($bInUse || DEBUG::LOG_ALL)) {
		static $oDebugger = NULL;
		if (!$oDebugger) {
			$oDebugger = new Logger($_SERVER['REQUEST_URI'], 'debug.txt');
		}
		$oDebugger->addText($sText);
	}
}

//--------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
class Logger {
	protected $sLogfile;
	protected $sContent;
	public function __construct(string $sSubject, string $sLogFile) {
		// TODO: improve timezone handling (dirty hack to avoid strict warning below)
		date_default_timezone_set('Europe/Berlin');
		$this->sCWD = getcwd();
		if (!CONFIG::LOGDIR_ABS) { // log directory is not absolute path
			$this->sLogfile = $this->sCWD.'/'.CONFIG::LOGDIR.$sLogFile;
		} else {
			$this->sLogfile = CONFIG::LOGDIR.$sLogFile;
		}
		// TODO: use this info?
		//$this->sLogSize
		$this->sContent .= '----- [ '.strftime('%y-%m-%d %H:%M:%S', time()).' ] ----- [ '.$_SERVER['REQUEST_URI']." ] -----\r\n";
	}
	public function __destruct() {
		error_log($this->sContent."\r\n", 3, $this->sLogfile);
	}
	public function addText(string $sText) {
		$this->sContent .= $sText."\r\n";
	}
}


//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function import(string $sLibrary, bool $bRequired = TRUE) {
	
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
function sm_autoload($sClassName) {
	
	global $_AUTOLOAD;
	
	if (isset($_AUTOLOAD[$sClassName])) {
		import($_AUTOLOAD[$sClassName]);
	} else {
		die('sm_autoload: UNKNOWN CLASS ('.$sClassName.')');	
	}
	
}
spl_autoload_register('sm_autoload', TRUE);

//------------------------------------------------------------------------------
/**
* Generates a UUID, hyphens not included!
* @return string a 16 byte UUID as hexadecimal string
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
 * Generates a sbUUID, which means a ordered and web-safe Base64 representation
 * @return string a 16 byte UUID
 */
function sbUUID() {
	return base64url_encode(hex2bin(ordered_uuid()));
}

//------------------------------------------------------------------------------
/**
 * 
 */
function ordered_uuid() {
	
	static $iCounter = 0;
	static $mtLast = 0;
	
	$mtNow = microtime(true);
	
	// check if generating UUIDs was too fast for microtime(), add 1ms in this case
	if ($mtLast != $mtNow) {
		$mtLast = $mtNow;
		$iCounter = 0;
	} else {
		$mtLast = $mtNow;
		$iCounter++;
	}
// 	echo floor($mtNow);
// 	echo ($mtNow-floor($mtNow))*10000000;
// 	echo 'i='.$iCounter;
// 	echo '|';
// 	echo $mtNow;
// 	echo '|';
// 	echo floor($mtNow);
// 	echo '|';
// 	echo floor ((($mtNow-floor($mtNow))*10000000)) + $iCounter;
// 	echo '|';
	
	// 16 bytes = 32 chars hex
	// 8+6+4+4+4+2
	return sprintf('%08x%06x%04x%04x%04x%04x%02x',
			floor($mtNow), 
			(($mtNow-floor($mtNow))*10000000)+$iCounter,
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000, 
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xff )
			);
}


//------------------------------------------------------------------------------
/**
 * Converts a binary String to URL-Safe Base64 (without filler '==' at the end)
 * @return string a 16 byte UUID
 */
function base64url_encode($sInput) {
	return substr(strtr(base64_encode($sInput), '+/', '-_'), 0, -2);
}

//------------------------------------------------------------------------------
/**
 * Converts a sbUUID to a binary string
 * @return 
 */
function base64url_decode($sInput) {
	return base64_decode(strtr($sInput.'==', '-_', '+/'));
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
			header('Cache-Control: max-age='.$aOptions['seconds']);
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

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function die_fancy($sText) {
	
	echo '
		<html>
			<head>
				<title>solidMatter has died...</title>
				<style type="text/css">
					html {
						background-color: black;
						font-family: Arial, Helvetica, Sans-Serif;
					}
					div.exception {
						border: 25px solid black;
						background-color: black;
					}
					table.exception {
						border: 1px solid darkred;
						margin: 0;
						background-color: black;
						color: red;
						font-size: 80%;
						border-collapse: collapse;
					}
					table.exception th, 
					table.exception td {
						padding: 2px;
						text-align: left;
						border: 1px solid darkred;
					}
					table.exception th {
						background-color: darkred;
						color: black;
					}
					table.exception th.gurumeditation {
						text-align: center;
						background-color: black;
						color: red;
						border: 1px solid black;
						padding: 0;
					}
					
					#gurumeditation {
						padding: 3px 30px;
						margin-bottom:20px;
					}
					div.gm_on {
						border: 5px solid red;
					}
					div.gm_off {
						border: 5px solid black;
					}
				</style>
			</head>
			<body>
				<div class="exception"><table class="exception">
				<tr>
					<th colspan="4" class="gurumeditation">
						<div id="gurumeditation" class="gm_on">
							Guru Meditation #DEAD0815.BEEF4711<br />
							'.$sText.'
						</div>
						<script language="Javascript" type="text/javascript">
							function toggleGM() {
								oGM = document.getElementById("gurumeditation");
								if (oGM.className == "gm_on") {
									oGM.className = "gm_off";
								} else {	
									oGM.className = "gm_on";
								}
							}
							window.setInterval("toggleGM()", 1000);
						</script>
					</th>
				</tr>
				</table></div>
			</body>
		</html>
	';
	
	/*if (is_numeric($sText)) {
		die((int) $sText);
	}*/
	die();
	
}

?>