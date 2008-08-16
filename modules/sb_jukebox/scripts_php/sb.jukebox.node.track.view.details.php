<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');
import('sb.tools.mime');

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_track_details extends sbJukeboxView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				// search form
				$formSearch = $this->buildSearchForm('tracks');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				// comment form
				$formComment = $this->buildCommentForm();
				$formComment->saveDOM();
				$_RESPONSE->addData($formComment);
				
				// add comments
				$niComments = $this->nodeSubject->loadChildren('comments', TRUE, TRUE, TRUE);
				foreach ($niComments as $nodeComment) {
					// TODO: check user existence, might be deleted
					$nodeUser = $this->crSession->getNodeByIdentifier($nodeComment->getProperty('jcr:createdBy'));
					$nodeComment->setAttribute('username', $nodeUser->getProperty('label'));
				}
				$this->nodeSubject->storeChildren();
				
				$this->nodeSubject->loadProperties();
				$this->nodeSubject->getTags();
				
				// add vote
				$this->nodeSubject->getVote(User::getUUID());
				
				return;
				
			case 'getCover':
				$nodeAlbum = $this->nodeSubject->getParent();
				parent::getCover($nodeAlbum);
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