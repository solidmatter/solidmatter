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
		
		$aTracks = array();
		switch ($nodeItem->getPrimaryNodeType()) {
			case 'sbJukebox:Track':
				$aTracks[] = $nodeItem;
				break;
			case 'sbJukebox:Album':
				$niTracks = $nodeItem->getChildren('play');
				foreach ($niTracks as $nodeTrack) {
					$aTracks[] = $nodeTrack;
				}
				break;
			default:
				throw new sbException('You can only add Albums and Tracks right now');
				break;
		}
		
		foreach ($aTracks as $nodeTrack) {
			$this->addExistingNode($nodeTrack);
		}
		$this->save();
		
	}
	
	
}

?>