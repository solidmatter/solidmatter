<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb_jukebox:sb.pdo.queries');

//------------------------------------------------------------------------------
/**
*/
class sbNode_jukebox_track extends sbNode {
	
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
		
		$nodeAlbum = $this->getParent();
		$nodeJukebox = $nodeAlbum->getParent()->getParent();
		
		$sRealPath = $nodeJukebox->getProperty('config_realpath').$nodeAlbum->getProperty('info_relpath').$this->getProperty('info_filename');
		
		return ($sRealPath);
		
	}
	
}

?>