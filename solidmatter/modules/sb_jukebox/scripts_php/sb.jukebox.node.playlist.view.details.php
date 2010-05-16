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
class sbView_jukebox_playlist_details extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		'display' => array('read'),
		'addItem' => array('add_titles'),
		'removeItem' => array('write'),
		'copyItems' => array('read'), // has to be checked on target playlist
		'moveItems' => array('write'), // has to be checked on target playlist, too
		'removeItems' => array('write'),
		'clear' => array('write'),
		'orderBefore' => array('write'),
		'activate' => array('add_titles'),
		'getM3U' => array('read'),
		'importM3U' => array('write'),
		'download' => array('download'),
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
			
			case 'display':
				
				// forms
				$this->addSearchForm('artists');
				$this->addCommentForm();
				// tags and relations deactivated
				//$this->addTagForm();
				//$this->addRelateForm();
				$formImport = $this->buildImportForm();
				$formImport->saveDOM();
				$_RESPONSE->addData($formImport);
				
				// data
				$this->addComments();
				$this->nodeSubject->getVote(User::getUUID());
				
				// add tracks
				$niTracks = $this->nodeSubject->loadChildren('tracks', TRUE, TRUE, FALSE);
				foreach ($niTracks as $nodeTrack) {
					$nodeTrack->getVote($this->getPivotUUID());
				}
				
				// save data in element
				$this->nodeSubject->storeChildren();
				break;
			
			case 'search':
				throw new LazyBastardException('searching playlists not implemented yet');
				break;
			
			case 'addItem':
				$nodeItem = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('item'));
				$this->nodeSubject->addItem($nodeItem);
				break;
				
			case 'removeItem':
				$nodeItem = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('item'));
				$this->nodeSubject->removeItem($nodeItem);
				if (!isset($_GET['silent'])) {
					$_RESPONSE->redirect($this->nodeSubject->getIdentifier());
				}
				break;
				
			case 'copyItems':
				$nodeTarget = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('target'));
				if (!User::isAuthorised('add_titles', $nodeTarget)) {
					throw new SecurityException('User ist not allowed to write to target playlist');
				}
				$aItems = $_REQUEST->getParam('items');
				foreach ($aItems as $sItemUUID) {
					$nodeItem = $this->crSession->getNodeByIdentifier($sItemUUID);
					$nodeTarget->addItem($nodeItem);
				}
				$_RESPONSE->redirect($this->nodeSubject->getIdentifier());
				break;
				
			case 'moveItems':
				$nodeTarget = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('target'));
				if (!User::isAuthorised('add_titles', $nodeTarget)) {
					throw new SecurityException('User ist not allowed to write to target playlist');
				}
				$aItems = $_REQUEST->getParam('items');
				foreach ($aItems as $sItemUUID) {
					$nodeItem = $this->crSession->getNodeByIdentifier($sItemUUID);
					$this->nodeSubject->moveItem($nodeItem, $nodeTarget);
				}
				$_RESPONSE->redirect($this->nodeSubject->getIdentifier());
				break;
				
			case 'removeItems':
				$aItems = $_REQUEST->getParam('items');
				foreach ($aItems as $sItemUUID) {
					$nodeItem = $this->crSession->getNodeByIdentifier($sItemUUID);
					$this->nodeSubject->removeItem($nodeItem);
				}
				$_RESPONSE->redirect($this->nodeSubject->getIdentifier());
				break;
								
			case 'clear':
				$this->nodeSubject->clear();
				$_RESPONSE->redirect($this->nodeSubject->getIdentifier());
				break;
				
			case 'orderBefore':
				$nodeSubject = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('subject'));
				$nodeNextSibling = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('nextsibling'));
				$this->nodeSubject->orderBefore($nodeSubject->getName(), $nodeNextSibling->getName());
				$this->nodeSubject->save();
				break;
				
			case 'activate':
				$sJukeboxUUID = $this->getJukebox()->getIdentifier();
				sbSession::$aData['sbJukebox'][$sJukeboxUUID]['playlist'] = $this->nodeSubject->getIdentifier();
				$_RESPONSE->redirect('-', 'playlists');
				break;
			
			case 'importM3U':
				if (!isset($_FILES['playlist_file'])) {
					throw new sbException(__CLASS__.': no file submitted');
				}
				$hPlaylist = fopen($_FILES['playlist_file']['tmp_name'], 'r');
				if (trim(fgets($hPlaylist)) != '#EXTM3U') {
					throw new sbException(__CLASS__.': file is no playlist');
				}
				$this->clearPlaylist();
				while (!feof($hPlaylist)) {
					// skip info lines
					fgets($hPlaylist, 4096);
			    	// process lines with stream urls
			    	$sLine = fgets($hPlaylist, 4096);
			    	$aMatches = array();
				    if (preg_match('/play\/([0-9a-f]{32})/', $sLine, $aMatches)) {
				    	$sTrackUUID = $aMatches[1];
				    	try {
					    	$nodeTrack = $this->crSession->getNodeByIdentifier($sTrackUUID);
					    	$this->nodeSubject->addExistingNode($nodeTrack);
				    	} catch (NodeNotFoundException $e) {
				    		// invalid node id / obsolete track
				    		// TODO: log an event at the end of import
				    	} 
				    }
				}
				$this->nodeSubject->save();
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
			
			case 'getM3U':
				$bRandom = FALSE;
				if ($_REQUEST->getParam('random') != NULL) {
					$bRandom = TRUE;
				}
				$this->sendPlaylist($this->nodeSubject, $bRandom);
				//$this->sendPlaylist();
				break;
			
			case 'download':
				import('sbJukebox:sb.jukebox.tools');
				$this->logEvent(System::INFO, 'DOWNLOAD_STARTED', 'Playlist: '.$this->nodeSubject->getProperty('label'));
				JukeboxTools::sendDownloadArchive($this->nodeSubject);
				$this->logEvent(System::INFO, 'DOWNLOAD_ENDED', 'Playlist: '.$this->nodeSubject->getProperty('label'));
				exit();
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
				
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildImportForm() {
		
		$formImport = new sbDOMForm(
			'importM3U',
			'',
			System::getRequestURL($this->nodeSubject, 'details', 'importM3U'),
			$this->crSession
		);
		
		$formImport->addInput('playlist_file;fileupload;required=true;', '');
		$formImport->addSubmit('$locale/sbSystem/actions/save');
		
		return ($formImport);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*protected function clearPlaylist() {
		
		$niTracks = $this->nodeSubject->getNodes();
		foreach ($niTracks as $nodeTrack) {
			$this->removeItem($nodeTrack);	
		}
		
	}*/
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*protected function addItem($nodeItem) {
		
		foreach ($nodeItem->getSharedSet() as $nodeShared) {
			if ($nodeShared->getParent()->isSame($this->nodeSubject)) {
				$nodeShared->removeShare();
				$this->crSession->save();
				return (TRUE);
			}
		}
		return (FALSE);
		
	}*/
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*protected function removeItem($nodeItem) {
		
		foreach ($nodeItem->getSharedSet() as $nodeShared) {
			if ($nodeShared->getParent()->isSame($this->nodeSubject)) {
				$nodeShared->removeShare();
				$this->crSession->save();
				return (TRUE);
			}
		}
		return (FALSE);
		
	}*/
	
}


?>