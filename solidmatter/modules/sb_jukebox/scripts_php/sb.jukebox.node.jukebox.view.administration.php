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
				
				$this->logEvent(System::MAINTENANCE, 'CLEAR_STARTED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
				
				$nlChildren = $this->nodeSubject->getNodes();
				
				//$this->nodeSubject->getSession()->beginTransaction('sbJukebox::clearLibrary');
				
				foreach ($nlChildren as $nodeChild) {
					if ($nodeChild->getPrimaryNodeType() == 'sbJukebox:Album') {
						$nodeChild->remove();
						$nodeChild->save();
					}
				}
				
				foreach ($nlChildren as $nodeChild) {
					if ($nodeChild->getPrimaryNodeType() == 'sbJukebox:Artist') {
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
				
				$this->logEvent(System::MAINTENANCE, 'CLEAR_ENDED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
				
				//$this->nodeSubject->getSession()->commit('sbJukebox::clearLibrary');
				
				break;
				
			case 'clearQuilts':
				$cacheQuilts = CacheFactory::getInstance('misc');
				$cacheQuilts->clear('JBQUILT:');
				break;
				
			case 'storeUGC':
				import('sbJukebox:sb.jukebox.tools');
				$this->logEvent(System::MAINTENANCE, 'STORE_UGC_STARTED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
				echo 'Preparing...<br>';
				$niArtists = $this->nodeSubject->getChildren('storeUGC');
				$iLimit = 10000;
				foreach ($niArtists as $nodeArtist) {
					echo 'Artist: '.$nodeArtist->getProperty('label').'<br>';
					$niAlbums = $nodeArtist->getChildren('storeUGC');
					foreach ($niAlbums as $nodeAlbum) {
						echo 'Album: '.$nodeAlbum->getProperty('label').'<br>';
						$this->storeUGC($nodeAlbum);
						if ($iLimit-- < 1) {
							die('Limit reached');	
						}
					}
				}
				$this->logEvent(System::MAINTENANCE, 'STORE_UGC_ENDED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
				exit();
				break;
				
			case 'removeUGC':
				import('sbJukebox:sb.jukebox.tools');
				$this->logEvent(System::MAINTENANCE, 'REMOVE_UGC_STARTED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
				echo 'Preparing...';
				$niArtists = $this->nodeSubject->getChildren('storeUGC');
				foreach ($niArtists as $nodeArtist) {
					echo 'Artist: '.$nodeArtist->getProperty('label').'<br>';
					$niAlbums = $nodeArtist->getChildren('storeUGC');
					foreach ($niAlbums as $nodeAlbum) {
						echo 'Album: '.$nodeAlbum->getProperty('label').'<br>';
						$this->removeUGC($nodeAlbum);
					}
				}
				$this->logEvent(System::MAINTENANCE, 'REMOVE_UGC_ENDED', 'library path: '.$this->nodeSubject->getProperty('config_sourcepath'));
				exit();
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Stores all user generated content in album directory
	* @param 
	* @return 
	*/
	public function storeUGC($nodeCurrent) {
		
		$domUGC = new sbDOMDocument();
		$domUGC->appendChild($this->convertUGC($domUGC, $nodeCurrent));
		
		$sFilename = JukeboxTools::getFSPath($nodeCurrent).'sbUGC.xml';
		
		$domUGC->save($sFilename);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Stores all user generated content in album directory
	* @param 
	* @return 
	*/
	public function removeUGC($nodeCurrent) {
		
		$sFilename = JukeboxTools::getFSPath($nodeCurrent).'sbUGC.xml';
		if (file_exists($sFilename)) {
			unlink($sFilename);	
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Stores all user generated content in album directory
	* @param 
	* @return 
	*/
	protected function convertUGC($domUGC, $nodeCurrent) {
		
		$elemCurrent = $domUGC->createElement('ugc');
		$elemCurrent->setAttribute('uuid', $nodeCurrent->getProperty('jcr:uuid'));
		$elemCurrent->setAttribute('nodetype', $nodeCurrent->getPrimaryNodeType());
		$elemCurrent->setAttribute('name', $nodeCurrent->getProperty('name'));
		if ($nodeCurrent->getPrimaryNodeType() == 'sbJukebox:Track') {
			$elemCurrent->setAttribute('track', $nodeCurrent->getProperty('info_index'));	
		}
		
		$aVotes = $nodeCurrent->getVotes();
		$aTags = $nodeCurrent->getTags();
		$niComments = $nodeCurrent->getChildren('comments');
		$niChildren = $nodeCurrent->getChildren('storeUGC');
		
		foreach ($aVotes as $aVote) {
			// TODO: less hardcode the rootnode id?
			if ($aVote['fk_user'] == '00000000000000000000000000000000') {
				continue;
			}
			$elemVote = $domUGC->createElement('vote');
			$elemVote->setAttribute('voter', $aVote['fk_user']);
			$elemVote->setAttribute('vote', $aVote['n_vote']);
			$elemCurrent->appendChild($elemVote);
		}
		
		$aSkippables = array();
		foreach ($aTags as $sTag) {
			if ($nodeCurrent->getPrimaryNodeType() == 'sbJukebox:Track') {
				$aSkippables[] = $nodeCurrent->getProperty('enc_bitrate').'kbs';
				$aSkippables[] = $nodeCurrent->getProperty('enc_mode');
			}
			if ($nodeCurrent->getPrimaryNodeType() == 'sbJukebox:Album') {
				$aSkippables[] = $nodeCurrent->getProperty('info_published');
			}
			//var_dumpp($aSkippables);
			if (!in_array($sTag, $aSkippables)) {
				$elemTag = $domUGC->createElement('tag');
				$elemTag->setAttribute('title', $sTag);
				$elemCurrent->appendChild($elemTag);
			}
		}
		
		foreach ($niComments as $nodeComment) {
			$elemComment = $domUGC->createElement('comment');
			$elemComment->setAttribute('creator', $nodeComment->getProperty('jcr:createdBy'));
			$elemComment->setAttribute('date', $nodeComment->getProperty('jcr:created'));
			$elemComment->nodeValue = $nodeComment->getProperty('comment');
			$elemCurrent->appendChild($elemComment);
		}
		
		foreach ($niChildren as $nodeChild) {
			$elemCurrent->appendChild($this->convertUGC($domUGC, $nodeChild));	
		}
		
		return ($elemCurrent);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clearLibraryInfo() {
		
		$nodeJukebox = $this->getJukebox();
		
		$sCacheKey = 'JBINFO:'.$this->nodeSubject->getProperty('jcr:uuid');
		$cacheData = CacheFactory::getInstance('misc');
		$cacheData->clear($sCacheKey);
	
	}
	
}

?>