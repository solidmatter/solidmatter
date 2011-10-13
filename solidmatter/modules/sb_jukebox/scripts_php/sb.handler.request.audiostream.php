<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.request.tokenbased');
import('sb.tools.filesystem');
import('sbJukebox:sb.jukebox.tools');

//------------------------------------------------------------------------------
/**
*/
class JBAudioStreamHandler extends TokenBasedHandler {
	
	protected $nodeTrack = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* Request URI Format:
	* http://<site>/play/<nodeid>/<tokenid>
	* 
	* @param 
	* @return 
	*/
	public function fulfilRequest() {
		
		$this->nodeTrack = $this->crSession->getNode($this->aRequest['subject']);
		if ($this->nodeTrack->getPrimaryNodeType() != 'sbJukebox:Track') {
			$this->fail('the adressed node is not a track, it\'s a '.$this->nodeTrack->getPrimaryNodeType(), 400);
		}
		
		// TODO: check permissions
		
		$this->playTrack();
		
		exit();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function playTrack() {
		
		// init
		$sFilename = JukeboxTools::getFSPath($this->nodeTrack);
		$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
		$sSongTitle = $this->nodeTrack->getProperty('label');
		$iFilesize = filesize($sFilename);
		
		// RIPPED FROM AMPACHE
		$startArray = sscanf($_REQUEST->getServerValue('HTTP_RANGE'), 'bytes=%d-');
		$start = $startArray[0];
		
		DEBUG('Play Track: SessionID = '.sbSession::getID(), DEBUG::SESSION);
		
		// Send file, possible at a byte offset
		$hMP3 = fopen($sFilename, 'rb');
		
		if (!$hMP3) {
			System::logEvent(System::ERROR, 'sbJukebox', 'FILE_INACCESSIBLE', $sFilename, $this->nodeTrack->getProperty('jcr:uuid'));
			$this->fail('file inaccessible', 500);
		}
		if ($iFilesize === FALSE || $iFilesize == 0) {
			System::logEvent(System::ERROR, 'sbJukebox', 'FILESIZE_IS_0', $sFilename, $this->nodeTrack->getProperty('jcr:uuid'));
			$this->fail('file inaccessible', 500);
		}
		
		$this->setNowPlaying();
		$this->refreshToken();
		sbSession::close();
		
		// Prevent the script from timing out
		set_time_limit(0);
		header('Content-type: audio/mpeg');
		header("Accept-Ranges: bytes" );
		if ($start) {
			fseek($hMP3, $start);
			$range = $start ."-". $iFilesize . "/" . $iFilesize;
			header('Content-Disposition: attachment; filename='.$this->nodeTrack->getProperty('info_filename'));
			header("HTTP/1.1 206 Partial Content");
			header("Content-Range: bytes=$range");
			header("Content-Length: ".($iFilesize-$start));
		} else {
			$this->setHistory();
			header('Content-Length: '.$iFilesize);
			header('Content-Disposition: attachment; filename='.$this->nodeTrack->getProperty('info_filename'));
		}
		
		// fpassthru() causes problems with output buffering and/or buggy php versions
		//fpassthru($hMP3);
		while (!feof($hMP3)) {
			$buf = fread($hMP3, 4096);
			echo $buf;
			ob_flush();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function setNowPlaying() {
		$stmtSet = $this->crSession->prepareKnown('sbJukebox/nowPlaying/set');
		$stmtSet->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtSet->bindValue('track_uuid', $this->nodeTrack->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtSet->bindValue('playtime', $this->nodeTrack->getProperty('enc_playtime'), PDO::PARAM_INT);
		$stmtSet->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function setHistory() {
		
		// init data
		$nodeAlbum = $this->nodeTrack->getParent();
		$iTrackThreshold = Registry::getValue('sb.jukebox.history.tracks.threshold');
		$iAlbumThreshold = Registry::getValue('sb.jukebox.history.albums.threshold');
		
		// first remove tracks that obviously didn't really play...
		$stmtRemove = $this->crSession->prepareKnown('sbJukebox/history/tracks/remove');
		$stmtRemove->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtRemove->bindValue('threshold', $iTrackThreshold, PDO::PARAM_INT);
		$stmtRemove->execute();
		
		// ...then add history entry for current track
		$stmtSet = $this->crSession->prepareKnown('sbJukebox/history/tracks/set');
		$stmtSet->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtSet->bindValue('track_uuid', $this->nodeTrack->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtSet->bindValue('playtime', $this->nodeTrack->getProperty('enc_playtime'), PDO::PARAM_INT);
		$stmtSet->execute();
		
		// first check if album has already been played within threshold
		$stmtGetPlayedAlbum =  $this->crSession->prepareKnown('sbJukebox/history/albums/check');
		$stmtGetPlayedAlbum->bindValue('album_uuid', $nodeAlbum->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetPlayedAlbum->bindValue('threshold', 60*$iAlbumThreshold, PDO::PARAM_INT);
		$stmtGetPlayedAlbum->execute();
		
		// ...then add history entry for current album
		if($stmtGetPlayedAlbum->rowCount() == 0) {
			$stmtAlbumSet = $this->crSession->prepareKnown('sbJukebox/history/albums/set');
			$stmtAlbumSet->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
			$stmtAlbumSet->bindValue('album_uuid', $nodeAlbum->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtAlbumSet->execute();
		}
	}
	
}

?>