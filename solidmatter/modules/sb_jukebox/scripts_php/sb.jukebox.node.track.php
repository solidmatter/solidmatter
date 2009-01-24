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
class sbNode_jukebox_track extends sbJukeboxNode {
	
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbJukebox/track/properties/load/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbJukebox/track/properties/save/auxiliary';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getRealPath() {
		
		$nodeJukebox = $this->getParent()->getParent()->getParent();
		$sRealPath = $nodeJukebox->getProperty('config_realpath').$nodeAlbum->getProperty('info_relpath').$this->getProperty('info_filename');
		
		return ($sRealPath);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function modifyForm($formCurrent, $sMode) {
		$nodeJukebox = $this->getParent()->getParent()->getParent();
		$this->fillArtists($formCurrent, $nodeJukebox);
	}
	
}

?>