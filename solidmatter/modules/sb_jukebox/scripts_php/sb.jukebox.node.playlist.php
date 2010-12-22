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
class sbNode_jukebox_playlist extends sbJukeboxNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clear() {
		
		$niTracks = $this->getNodes();
		foreach ($niTracks as $nodeTrack) {
			$this->removeItem($nodeTrack);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function removeItem($nodeItem) {
		
		foreach ($nodeItem->getSharedSet() as $nodeShared) {
			if ($nodeShared->getParent()->isSame($this)) {
				$nodeShared->removeShare();
				$this->crSession->save();
				return (TRUE);
			}
		}
		return (FALSE);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function moveItem($nodeItem, $nodeTarget) {
		
		$this->crSession->beginTransaction('MOVE_ITEM');
		$nodeTarget->addItem($nodeItem);
		$this->removeItem($nodeItem);
		$this->crSession->commit('MOVE_ITEM');
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addItem($nodeItem) {
		
		$aItems = array();
		switch ($nodeItem->getPrimaryNodeType()) {
			case 'sbJukebox:Track':
			case 'sbJukebox:Album':
			case 'sbJukebox:Playlist':
				$aItems[] = $nodeItem;
				break;
			default:
				throw new sbException('You can only add Albums, Tracks and Playlists right now');
				break;
		}
		
		foreach ($aItems as $nodeItem) {
			$this->addExistingNode($nodeItem);
		}
		$this->save();
		
	}
	
	
}

?>