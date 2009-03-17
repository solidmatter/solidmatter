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
				if (User::isAuthorised('comment', $this->nodeSubject)) {
					$formComment = $this->buildCommentForm();
					$formComment->saveDOM();
					$_RESPONSE->addData($formComment);
				}
				
				// tag form
				if (User::isAuthorised('tag', $this->nodeSubject)) {
					$formTag = $this->buildTagForm();
					$formTag->saveDOM();
					$_RESPONSE->addData($formTag);
				}
				
				// EXPERIMENTAL
				if (true) {
					$formRelate = $this->buildRelateForm();
					$formRelate->saveDOM();
					$_RESPONSE->addData($formRelate);
				}
				
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
				
				// add relations
				$this->nodeSubject->storeRelations();
				
				// store track artist
				$nodeArtist = $this->crSession->getNodeByIdentifier($this->nodeSubject->getProperty('info_artist'));
				$_RESPONSE->addData($nodeArtist, 'track_artist');
				return;
				
			case 'getCover':
				import('sbJukebox:sb.jukebox.tools');
				JukeboxTools::sendCover($this->nodeSubject->getParent());
				break;
				
			case 'getM3U':
				$this->sendPlaylist();
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
}

?>