<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb_jukebox:sb.pdo.queries');

//------------------------------------------------------------------------------
/**
*/
class sbJukeboxView extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __init() {
		$this->storeLibraryInfo();
		$this->storeNowPlaying();
		$this->storeCurrentPlaylist();
		parent::__init();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getJukebox() {
		// is subject node the jukebox?
		if ($this->nodeSubject->getPrimaryNodeType() == 'sb_jukebox:jukebox') {
			$nodeJukebox = $this->nodeSubject;
		} else {
			$nodeJukebox = $this->nodeSubject->getAncestorOfType('sb_jukebox:jukebox');
		}
		
		return ($nodeJukebox);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeLibraryInfo() {
		
		global $_RESPONSE;
		
		$nodeJukebox = $this->getJukebox();
		
		// check cache
		$sCacheKey = 'JBINFO:'.$nodeJukebox->getProperty('jcr:uuid');
		$cacheData = CacheFactory::getInstance('misc');
		if ($cacheData->exists($sCacheKey)) {
			$aData = $cacheData->loadData($sCacheKey);
			$_RESPONSE->addData($aData, 'library');
			return;
		}
		
		// query and build data array
		$stmtInfo = $this->crSession->prepareKnown('sbJukebox/jukebox/gatherInfo');
		$stmtInfo->bindValue('jukebox_uuid', $nodeJukebox->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtInfo->execute();
		
		foreach ($stmtInfo as $aRow) {
			$aData['albums'] = $aRow['n_numalbums'];
			$aData['artists'] = $aRow['n_numartists'];
			$aData['tracks'] = $aRow['n_numtracks'];
			$aData['playlists'] = $aRow['n_numplaylists'];
		}
		
		$aData['min_stars'] = Registry::getValue('sb.jukebox.voting.scale.min');
		$aData['max_stars'] = Registry::getValue('sb.jukebox.voting.scale.max');
		
		// store data
		$cacheData->storeData($sCacheKey, $aData);
		
		$_RESPONSE->addData($aData, 'library');
	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clearLibraryInfo() {
		
		$nodeJukebox = $this->getJukebox();
		
		$sCacheKey = 'JBINFO:'.$this->nodeSubject->getProperty('jcr:uuid');
		$cacheData = CacheFactory::getInstance('misc');
		$cacheData->clear($sCacheKey);
	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		// nothing, has to be implemented in deriving class
		throw new sbException('method has to be implemented in deriving class!');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPivotUUID() {
		$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
		if (!isset(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot'])) {
			if (Registry::getValue('sb.jukebox.voting.display.default') == 'average') {
				$sPivotUUID = $this->crSession->getRootNode()->getProperty('jcr:uuid');
			} else {
				$sPivotUUID = User::getUUID();
			}
			sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot'] = $sPivotUUID;
		}
		return (sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildCommentForm() {
		
		$formSearch = new sbDOMForm(
			'addComment',
			'$locale/system/general/labels/comment',
			System::getURL($this->nodeSubject, 'votes', 'addComment'),
			$this->crSession
		);
		
		$formSearch->addInput('title;string;minlength=2;maxlength=250;required=true;', '$locale/system/general/labels/title');
		$formSearch->addInput('comment;text;minlength=3;maxlength=2000;required=true;', '$locale/system/general/labels/comment');
		$formSearch->addSubmit('$locale/system/general/actions/save');
		
		return ($formSearch);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildSearchForm($sSubject) {
		
		switch ($sSubject) {
			
			case 'artists':
				$sID = 'searchArtists';
				$sTarget = System::getURL('-', 'artists', 'search');
				break;
			case 'albums':
				$sID = 'searchAlbums';
				$sTarget = System::getURL('-', 'albums', 'search');
				break;
			case 'jukebox':
				$sID = 'searchJukebox';
				$sTarget = System::getURL('-', 'library', 'search');
				break;
			case 'tracks':
				$sID = 'searchTracks';
				$sTarget = System::getURL('-', 'library', 'search');
				break;
			default:
				throw new sbException('searchform subject not recognized: "'.$sSubject.'"');
			
		}
		
		$formSearch = new sbDOMForm(
			$sID,
			'$locale/system/general/labels/search/title',
			$sTarget,
			$this->crSession
		);
		
		$formSearch->addInput('searchstring;string;minlength=2;maxlength=20;', '$locale/system/general/labels/search/title');
		$formSearch->addSubmit('$locale/system/general/actions/search');
		
		return ($formSearch);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getCoverFilename($nodeAlbum) {
		$nodeArtist = $nodeAlbum->getParent();
		$nodeJukebox = $nodeArtist->getParent();
		$sFilename = $nodeJukebox->getProperty('config_sourcepath');
		$sFilename = normalize_path($sFilename);
		$sFilename .= $nodeAlbum->getProperty('info_relpath');
		$sFilename .= $this->nodeSubject->getProperty('info_coverfilename');
		$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
		return ($sFilename);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getCover($nodeAlbum) {
		
		$iSize = $_REQUEST->getParam('size');
		if ($iSize != NULL && ($iSize <= 0 || $iSize > 500)) {
			die('invalid cover size ('.$iSize.')');	
		}
		
		headers('cache');
		
		// check cache first
		// TODO: use registry value to en/disable caching
		if ($iSize != NULL) {
			$cacheImages = CacheFactory::getInstance('images');
			if ($sImageData = $cacheImages->loadImage($nodeAlbum->getProperty('jcr:uuid'), $iSize, 'custom')) {
				$imgCurrent = new Image(Image::FROMSTRING, $sImageData);
				$imgCurrent->output(JPG);
			}
		}
		
		// deliver replacement if cover does not exist
		if ($nodeAlbum->getProperty('info_coverexists') != 'TRUE') {
			$imgCover = new Image(Image::FROMFILE, 'modules/sb_jukebox/data/no_cover.png');
			if ($iSize != NULL) {
				$imgCover->resample($iSize, $iSize, Image::LOSEASPECT, Image::UPSAMPLE|Image::DOWNSAMPLE);
			}
			header('Content-type: image/jpeg');
			$imgCover->output(JPG);
		}
		
		// cover exists, so build path and read it
		$sFilename = $this->getCoverFilename($nodeAlbum);
		if (file_exists($sFilename)) {
			if ($iSize != NULL) {
				$imgCover = new Image(Image::FROMFILE, $sFilename);
				$imgCover->resample($iSize, $iSize, Image::LOSEASPECT, Image::UPSAMPLE|Image::DOWNSAMPLE);
				// cache image
				$cacheImages->storeImage($nodeAlbum->getProperty('jcr:uuid'), $iSize, 'custom', $imgCover->getData());
				$imgCover->output(JPG, TRUE);
			}
			$hCover = fopen($sFilename, 'r');
			header('Content-type: '.get_mimetype_by_extension($sFilename));
			fpassthru($hCover);
		} else {
			die('cover "'.$sFilename.'" does not exist');	
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeNowPlaying() {
		$stmtClear = $this->crSession->prepareKnown('sbJukebox/nowPlaying/clear');
		$stmtClear->bindValue('seconds', Registry::getValue('sb.jukebox.nowplaying.refresh'), PDO::PARAM_INT);
		$stmtClear->execute();
		$stmtGet = $this->crSession->prepareKnown('sbJukebox/nowPlaying/get');
		$stmtGet->execute();
		global $_RESPONSE;
		$_RESPONSE->addData($stmtGet->fetchElements(), 'nowPlaying');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeCurrentPlaylist() {
		$sJukeboxUUID = $this->getJukebox()->getIdentifier();
		if (isset(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['playlist'])) {
			$nodePlaylist = $this->crSession->getNodeByIdentifier(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['playlist']);
			global $_RESPONSE;
			$_RESPONSE->addData($nodePlaylist, 'currentPlaylist');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getPlaylist($nodeSubject, $bRandom = FALSE) {
		//var_dumpp('start');
		//var_dumpp($nodeSubject->getName());
		$aPlaylistItems = $this->getPlaylistItems($nodeSubject);
		
		//import('sb.tools.arrays');
		//$aPlaylistItems = ivsort($aPlaylistItems, 'label');
		
		if ($bRandom) {
			shuffle($aPlaylistItems);
		}
		
		$sPlaylist = "#EXTM3U\n";
		foreach ($aPlaylistItems as $aItem) {
			$sPlaylist .= '#EXTINF:'.$aItem['playtime'].','.iconv('UTF-8', 'ISO-8859-1', $aItem['label'])."\n";
			$sPlaylist .= 'http://'.$_REQUEST->getDomain().'/'.$aItem['uuid'].'/song/play/sid='.sbSession::getSessionID()."\n";
		}
		
		//var_dumpp($sPlaylist);
		//die ('fgg');
		return ($sPlaylist);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getPlaylistItems($nodeSubject, $aCyclePrevention = array()) {
		
		$aItems = array();
		
		// if it is a track just return itself
		if ($nodeSubject->getPrimaryNodeType() == 'sb_jukebox:track') {
			$sTrackUUID = $nodeSubject->getIdentifier();
			$aItems[$sTrackUUID]['uuid'] = $sTrackUUID;
			$aItems[$sTrackUUID]['label'] = $nodeSubject->getProperty('label');
			$aItems[$sTrackUUID]['playtime'] = $nodeSubject->getProperty('enc_playtime');
		}
		
		// loop all playable children
		$niChildren = $nodeSubject->getChildren('playlist');
		foreach ($niChildren as $nodeChild) {
			
			// prevent infinite loops
			if (isset($aCyclePrevention[$nodeChild->getIdentifier()])) {
				continue;
			}
			$aCyclePrevention[$nodeChild->getIdentifier()] = TRUE;
			
			// add stuff dependent on node type
			switch ($nodeChild->getPrimaryNodeType()) {
				
				case 'sb_jukebox:track':
					$sTrackUUID = $nodeChild->getIdentifier();
					$aItems[$sTrackUUID]['uuid'] = $sTrackUUID;
					$aItems[$sTrackUUID]['label'] = $nodeChild->getProperty('label');
					$aItems[$sTrackUUID]['playtime'] = $nodeChild->getProperty('enc_playtime');
					break;
					
				default:
					$aItems	= array_merge($aItems, $this->getPlaylistItems($nodeChild, $aCyclePrevention));
					break;
						
			}
			
		}
		
		// if it is an artist append the tracks from foreign albums
		if ($nodeSubject->getPrimaryNodeType() == 'sb_jukebox:artist') {
			$stmtGetTitles = $this->crSession->prepareKnown('sbJukebox/artist/getTracks/differentAlbums');
			$stmtGetTitles->bindValue('jukebox_uuid', $this->getJukebox()->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtGetTitles->bindValue('artist_uuid', $nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtGetTitles->bindValue('limit', 100, PDO::PARAM_INT);
			$stmtGetTitles->execute();
			foreach ($stmtGetTitles as $aRow) {
				$sTrackUUID = $aRow['uuid'];
				$aItems[$sTrackUUID]['uuid'] = $sTrackUUID;
				$aItems[$sTrackUUID]['label'] = $aRow['label'];
				$aItems[$sTrackUUID]['playtime'] = $aRow['playtime'];
			}
			$stmtGetTitles->closeCursor();
		}
		
		return ($aItems);
		
	}
	
}

?>