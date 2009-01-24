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
class sbNode_jukebox_album extends sbJukeboxNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbJukebox/album/properties/load/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbJukebox/album/properties/save/auxiliary';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function modifyForm($formCurrent, $sMode) {
		$nodeJukebox = $this->getParent()->getParent();
		$this->fillArtists($formCurrent, $nodeJukebox);
	}
	
}

?>