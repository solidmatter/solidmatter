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
				
				// forms
				$this->addSearchForm('tracks');
				$this->addCommentForm();
				$this->addTagForm();
				$this->addRelateForm();
				
				// data
				$this->addComments();
				$this->nodeSubject->loadProperties();
				$this->nodeSubject->getTags();
				$this->nodeSubject->getVote($this->getPivotUUID());
				$this->nodeSubject->storeRelations();
				
				// store track artist
				$nodeArtist = $this->crSession->getNodeByIdentifier($this->nodeSubject->getProperty('info_artist'));
				$_RESPONSE->addData($nodeArtist, 'track_artist');
				
				// save data in element
				$this->nodeSubject->storeChildren();
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