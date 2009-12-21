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
				$this->addLyricsForm();
				
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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function addLyricsForm() {
		
		if (!User::isAuthorised('edit_lyrics', $this->nodeSubject)) {
			return (FALSE);
		}
		
		$formLyrics = new sbDOMForm(
			'editLyrics',
			'$locale/sbJukebox/labels/edit_lyrics',
			System::getRequestURL($this->nodeSubject, 'votes', 'saveLyrics'),
			$this->crSession
		);
		
		$formLyrics->addInput('lyrics;text;minlength=3;columns=100;rows=30;maxlength=10000;', '$locale/sbSystem/labels/comment');
		$formLyrics->addSubmit('$locale/sbSystem/actions/save');
		
		$formLyrics->setValue('lyrics', $this->nodeSubject->getProperty('info_lyrics'));
		
		$formLyrics->saveDOM();
		global $_RESPONSE;
		$_RESPONSE->addData($formLyrics);
		
	}
	
}

?>