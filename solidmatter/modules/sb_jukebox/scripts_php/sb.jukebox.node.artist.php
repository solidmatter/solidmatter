<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbJukebox:sb.jukebox.node');

//------------------------------------------------------------------------------
/**
*/
class sbNode_jukebox_artist extends sbJukeboxNode {
	
	protected $niTracksOnDifferentAlbums = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbJukebox/album/properties/load/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbJukebox/album/properties/save/auxiliary';
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: support the flag parameter, currently it is always assumed TRUE
	* @param 
	* @return 
	*/
	public function getTracks($bDifferentAlbumsOnly = TRUE) {
		
		if ($this->niTracksOnDifferentAlbums != NULL) {
			return ($this->niTracksOnDifferentAlbums);
		}
		
		$nodeJukebox = $this->getParent();
		$stmtGetTracks = $this->crSession->prepareKnown('sbJukebox/artist/getTracks/differentAlbums');
		$stmtGetTracks->bindValue('jukebox_mpath', $nodeJukebox->getMPath(), PDO::PARAM_STR);
		$stmtGetTracks->bindValue('artist_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetTracks->bindValue('limit', 100, PDO::PARAM_INT);
		$stmtGetTracks->execute();
		
		$aTracks = array();
		foreach ($stmtGetTracks as $aRow) {
			$aTracks[] = $this->crSession->getNodeByIdentifier($aRow['uuid']);
		}
		$this->niTracksOnDifferentAlbums = new sbCR_NodeIterator($aTracks);
		
		return ($this->niTracksOnDifferentAlbums);
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: support the flag parameter, currently it is always assumed TRUE
	* @param 
	*/
	public function storeTracks() {
		
		if ($this->niTracksOnDifferentAlbums == NULL) {
			$this->getTracks();
		}
		
		$this->storeNodeList($this->niTracksOnDifferentAlbums, TRUE, 'tracks');
				
	}
	
}

?>