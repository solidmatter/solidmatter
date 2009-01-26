<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbJukebox:sb.pdo.queries');

//------------------------------------------------------------------------------
/**
*/
class JukeboxTools {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected static function getCoverFilename($nodeAlbum) {
		$sFilename = self::getFSPath($nodeAlbum);
		$sFilename .= $nodeAlbum->getProperty('info_coverfilename');
		$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
		return ($sFilename);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function sendCover($nodeAlbum) {
		
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
		$sFilename = self::getCoverFilename($nodeAlbum);
		if (file_exists($sFilename)) {
			if ($iSize != NULL) {
				$imgCover = new Image(Image::FROMFILE, $sFilename);
				$imgCover->resample($iSize, $iSize, Image::LOSEASPECT, Image::UPSAMPLE|Image::DOWNSAMPLE);
				// cache image
				$cacheImages->storeImage($nodeAlbum->getProperty('jcr:uuid'), $iSize, 'custom', $imgCover->getData());
				$imgCover->output(JPG, TRUE);
			}
			$hCover = fopen($sFilename, 'r');
			if (!$hCover) {
				die('error opening cover "'.$sFilename);
			}
			header('Content-type: '.get_mimetype_by_extension($sFilename));
			fpassthru($hCover);
			exit();
		} else {
			die('cover "'.$sFilename.'" does not exist');	
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the full filesystem path for the current node 
	* @param 
	* @return 
	*/
	public static function getFSPath($nodeSubject) {
		
		import('sbSystem:sb.tools.filesystem');
		
		switch ($nodeSubject->getPrimaryNodeType()) {
			
			case 'sbJukebox:Jukebox':
				$sPath = $nodeSubject->getProperty('config_sourcepath');
				break;
			case 'sbJukebox:Artist':
				$sPath = $nodeSubject->getParent()->getProperty('config_sourcepath');
				break;
			case 'sbJukebox:Album':
				$sPath = normalize_path(self::getFSPath($nodeSubject->getParent()), FALSE);
				$sPath .= $nodeSubject->getProperty('info_relpath');
				break;
			case 'sbJukebox:Track':
				$sPath = normalize_path(self::getFSPath($nodeSubject->getParent()), FALSE);
				$sPath .= $nodeSubject->getProperty('info_filename');
				break;
			default:
				throw new sbException(__CLASS__.': getFSPath does not support the nodetype '.$nodeSubject->getPrimaryNodeType());			
		}
		
		return ($sPath);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getPlaylist($nodeJukebox, $nodeSubject, $bShuffle = FALSE, $sFormat = 'M3U') {
		
		// prepare
		$aPlaylistItems = self::getPlaylistItems($nodeJukebox, $nodeSubject);
		if ($bShuffle) {
			shuffle($aPlaylistItems);
		}
		
		if ($sFormat == 'M3U') {
			// generate plain text M3U
			$sPlaylist = "#EXTM3U\n";
			foreach ($aPlaylistItems as $aItem) {
				// title row is always the same / convert label back from UTF8
				$sPlaylist .= '#EXTINF:'.$aItem['playtime'].','.iconv('UTF-8', 'ISO-8859-1', $aItem['label'])."\n";
				if ($nodeJukebox->getProperty('config_userealpath') == 'TRUE') { // use direct paths to e.g. a shared folder
					$sPlaylist .= iconv('UTF-8', System::getFilesystemEncoding(), $nodeSubject->getSession()->getNodeByIdentifier($aItem['uuid'])->getRealPath())."\n";
				} else { // use urls
					$sPlaylist .= 'http://'.$_REQUEST->getDomain().'/'.$aItem['uuid'].'/song/play/'.$aItem['uuid'].'.mp3?sid='.sbSession::getSessionID()."#.mp3\n";
				}
			}
		} elseif ($sFormat == 'XSPF') {
			// generate plain text M3U
			$sPlaylist = '<?xml version="1.0" encoding="UTF-8"?><playlist version="1" xmlns="http://xspf.org/ns/0/"><trackList>';
			foreach ($aPlaylistItems as $aItem) {
				if ($nodeJukebox->getProperty('config_userealpath') == 'TRUE') { // use direct paths to e.g. a shared folder
					//$sPlaylist .= iconv('UTF-8', System::getFilesystemEncoding(), $nodeSubject->getSession()->getNodeByIdentifier($aItem['uuid'])->getRealPath())."\n";
				} else { // use urls
					$sPlaylist .= '<track><location>http://'.$_REQUEST->getDomain().'/'.$aItem['uuid'].'/song/play/sid='.sbSession::getSessionID()."</track></location>\n";
				}
			}
			$sPlaylist .= '</trackList></playlist>';
		}
		
		return ($sPlaylist);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected static function getPlaylistItems($nodeJukebox, $nodeSubject, $aCyclePrevention = array()) {
		
		$aItems = array();
		
		// if it is a track just return itself
		if ($nodeSubject->getPrimaryNodeType() == 'sbJukebox:Track') {
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
				
				case 'sbJukebox:Track':
					$sTrackUUID = $nodeChild->getIdentifier();
					$aItems[$sTrackUUID]['uuid'] = $sTrackUUID;
					$aItems[$sTrackUUID]['label'] = $nodeChild->getProperty('label');
					$aItems[$sTrackUUID]['playtime'] = $nodeChild->getProperty('enc_playtime');
					break;
					
				default:
					$aItems	= array_merge($aItems, self::getPlaylistItems($nodeJukebox, $nodeChild, $aCyclePrevention));
					break;
					
			}
			
		}
		
		// if it is an artist append the tracks from foreign albums
		if ($nodeSubject->getPrimaryNodeType() == 'sbJukebox:Artist') {
			$stmtGetTitles = $nodeSubject->getSession()->prepareKnown('sbJukebox/artist/getTracks/differentAlbums');
			$stmtGetTitles->bindValue('jukebox_mpath', $nodeJukebox->getMPath(), PDO::PARAM_STR);
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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getDownloadItems($nodeSubject, $aCyclePrevention = array()) {
		
		$aItems = array();
		
		switch ($nodeSubject->getPrimaryNodeType()) {
			
			case 'sbJukebox:Album':
				$sFilename = self::getFSPath($nodeSubject);
//				$sFilename = substr($sFilename, 0, -1);
//				var_dumpp($sFilename); die();
//				$sFilename = str_replace('/', '\\', $sFilename);
				$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
				$aItems[] = $sFilename;
				break;
				
			default:
				throw new sbException(__CLASS__.': getDownloadItems() doesn\'t support nodetype '.$nodeSubject->getPrimaryNodeType());
				
		}
		
		return ($aItems);
		
	}
	
}

?>