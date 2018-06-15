<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_various_playlist extends sbJukeboxView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		$bShuffle = FALSE;
		if ($_REQUEST->getParam('shuffle') == 'true') {
			$bShuffle = TRUE;
		}
		
		$nodeSubject = $this->nodeSubject;
		if ($this->nodeSubject->getPrimaryNodetype() == 'sbJukebox:Jukebox') {
			$nodeSubject = $this->nodeSubject->getFavoritesNode();
		}
		
		switch ($sAction) {
			
			case 'getM3U':
				
				$sName = $this->nodeSubject->getProperty('name');
				$sPlaylist = JukeboxTools::getPlaylist($this->getJukebox(), $nodeSubject, $bShuffle, 'M3U');
				headers('m3u', array(
						'filename' => $sName.'.m3u',
						'download' => false,
						'size' => strlen($sPlaylist),
				));
				echo $sPlaylist;
				exit();
				
				break;
				
			case 'openPlayer':
				
				$aPlaylistItems = JukeboxTools::getPlaylist($this->getJukebox(), $nodeSubject, $bShuffle, 'ARRAY');
				
// 				$_RESPONSE->addData($nodeSubject, 'subject');
				$_RESPONSE->addData($aPlaylistItems, 'playlist');
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
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
		} elseif ($sFormat == 'ARRAY') {
			$aOutput = array();
			foreach ($aPlaylistItems as $aItem) {
				$aOutputItem = array();
				$aOutputItem['uuid'] = $aItem['uuid'];
				$aOutputItem['label'] = $aItem['label'];
				$aOutputItem['playtime'] = $aItem['playtime'];
				$aOutputItem['url'] = 'http://'.$_REQUEST->getDomain().'/play/'.$aItem['uuid'].'/'.$sToken.'/'.str2urlsafe($aItem['label'], TRUE, TRUE).".mp3";
				$aOutput[] = $aOutputItem;
			}
			return ($aOutput);
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
	/*protected function getPlaylistNodes($nodeSubject = NULL, $aCyclePrevention = array()) {
		
		$niPlaylist = new NodeIterator();
		
		$nodeJukebox = $this->getJukebox();
		if ($nodeSubject == NULL) {
			$nodeSubject = $this->nodeSubject;
		}
		
		
		if ($nodeSubject->getPrimaryNodeType() == 'sbJukebox:Track') {
			$niPlaylist->append($nodeSubject);
			$sTrackUUID = $nodeSubject->getIdentifier();
			$aItems[$sTrackUUID]['uuid'] = $sTrackUUID;
			$aItems[$sTrackUUID]['label'] = $nodeSubject->getProperty('label');
			$aItems[$sTrackUUID]['playtime'] = $nodeSubject->getProperty('enc_playtime');
		}
		
// 		protected static function getPlaylistNodes($nodeJukebox, $nodeSubject, $aCyclePrevention = array()) {
			
// 			$aItems = array();
			
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
		
	}*/
	
	
	
}

?>