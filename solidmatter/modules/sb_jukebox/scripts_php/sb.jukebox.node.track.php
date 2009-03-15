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
	* TODO: RealPath does not work with imports done via multiple jukebox paths
	* @param 
	* @return 
	*/
	public function getRealPath() {
		
		$nodeAlbum = $this->getParent();
		$nodeJukebox = $nodeAlbum->getParent()->getParent();
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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkFileExistance() {
		$sFilename = JukeboxTools::getFSPath($this);
		$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
		return (file_exists($sFilename));
	}
	
}

?>