<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_jukebox_administration extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		'info' => array('write'),
		'startImport' => array('write'),
		'clearLibrary' => array('write'),
		'clearQuilts' => array('write'),
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'info':
				//$this->storeLibraryInfo();
				break;
			
			case 'startImport':
				
				if ($_REQUEST->getParam('dry') != 'true') {
					$this->logEvent(System::MAINTENANCE, 'IMPORT_STARTED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
				}
				
				import('sb_jukebox:sb.tools.import.library.default');
				$ihCurrentImportHandler = new DefaultJukeboxImporter($this->nodeSubject);
				$ihCurrentImportHandler->startImport();
				
				if ($_REQUEST->getParam('dry') != 'true') {
					$this->logEvent(System::MAINTENANCE, 'IMPORT_ENDED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
					$this->clearLibraryInfo();
				}
				
				break;
				
			case 'clearLibrary':
				
				$nlChildren = $this->nodeSubject->getNodes();
				
				//$this->nodeSubject->getSession()->beginTransaction('sbJukebox::clearLibrary');
				
				foreach ($nlChildren as $nodeChild) {
					if ($nodeChild->getPrimaryNodeType() == 'sb_jukebox:album') {
						$nodeChild->remove();
						$nodeChild->save();
					}
				}
				
				foreach ($nlChildren as $nodeChild) {
					if ($nodeChild->getPrimaryNodeType() == 'sb_jukebox:artist') {
						$nodeChild->remove();
						$nodeChild->save();
					}
				}
				
				//$this->nodeSubject->getSession()->save();
				
				import('sb.tools.filesystem.directory');
				import('sb.tools.strings.conversion');
				
				$dirAlbums = new sbDirectory($this->nodeSubject->getProperty('config_sourcepath'));
				foreach ($dirAlbums->getDirectories(TRUE) as $dirAlbum) {
					$fileInfo = $dirAlbum->getFile('sbJukebox.txt');
					if ($fileInfo) {
						$fileInfo->delete();
					}
				}
				
				$this->clearLibraryInfo();
				
				//$this->nodeSubject->getSession()->commit('sbJukebox::clearLibrary');
				
				break;
				
			case 'clearQuilts':
				$cacheQuilts = CacheFactory::getInstance('misc');
				$cacheQuilts->clear('JBQUILT:');
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}

?>