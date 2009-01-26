<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbSystem:sb.system.errors');
import('sbSystem:external:getid3/getid3');
import('sbSystem:sb.tools.filesystem.directory');
import('sbSystem:sb.tools.strings.conversion');
import('sbJukebox:sb.tools.import');

//------------------------------------------------------------------------------
/**
*/
class DefaultJukeboxImporter {
	
	protected $nodeJukebox = NULL;
	protected $jbToolkit = NULL;
	
	protected $bVerbose = TRUE;
	protected $iMaxAlbums = 10000;
	protected $iImportedAlbums = 0;
	
	// config
	protected $aAbortFlags = array(
		'NO_GENRE' => FALSE,
		//'NO_ID3V1' => TRUE,
		'NO_COVER' => TRUE,
		//'DUPLIVATE_ALBUM' => TRUE,
		
	);
	
	protected $aVerboseFlags = array(
		'artist_query' => FALSE,
		'dirname_format' => FALSE,
		'cover_info' => TRUE,
		'track_names' => TRUE,
	);
	
	/*protected $aKnownDefects = array(
		'128kbs' => 'LOW QUALITY',
		'incomplete' => 'INCOMPLETE',
		'bad quality' => 'LOW QUALITY',
	);
	
	protected $aKnownAlbumTypes = array(
		'' => 'LP',
		'maxi' => 'EP',
		'single' => 'SINGLE',
		'live' => 'LIVE',
		'remixes' => 'REMIXES',
		'compilation' => 'COMPILATION',
		'best Of' => 'BESTOF',
		'greatest Hits' => 'BESTOF',
		'custom' => 'BOOTLEG',
		'bootleg' => 'BOOTLEG',
	);*/
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($nodeSubject) {
		$this->nodeJukebox = $nodeSubject;
		$this->jbToolkit = new JukeboxToolkit($nodeSubject);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function startImport() {
		
		global $_RESPONSE;
		
		ini_set('max_execution_time', 6000000);
		
		header('Content-Type: text/html; encoding="UTF-8"'."\r\n");
		echo ('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html><head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8">
			<style type="text/css">
			html {
				font-size: 12px;
				font-family: Arial, Helvetica, sans-serif;
			}
			h1 {
				font-size: 1.2em;
				margin-bottom: 5px;
				padding: 5px 0 0 0;
			}
			h1 a {
				text-decoration: none;
				display: block;
				color: black;
			}
			p.bad {
				font-weight: bold;
				color: red;
			}
			p.info {
				font-weight: bold;
				color: blue;
			}
			p.good {
				font-weight: bold;
				color: green;
			}
			p.warning {
				font-weight: bold;
				color: yellow;
			}
			span.note {
				color: #999;
			}
			h1.category {
				background-color: #EEEECC;
				padding: 5px;
				margin-bottom: 0px;
			}
			div.albums {
				background-color: #F8F8D8;
				padding: 5px;
				margin: 0;
				display: none;
			}
			h1.album {
				
			}
			div.albuminfo {
				backgropund-color: #EEE;
				padding: 5px;
			}
			
			</style>
			<script language="javascript" type="text/javascript">
				
				function toggle(sID) {
					var oElement = document.getElementById(sID);
					if (oElement.style.display == "none") {
						oElement.style.display = "block";
					} else {
						oElement.style.display = "none";
					}
				}

				function addItemToCategory(sCategory, sItemID) {
					var oContainer = document.getElementById("albums_" + sCategory);
					var oCounter = document.getElementById("counter_" + sCategory);
					var oAlbum = document.getElementById("album_" + sItemID);
					oContainer.appendChild(oAlbum);
					//alert(oCounter.nodeValue);
					var iCurrent = parseInt(oCounter.firstChild.nodeValue);
					iCurrent++;
					oCounter.firstChild.nodeValue = iCurrent.toString();
				}
			
			</script>
			</head>
			<body>
			<p>starting import...</p>
			<h1 class="category"><a href="javascript:toggle(\'albums_good\')">Good: <span id="counter_good">0</span></a></h1>
			<div id="albums_good" class="albums"></div>
			<h1 class="category"><a href="javascript:toggle(\'albums_warning\')">Warning: <span id="counter_warning">0</span></a></h1>
			<div id="albums_warning" class="albums"></div>
			<h1 class="category"><a href="javascript:toggle(\'albums_error\')">Error: <span id="counter_error">0</span></a></h1>
			<div id="albums_error" class="albums"></div>
			
		');
		
		// get all albums in import dir and cycle through
		$dirAlbums = new sbDirectory($this->nodeJukebox->getProperty('config_sourcepath'));
		foreach ($dirAlbums->getDirectories(TRUE) as $dirAlbum) {
			
			$sState = 'good';
			$sDirName = iconv($dirAlbum->getEncoding(), 'UTF-8', $dirAlbum->getName());
			$sAlbumHash = md5($dirAlbum->getName());
			$sAlbumContainerID = 'album_'.$sAlbumHash;
			$sAlbumInfoID = 'info_'.$sAlbumHash;
			echo ('<div id="'.$sAlbumContainerID.'"><h1><a href="javascript:toggle(\''.$sAlbumInfoID.'\')">'.$sDirName.'</a></h1><div id="'.$sAlbumInfoID.'">');
			
			if ($this->iImportedAlbums > $this->iMaxAlbums) {
				throw new ImportException('threshold of '.$this->iMaxAlbums.' reached', E_WARNING);
			}
			
			try {
				
				$aResult = $this->jbToolkit->importAlbum($dirAlbum);
				
				if ($_REQUEST->getParam('dry') != 'true') {
					
					$this->nodeJukebox->getSession()->beginTransaction('sbJukebox::importAlbum');
					
					try {
						
						$nodeArtist = $aResult['nodeAlbumArtist'];
						$nodeAlbum = $aResult['nodeAlbum'];
						
						if (!$nodeArtist->isNew()) {
							$nodeArtist->save();
						}
						$this->nodeJukebox->save();
						
						$aLibraryInfo['album_uuid'] = $nodeAlbum->getProperty('jcr:uuid');
						$aLibraryInfo['state'] = 'imported';
						file_put_contents($dirAlbum->getAbsPath().'sbJukebox.txt', serialize($aLibraryInfo));
						
						$this->jbToolkit->storeArtists();
						
					} catch (Exception $e) {
						$this->nodeJukebox->getSession()->rollback();
						throw $e;
					}
					
					$this->nodeJukebox->getSession()->commit('sbJukebox::importAlbum');
					
				}
				
				$this->iImportedAlbums++;
				$this->echoInfo('good', '[imported] new album as "'.$aResult['nodeAlbum']->getProperty('label').'"');
				
			} catch (ImportException $e) {
				
				// FIXME: incomplete albums (with errors) are saved regardless of exceptions!
				$this->jbToolkit->dumpArtists();
				$this->nodeJukebox->refresh(FALSE);
				
				if ($_REQUEST->getParam('dry') != 'true') {
					/*$aLibraryInfo['state'] = 'skipped';
					file_put_contents($dirAlbum->getAbsPath().'sbJukebox.txt', serialize($aLibraryInfo));*/
				}
				
				if ($e->getCode() == E_WARNING) {
					$this->echoInfo('info', $e->getMessage());
					$sState = 'warning';
				} else {
					$this->echoInfo('bad', $e->getMessage());
					$sState = 'error';
				}
				
			}
			
			echo '</div></div>';
			
			echo '<script language="javascript">toggle(\''.$sAlbumInfoID.'\'); addItemToCategory("'.$sState.'", "'.$sAlbumHash.'");</script>';
			
		}
		
		echo '<p>Import ended</p></body></html>';
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function echoInfo($sMode, $sMessage) {
		if (!$this->bVerbose) {
			if ($sMode == 'info' || $sMode == 'note') {
				return;
			}
		}
		echo '<p class="'.$sMode.'">'.$sMessage.'</p>';
	}
	
}

?>