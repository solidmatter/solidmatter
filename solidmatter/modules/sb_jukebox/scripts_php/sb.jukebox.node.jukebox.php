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
class sbNode_jukebox_jukebox extends sbJukeboxNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadChildren']['byMode'] = 'sbCR/node/loadChildren/mode/standard/byNodetypeAndLabel';
	}
	
	//--------------------------------------------------------------------------
	/**
	* Overrides parent method to adjust view dependent on handler.
	* @return string 'properties' if in backend handler, otherwise the nodetype default
	*/
	protected function getDefaultView() {
		if ($_REQUEST->getHandler() == 'backend') {
			return ('properties');	
		} else {
			return (parent::getDefaultView());	
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function getFavoritesNode() {
		$nodeUser = User::getNode();
		$sJukeboxUUID = $this->getProperty('jcr:uuid');
		if ($nodeUser->hasNode($sJukeboxUUID)) {
			$nodeFavorites = $nodeUser->getNode($sJukeboxUUID);
		} else {
			$nodeFavorites = $nodeUser->addNode($sJukeboxUUID, 'sbJukebox:Playlist');
			$nodeFavorites->setProperty('label', 'Favorites');
			$nodeUser->save();
		}
		return ($nodeFavorites);
	}
		
}

?>