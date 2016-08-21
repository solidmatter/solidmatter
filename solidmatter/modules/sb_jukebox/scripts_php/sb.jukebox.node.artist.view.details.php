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
class sbView_jukebox_artist_details extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		'display' => array('read'),
		'getM3U' => array('read'),
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'display':
				
				// forms
				$this->addSearchForm('artists');
				$this->addCommentForm();
				$this->addTagForm();
				$this->addRelateForm();
				
				// data
				$this->addComments();
				$this->nodeSubject->getTags();
				$this->nodeSubject->storeRelations();
				$this->nodeSubject->getVote($this->getPivotUUID());
				
				// add albums
				$niAlbums = $this->nodeSubject->loadChildren('albums', TRUE, TRUE, TRUE);
				// TODO: find a less dirty hack to control sorting
				if ($this->nodeSubject->getName() == 'Various_Artists' || $this->nodeSubject->getName() == 'Soundtrack') {
					$niAlbums->sortAscending('label');
				} else {
					$niAlbums->sortAscending('info_published');
				}					
				foreach ($niAlbums as $nodeAlbum) {
					$nodeAlbum->setAttribute('vote', $nodeAlbum->getVote($this->getPivotUUID()));
				}
				
				// optionally check if files still exist
				if (Registry::getValue('sb.jukebox.validation.missingfiles.indicate')) {
					import('sbJukebox:sb.jukebox.tools');
					foreach ($niAlbums as $nodeAlbum) {
						if (!$nodeAlbum->checkFileExistance()) {
							$nodeAlbum->setAttribute('missing', 'TRUE');
						}
					}
				}
				
				// add tracks
				$stmtGetTitles = $this->crSession->prepareKnown('sbJukebox/artist/getTracks/differentAlbums');
				$stmtGetTitles->bindValue('jukebox_mpath', $this->getJukebox()->getMPath(), PDO::PARAM_STR);
				$stmtGetTitles->bindValue('artist_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetTitles->bindValue('limit', 100, PDO::PARAM_INT);
				$stmtGetTitles->execute();
				$_RESPONSE->addData($stmtGetTitles->fetchElements(), 'tracks');
				
				// add songkick concert info
				$sArtistSongkickID = $this->nodeSubject->getProperty('songkick_id');
				if (Registry::getValue('sb.jukebox.songkick.enabled')) {
					try {
						if ($sArtistSongkickID == NULL) {
							$this->storeSongkickArtistInfo();
						} else {
							$this->storeSongkickConcertInfo();
						}
					} catch (SongkickException $e) {
						$_RESPONSE->addData($e->getMessage(), 'sk_exception');
					}
				}
				
				// save data in element
				$this->nodeSubject->storeChildren();
				break;
				
			case 'getM3U':
				$this->sendPlaylist();
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
	public function storeSongkickArtistInfo() {
		if ($domResponse = $this->nodeSubject->getSongkickArtistInfo()) {
			global $_RESPONSE;
			$_RESPONSE->addData($domResponse, 'sk_artists');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function storeSongkickConcertInfo() {
		if ($domResponse = $this->nodeSubject->getSongkickConcertInfo()) {
			global $_RESPONSE;
			$_RESPONSE->addData($domResponse, 'sk_concerts');
		}
	}
	
}




?>