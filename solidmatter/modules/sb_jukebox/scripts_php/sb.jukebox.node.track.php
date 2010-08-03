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
	
	protected $aGetID3Info = NULL;
	
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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTitle() {
		return (substr($this->getProperty('label'), strpos($this->getProperty('label'), ' - ') + 3));
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: currently only supports IDv2 and relies on this tag to be present
	* @param 
	* @return 
	*/
	public function getTag($sFrameName) {
		
		if ($this->aGetID3Info == NULL) {
		
			import('sbSystem:external:getid3/getid3');
			
			$sFilename = JukeboxTools::getFSPath($this);
			$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
			
			// get track info through getid3
			$oGetID3 = new getid3();
			$oGetID3 = new getid3(); // instatiate twice because of strange heplerapps bug in getid3!
			error_reporting(0);
			$this->aGetID3Info = $oGetID3->analyze($sFilename);
			error_reporting(E_STRICT | E_ALL);
			
		}
		//var_dumpp($aInfo);exit();
		
		return (iconv($this->aGetID3Info['id3v2'][$sFrameName][0]['encoding'], 'UTF-8', $this->aGetID3Info['id3v2'][$sFrameName][0]['data']));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: currently only supports IDv2 and relies on this tag to be present
	* @param 
	* @return 
	*/
	public function setTag($sTagName) {
		
		/*import('sbSystem:external:getid3/getid3');
		
		$sFilename = JukeboxTools::getFSPath($this);
		$sFilename = iconv('UTF-8', System::getFilesystemEncoding(), $sFilename);
		
		// get track info through getid3
		$oGetID3 = new getid3();
		$oGetID3 = new getid3(); // instantiate twice because of strange heplerapps bug in getid3!
		$aInfo = $oGetID3->analyze($sFilename);
		var_dumpp($aInfo);exit();
		return ($aInfo['tags']['id3v2'][$sTagName]);*/
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getFile() {
		import('sbSystem:sb.tools.filesystem.file');
		return (new sbFile(JukeboxTools::getFSPath($this)));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDirectory() {
		
		
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function moveFile($dirTargetDirectory) {
		
		
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function renameFile($sNewName) {
		$fileMP3 = $this->getFile();
		$fileMP3->rename($sNewName);
	}
	
}

?>