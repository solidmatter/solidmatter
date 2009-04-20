<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');
import('sbJukebox:sb.jukebox.tools');

//------------------------------------------------------------------------------
/**
*/
class JBAudioStreamHandler {
	
	protected $crSession = NULL;
	protected $nodeTrack = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		$this->crSession = $crSession;
		$sNodeID = NULL;
		$sTokenID = NULL;
		
		// parse request
		$aStuff = explode('/', $_REQUEST->getPath(), 5);
		if (isset($aStuff[1])) {
			// fixed: "play"
		}
		if (isset($aStuff[2])) {
			$sNodeID = $aStuff[2];
		}
		if (isset($aStuff[3])) {
			$sTokenID = $aStuff[3];
		}
		if ($sNodeID === NULL || $sTokenID === NULL) {
			die('nodeid or token missing');
		}
		
		$sUserID = $this->getTokenOwner($sTokenID);
		if (!$sUserID) {
			die('token is invalid');
		}
		User::setUUID($sUserID);
		
		$this->nodeTrack = $crSession->getNode($sNodeID);
		if ($this->nodeTrack->getPrimaryNodeType() != 'sbJukebox:Track') {
			die('the adressed node is not a track');
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
			logEvent(System::ERROR, 'sbJukebox', 'FILE_INACCESSIBLE', $sFilename, $this->nodeTrack->getProperty('jcr:uuid'));
			var_dumpp($sFilename);
			die ('file inaccessible');
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
		
		// fpassthru() causse problems with output buffering and/or buggy php versions
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
		// first remove tracks that obviously didn't really play...
		$stmtRemove = $this->crSession->prepareKnown('sbJukebox/history/remove');
		$stmtRemove->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		// TODO: make threshold configurable via registry
		$stmtRemove->bindValue('threshold', 60, PDO::PARAM_INT);
		$stmtRemove->execute();
		// ...then add history entry
		$stmtSet = $this->crSession->prepareKnown('sbJukebox/history/set');
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
	protected function getTokenOwner($sTokenID) {
		$stmtClear = $this->crSession->prepareKnown('sbJukebox/tokens/clear');
		$stmtClear->execute();
		$stmtGetOwner = $this->crSession->prepareKnown('sbJukebox/tokens/get/byToken');
		$stmtGetOwner->bindValue('token', $sTokenID, PDO::PARAM_STR);
		$stmtGetOwner->execute();
		$sUserUUID = FALSE;
		foreach ($stmtGetOwner as $aRow) {
			$sUserUUID = $aRow['user_uuid'];
		}
		return ($sUserUUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function refreshToken() {
		$stmtRefresh = $this->crSession->prepareKnown('sbJukebox/tokens/refresh');
		$stmtRefresh->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtRefresh->execute();
	}
	
}

?>