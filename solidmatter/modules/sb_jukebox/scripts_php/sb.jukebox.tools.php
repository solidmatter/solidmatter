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
	
	protected static $nodeCurrentJukebox = null;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function setCurrentJukebox($nodeJukebox) {
		self::$nodeCurrentJukebox = $nodeJukebox;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getCurrentJukebox() {
		return (self::$nodeCurrentJukebox);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getCoverFilename($nodeAlbum) {
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
			headers('cache');
			if ($iSize != NULL) {
				$imgCover = new Image(Image::FROMFILE, $sFilename);
				$imgCover->resample($iSize, $iSize, Image::LOSEASPECT, Image::UPSAMPLE|Image::DOWNSAMPLE);
				// cache image
				$cacheImages->storeImage($nodeAlbum->getProperty('jcr:uuid'), $iSize, 'custom', $imgCover->getData());
				header('Content-type: image/jpeg');
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
			case 'sbJukebox:Artist': // is this even used? artists have no directory
				$sPath = $nodeSubject->getParent()->getProperty('config_sourcepath');
				break;
			case 'sbJukebox:Album':
				if (Registry::getValue('sb.jukebox.paths.albums.useabspath')) {
					$sPath = $nodeSubject->getProperty('info_abspath');
				} else {
					// use normalizing in case jukebox doesn't have a trailing slash, but don't use real path!
					// using real path with paths encoded in UTF-8 will have invalid results on non-UTF-8 filesystems!
					$sPath = normalize_path(self::getFSPath($nodeSubject->getParent()));
					$sPath .= $nodeSubject->getProperty('info_relpath');
				}
				break;
			case 'sbJukebox:Track':
				$sPath = self::getFSPath($nodeSubject->getParent());
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
		
		import('sb.tools.strings.conversion');
		
		// prepare
		$aPlaylistItems = self::getPlaylistItems($nodeJukebox, $nodeSubject);
		if ($bShuffle) {
			shuffle($aPlaylistItems);
		}
		
		$sToken = self::getToken();
		
		if ($sFormat == 'M3U') {
			// generate plain text M3U
			$sPlaylist = "#EXTM3U\n";
			foreach ($aPlaylistItems as $aItem) {
				// title row is always the same / convert label back from UTF8
				$sPlaylist .= '#EXTINF:'.$aItem['playtime'].','.iconv('UTF-8', 'ISO-8859-1', $aItem['label'])."\n";
				if ($nodeJukebox->getProperty('config_userealpath') == 'TRUE') { // use direct paths to e.g. a shared folder
					$sPlaylist .= iconv('UTF-8', System::getFilesystemEncoding(), $nodeSubject->getSession()->getNodeByIdentifier($aItem['uuid'])->getRealPath())."\n";
				} else { // use urls
					//$sPlaylist .= 'http://'.$_REQUEST->getDomain().'/'.$aItem['uuid'].'/song/play/'.$aItem['uuid'].'.mp3?sid='.sbSession::getID()."#.mp3\n";
					$sPlaylist .= 'http://'.$_REQUEST->getDomain().'/play/'.$aItem['uuid'].'/'.$sToken.'/'.str2urlsafe($aItem['label'], TRUE, TRUE).".mp3\n";
				}
			}
		} elseif ($sFormat == 'XSPF') {
			// generate plain text M3U
			$sPlaylist = '<?xml version="1.0" encoding="UTF-8"?><playlist version="1" xmlns="http://xspf.org/ns/0/"><trackList>';
			foreach ($aPlaylistItems as $aItem) {
				if ($nodeJukebox->getProperty('config_userealpath') == 'TRUE') { // use direct paths to e.g. a shared folder
					//$sPlaylist .= iconv('UTF-8', System::getFilesystemEncoding(), $nodeSubject->getSession()->getNodeByIdentifier($aItem['uuid'])->getRealPath())."\n";
				} else { // use urls
					$sPlaylist .= '<track><location>http://'.$_REQUEST->getDomain().'/play/'.$aItem['uuid'].'/'.$sToken.'/'.$aItem['uuid']."</track></location>\n";
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
				$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
				$aItems[] = $sFilename;
				break;
			
			case 'sbJukebox:Playlist':
				// generate M3U along the way
				$sM3UFilename = Registry::getValue('sb.system.temp.dir').'/'.$nodeSubject->getName().'.m3u';
				$sM3U = "#EXTM3U\n";
				$aItems[] = $sM3UFilename;
				$niTracks = $nodeSubject->getChildren('play');
				foreach ($niTracks as $nodeTrack) {
					$nodeTrack_PrimaryParent = $nodeSubject->getSession()->getNodeByIdentifier($nodeTrack->getProperty('jcr:uuid'));
					$sFilename = self::getFSPath($nodeTrack_PrimaryParent);
					$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
					$aItems[] = $sFilename;
					// title row is always the same / convert label back from UTF8
					$sM3U .= '#EXTINF:'.$nodeTrack->getProperty('enc_playtime').','.iconv('UTF-8', 'ISO-8859-1', $nodeTrack->getProperty('label'))."\n";
					$sCurrentFilename = $nodeTrack->getProperty('info_filename');
					$sCurrentFilename = substr($sCurrentFilename, strpos($sCurrentFilename, '/'));
					$sM3U .= iconv('UTF-8', System::getFilesystemEncoding(), $sCurrentFilename)."\n";
				}
				// TODO: save the M3U and include it in archive
				file_put_contents($sM3UFilename, $sM3U);
				break;
				
			default:
				throw new sbException(__CLASS__.': getDownloadItems() doesn\'t support nodetype '.$nodeSubject->getPrimaryNodeType());
				
		}
		
		return ($aItems);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function sendDownloadArchive($nodeSubject) {
		
		$sFilename = $nodeSubject->getName().'.zip';
		$aFileList = self::getDownloadItems($nodeSubject);
		
		import('sbSystem:external:pclzip/pclzip.lib');
		ini_set('max_execution_time', 6000000);
		ignore_user_abort(TRUE);
		
		// create the temporary zip archive
		$sTempFile = Registry::getValue('sb.system.temp.dir').'/'.$sFilename;
		// CAUTION: PCLZip (2.6) needs a modification because it strips away the drive letter part!!!
		$zipAlbum = new PclZip($sTempFile);
		$zipAlbum->create($aFileList, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_NO_COMPRESSION);
		
		// open the temporary zip
		$hTempFile = fopen($sTempFile, 'r');
		if (!$hTempFile) {
			die('somthing went wrong with creating the temporary zip file...');
		}
		
		// transmit the file
		$aOptions = array();
		$aOptions['filename'] = $sFilename;
		$aOptions['size'] = filesize($sTempFile);
		headers('download', $aOptions);
		$iBandwidth = Registry::getValue('sb.jukebox.downloads.maxbandwidth');
		while(!feof($hTempFile) && !connection_aborted()) {
			print fread($hTempFile, round($iBandwidth * 1024));
			sleep(1);
		}
		fclose($hTempFile);
		
		// remove temporary zip
		// NOTE: this loop is necessary because it can take some time to close the handle
		while (file_exists($sTempFile)) {
			fclose($hTempFile);
			unlink($sTempFile);
		}
		exit();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getToken() {
		$nodeUser = User::getNode();
		$stmtGetToken = $nodeUser->getSession()->prepareKnown('sbJukebox/tokens/get/byUser');
		$stmtGetToken->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtGetToken->execute();
		$sToken = FALSE;
		foreach ($stmtGetToken as $aRow) {
			$sToken = $aRow['token'];
		}
		$stmtGetToken->closeCursor();
		if (!$sToken) {
			$sToken = uuid();
			$stmtSetToken = $nodeUser->getSession()->prepareKnown('sbJukebox/tokens/create');
			$stmtSetToken->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
			$stmtSetToken->bindValue('token', $sToken, PDO::PARAM_STR);
			// TODO: parameterize token lifespan via registry
			$stmtSetToken->bindValue('lifespan', 60*60*24*30, PDO::PARAM_INT);
			$stmtSetToken->execute();
		}
		return ($sToken);
	}
	
}

?>