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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
			
				// search form
				$formSearch = $this->buildSearchForm('artists');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				// comment form
				$formComment = $this->buildCommentForm();
				$formComment->saveDOM();
				$_RESPONSE->addData($formComment);
				
				// add albums & comments
				$niAlbums = $this->nodeSubject->loadChildren('albums', TRUE, TRUE, TRUE);
				foreach ($niAlbums as $nodeAlbum) {
					// TODO: show votes?
					$nodeAlbum->setAttribute('vote', $nodeAlbum->getVote($this->getPivotUUID()));
				}
				$niComments = $this->nodeSubject->loadChildren('comments', TRUE, TRUE, TRUE);
				foreach ($niComments as $nodeComment) {
					// TODO: check user existence, might be deleted
					$nodeUser = $this->crSession->getNodeByIdentifier($nodeComment->getProperty('jcr:createdBy'));
					$nodeComment->setAttribute('username', $nodeUser->getProperty('label'));
				}
				$this->nodeSubject->storeChildren();
				
				// add tracks
				$stmtGetTitles = $this->crSession->prepareKnown('sbJukebox/artist/getTracks/differentAlbums');
				$stmtGetTitles->bindValue('jukebox_uuid', $this->getJukebox()->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetTitles->bindValue('artist_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetTitles->bindValue('limit', 100, PDO::PARAM_INT);
				$stmtGetTitles->execute();
				$_RESPONSE->addData($stmtGetTitles->fetchElements(), 'tracks');
				
				// add vote
				$this->nodeSubject->getVote(User::getUUID());
				
				break;
				
			case 'getM3U':
				$sName = $this->nodeSubject->getProperty('name');
				$sPlaylist = $this->getPlaylist($this->nodeSubject);
				headers('m3u', array(
					'filename' => $sName.'.m3u',
					'download' => false,
					'size' => strlen($sPlaylist),
				));
				echo $sPlaylist;
				exit();
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}

?>