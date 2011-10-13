<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbJukebox:sb.jukebox.tools');

//------------------------------------------------------------------------------
/**
*/
class sbJukeboxNode extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function fillArtists($formCurrent, $nodeJukebox) {
		
		$aArtists = JukeboxTools::getAllArtists($nodeJukebox);
		$formCurrent->setOptions('info_artist', $aArtists);
		
	}
	
	public function getJukebox() {
		
		if ($this->getPrimaryNodeType() == 'sbJukebox:Jukebox') {
			return ($this);
		} else {
			return ($this->getAncestorOfType('sbJukebox:Jukebox'));
		}
		
	}
	
}

?>