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
class sbView_jukebox_artist_concerts extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		global $_REQUEST;
		
		$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'overview':
				$this->execute('searchArtistOnSongkick');
				$this->execute('searchConcertsOnSongkick');
				break;
				
			case 'searchArtistOnSongkick':
				$_RESPONSE->addData($this->nodeSubject->getSongkickArtistInfo(), 'sk_artists');
				break;
				
			case 'searchConcertsOnSongkick':
				if ($this->nodeSubject->getProperty('songkick_id') != NULL) {
					$_RESPONSE->addData($this->nodeSubject->getSongkickConcertInfo(), 'sk_concerts');
				}
				break;
			
			case 'linkToSongkick':
				$sSongkickID = $_REQUEST->getParam('songkick_id');
				$this->nodeSubject->setProperty('songkick_id', $sSongkickID);
				$this->nodeSubject->save();
				if ($_REQUEST->getHandler() == 'backend') {
					$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'concerts');
				} else {
					$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				}
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}

?>