<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.system.errors');
import('sb_system:external:getid3/getid3');
import('sb.tools.filesystem.directory');
import('sb.tools.strings.conversion');
import('sb_jukebox:sb.tools.import');

//------------------------------------------------------------------------------
/**
*/
class DefaultJukeboxImporter {
	
	protected $nodeJukebox = NULL;
	protected $jbToolkit = NULL;
	
	protected $bVerbose = TRUE;
	protected $iMaxAlbums = 1000;
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
				padding: 15px 0 0 0;
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
			</style>
			</head>
			<body>
		');
		
		// get all albums in import dir and cycle through
		$dirAlbums = new sbDirectory($this->nodeJukebox->getProperty('config_sourcepath'));
		foreach ($dirAlbums->getDirectories(TRUE) as $dirAlbum) {
			
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
//						var_dumpp('startImport1:'.$nodeAlbum->getProperty('sbcr:inheritrights'));
						
						if (!$nodeArtist->isNew()) {
							$nodeArtist->save();
//							var_dumpp('startImport2:'.$nodeAlbum->getProperty('sbcr:inheritrights'));
						}
						$this->nodeJukebox->save();
						
//						var_dumpp('startImport3:'.$nodeAlbum->getProperty('sbcr:inheritrights'));
		
						//var_dumpp($nodeAlbum->getName().'-'.$nodeAlbum->getProperty('sbcr:inheritrights'));
						//var_dumpp($nodeAlbum->getProperties());
						
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
				
				$this->jbToolkit->dumpArtists();
				$this->nodeJukebox->refresh(FALSE);
				
				if ($_REQUEST->getParam('dry') != 'true') {
					/*$aLibraryInfo['state'] = 'skipped';
					file_put_contents($dirAlbum->getAbsPath().'sbJukebox.txt', serialize($aLibraryInfo));*/
				}
				
				if ($e->getCode() == E_WARNING) {
					$this->echoInfo('info', $e->getMessage());
				} else {
					$this->echoInfo('bad', $e->getMessage());
				}
				
			}
			
			echo '<hr>';
			
			echo '<script language="javascript">window.location.hash = "album_'.md5($dirAlbum->getName()).'";</script>';
			
		}
		
		echo '</body></html>';
		
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