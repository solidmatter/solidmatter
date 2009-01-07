<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_track_song extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		global $_REQUEST;
		
		switch ($sAction) {
			
			case 'play':
				
				$nodeAlbum = $this->nodeSubject->getParent();
				$nodeArtist = $nodeAlbum->getParent();
				$nodeJukebox = $nodeArtist->getParent();
				$sFilename = $nodeJukebox->getProperty('config_sourcepath');
				$sFilename = normalize_path($sFilename);
				$sFilename .= $nodeAlbum->getProperty('info_relpath');
				$sFilename .= $this->nodeSubject->getProperty('info_filename');
				$sFilename = iconv('UTF-8', 'Windows-1252', $sFilename);
				
				$sSongTitle = $this->nodeSubject->getProperty('label');
				$iFilesize = filesize($sFilename);
				
				// RIPPED FROM AMPACHE
				$startArray = sscanf($_REQUEST->getServerValue('HTTP_RANGE'), 'bytes=%d-');
				$start = $startArray[0];
				
				DEBUG('Play Track: SessionID', sbSession::getSessionID(), DEBUG::SESSIONID);
				
				header("Accept-Ranges: bytes" );
				
				// Prevent the script from timing out
				set_time_limit(0);
				
				// Send file, possible at a byte offset
				$hMP3 = fopen($sFilename, 'rb');
				
				if (!$hMP3) {
					$this->logEvent(System::ERROR, 'FILE_INACCESSIBLE', $sFilename);
					var_dumpp($sFilename);
					die ('file inaccessible');
				}
				
				$this->setNowPlaying();
				
				
				sbSession::disableStoring();
				
				header('Content-type: audio/mpeg');
				
				if ($start) {
					header('Content-Disposition: attachment; filename='.$this->nodeSubject->getProperty('info_filename'));
					fseek($hMP3, $start);
					$range = $start ."-". $iFilesize . "/" . $iFilesize;
					header("HTTP/1.1 206 Partial Content");
					header("Content-Range: bytes=$range");
					header("Content-Length: ".($iFilesize-$start));
				} else {
					$this->setHistory();
					header('Content-Length: '.$iFilesize);
					header('Content-Disposition: attachment; filename='.$this->nodeSubject->getProperty('info_filename'));
				}
				
				fpassthru($hMP3);
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
	public function setNowPlaying() {
		$stmtSet = $this->crSession->prepareKnown('sbJukebox/nowPlaying/set');
		$stmtSet->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtSet->bindValue('track_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtSet->bindValue('playtime', $this->nodeSubject->getProperty('enc_playtime'), PDO::PARAM_INT);
		$stmtSet->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setHistory() {
		$stmtSet = $this->crSession->prepareKnown('sbJukebox/history/set');
		$stmtSet->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtSet->bindValue('track_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtSet->bindValue('playtime', $this->nodeSubject->getProperty('enc_playtime'), PDO::PARAM_INT);
		$stmtSet->execute();
	}
	
}

?>