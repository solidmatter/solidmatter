<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbJukebox:sb.jukebox.tools');
import('sbSystem:sb.tools.strings.conversion');
import('sbSystem:sb.tools.actionqueue');

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_various_fix extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'showOptions':
				
				$this->gatherBasicInformation($this->nodeSubject);
				
				switch ($this->nodeSubject->getPrimaryNodeType()) {
					
					case 'sbJukebox:Artist':
						$formCorrectArtist = $this->buildForm('correctArtist');
						$_RESPONSE->addData($formCorrectArtist);
						$formSwitchArtist = $this->buildForm('switchArtist');
						$_RESPONSE->addData($formSwitchArtist);
						
						break;
						
					case 'sbJukebox:Album':
						$formRemoveTracks = $this->buildForm('removeDoubletAlbum');
						$_RESPONSE->addData($formRemoveTracks);
						$formSwitchArtist = $this->buildForm('switchArtist');
						$_RESPONSE->addData($formSwitchArtist);
						$formRemoveTracks = $this->buildForm('removeTracks');
						$_RESPONSE->addData($formRemoveTracks);
						break;
						
					case 'sbJukebox:Track':
						$formRenameTrack = $this->buildForm('renameTrack');
						$_RESPONSE->addData($formRenameTrack);
						break;
					
					
				}
				
				
				break;
				
			case 'fix':
				
				$this->gatherBasicInformation($this->nodeSubject);
				
				$sMode = $_REQUEST->getParam('mode');
				
				$formResult = $this->buildForm($sMode);
				$formResult->recieveInputs();
				
				if ($formResult->checkInputs()) {
				
					$aOptions = array();
					
					switch ($sMode) {
						
						case 'delete':
							
							break;
						
						case 'correctArtist':
							$aOptions['old'] = $this->nodeSubject->getProperty('label');
							$aOptions['new'] = $_REQUEST->getParam('new_name');
							if ($aOptions['old'] == $aOptions['new']) {
								$formResult->setError('new_name', 'has to be different');
							}
							break;
							
						case 'switchArtist':
							$aOptions['target'] = $_REQUEST->getParam('target');
							break;
							
						case 'renameTrack':
							$aOptions['old'] = $_REQUEST->getParam('search');
							$aOptions['new'] = $_REQUEST->getParam('replace');
							if ($aOptions['old'] == $aOptions['new']) {
								$formResult->setError('replace', 'has to be different');	
							}
							break;
					}
				
				}
				
				if ($formResult->hasError()) {
					$_RESPONSE->addData($formResult);
				} else {
					$formConfirm = $this->buildForm($sMode, TRUE);
					$formConfirm->recieveInputs();
					$aqActions = $this->gatherNecessaryActions($sMode, $this->nodeSubject, $aOptions);
					//var_dumpp($formConfirm->getValues()); exit();
					if ($_REQUEST->getParam('confirm') != NULL) {
						$aqActions->execute();
						$_RESPONSE->addData($_RESPONSE->convertArrayToElement('actions', $aqActions->getActions(), TRUE));
					} else {
						$_RESPONSE->addData($_RESPONSE->convertArrayToElement('actions', $aqActions->getActions(), TRUE));
						$formConfirm->setValue('confirm', 'TRUE');
						$_RESPONSE->addData($formConfirm);
					}
				}
				
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
	protected function gatherBasicInformation($nodeSubject) {
		
		switch ($nodeSubject->getPrimaryNodeType()) {
			case 'sbJukebox:Artist':
				$niChildren = $nodeSubject->loadChildren('debug', TRUE, TRUE, TRUE);
				foreach ($niChildren as $nodeChild) {
					if ($nodeChild->getPrimaryNodeType() == 'sbJukebox:Album') {
						$this->gatherBasicInformation($nodeChild);
					}
				}
				$nodeSubject->storeTracks();
				break;
			case 'sbJukebox:Album':
				$niChildren = $nodeSubject->loadChildren('debug', TRUE, TRUE, TRUE);
				foreach ($niChildren as $nodeChild) {
					if ($nodeChild->getPrimaryNodeType() == 'sbJukebox:Track') {
						$this->gatherBasicInformation($nodeChild);
					}
				}
				break;
			case 'sbJukebox:Track':
				$niChildren = $nodeSubject->loadChildren('debug', TRUE, TRUE, TRUE);
				break;
			
			
		}
		
		$nodeSubject->storeAllVotes();
		$nodeSubject->storeRelations();
		
		$nodeSubject->storeChildren();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildForm($sMode, $bConfirmOnly = FALSE) {
		
		switch ($sMode) {
			
			case 'correctArtist':
				$formResult = new sbDOMForm(
					'correctArtist',
					'$locale/sbJukebox/fix/correct_artist',
					'/'.$this->nodeSubject->getProperty('jcr:uuid').'/fix/fix/?mode=correctArtist',
					$this->crSession
				);
				if ($bConfirmOnly) {
					$formResult->addInput('new_name;hidden;');
				} else {
					$formResult->addInput('new_name;string;required=TRUE;maxlength=200;minlength=1;', 'corrected name');
					$formResult->setValue('new_name', $this->nodeSubject->getProperty('label'));
				}
				break;
				
			case 'switchArtist':
				$formResult = new sbDOMForm(
					'switchArtist',
					'$locale/sbJukebox/fix/switch_artist',
					'/'.$this->nodeSubject->getProperty('jcr:uuid').'/fix/fix/?mode=switchArtist',
					$this->crSession
				);
				if ($bConfirmOnly) {
					$formResult->addInput('target;hidden;');
				} else {
					$formResult->addInput('target;select;', 'real artist');
					$aArtists = JukeboxTools::getAllArtists($this->getJukebox());
					$formResult->setOptions('target', $aArtists);
				}
				break;
				
			case 'removeDoubletAlbum':
				$formResult = new sbDOMForm(
					'removeDoubletAlbum',
					'$locale/sbJukebox/fix/remove_doublet_album',
					'/'.$this->nodeSubject->getProperty('jcr:uuid').'/fix/fix/?mode=removeDoubletAlbum',
					$this->crSession
				);
				if ($bConfirmOnly) {
					$formResult->addInput('other_album;hidden;');
				} else {
					$formResult->addInput('other_album;select;', 'other_album');
					$aAlbums = JukeboxTools::getAllAlbums($this->getJukebox());
					$formResult->setOptions('other_album', $aAlbums);
					$formResult->addInput('keep_other_album;checkbox;', 'keep_other_album');
				}
				break;
				
			case 'removeTracks':
				$formResult = new sbDOMForm(
					'removeTracks',
					'$locale/sbJukebox/fix/remove_tracks',
					'/'.$this->nodeSubject->getProperty('jcr:uuid').'/fix/fix/?mode=removeTracks',
					$this->crSession
				);
				$niTracks = $this->nodeSubject->getChildren('tracks');
				$aTracks = array();
				foreach ($niTracks as $nodeChild) {
					$aTracks[$nodeChild->getProperty('jcr:uuid')] = $nodeChild->getProperty('info_index').'. '.$nodeChild->getProperty('label');
				}
				$formResult->addInput('tracks;select;multiple=TRUE;size='.$niTracks->getSize(), 'tracks_to_remove');
				$formResult->setOptions('tracks', $aTracks);
				break;
				
			case 'renameTrack':
				$formResult = new sbDOMForm(
					'renameTrack',
					'$locale/sbJukebox/fix/rename_track',
					'/'.$this->nodeSubject->getProperty('jcr:uuid').'/fix/fix/?mode=renameTrack',
					$this->crSession
				);
				if ($bConfirmOnly) {
					$formResult->addInput('search;hidden;');
					$formResult->addInput('replace;hidden;');
				} else {
					$formResult->addInput('search;string;required=TRUE;maxlength=200;minlength=1;', 'search');
					$formResult->setValue('search', $this->nodeSubject->getTitle());
					$formResult->addInput('replace;string;required=TRUE;maxlength=200;minlength=1;', 'replace');
					$formResult->setValue('replace', $this->nodeSubject->getTitle());
				}
				break;
				
		}
		
		if ($bConfirmOnly) {
			$formResult->addInput('confirm;hidden;');
			$formResult->addSubmit('$locale/sbSystem/actions/execute', 'execute');
		} else {
			$formResult->addSubmit('$locale/sbSystem/labels/preview', 'preview');
		}
		
		return ($formResult);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function gatherNecessaryActions($sMode, $nodeSubject, $aOptions, $aqActions = NULL) {
		
		if ($aqActions == NULL) {
			$aqActions = new sbActionQueue();
			$aqActions->setSession($this->crSession);
		}
		
		switch ($sMode) {
			
			case 'delete':
				
				break;
			
			case 'correctArtist':
				$sOldName = $aOptions['old'];
				$sNewName = $aOptions['new'];
				$this->gatherNecessaryActions('rename', $nodeSubject, array('old' => $sOldName, 'new' => $sNewName), $aqActions);
				break;
				
			case 'switchArtist':
				$nodeNewArtist = $this->nodeSubject->getSession()->getNodeByIdentifier($aOptions['target']);
				$sOldName = $nodeSubject->getProperty('label');
				$sNewName = $nodeNewArtist->getProperty('label');
				$this->gatherNecessaryActions('rename', $nodeSubject, array('old' => $sOldName, 'new' => $sNewName), $aqActions);
				
				break;
				
			case 'renameTrack':
				
				//break;
				
			case 'rename':
				
				$sOld = $aOptions['old'];
				$sNew = $aOptions['new'];
				
				switch ($nodeSubject->getPrimaryNodeType()) {
					
					case 'sbJukebox:Track':
					
						$sOldLabel = $nodeSubject->getProperty('label');
						$sNewLabel = str_replace($sOld, $sNew, $nodeSubject->getProperty('label'));
						$sOldName = $nodeSubject->getName();
						$sNewName = str_replace(str2urlsafe($sOld), str2urlsafe($sNew), $nodeSubject->getName());
						$sOldArtistTag = $nodeSubject->getTag('TPE1');
						$sNewArtistTag = str_replace($sOld, $sNew, $sOldArtistTag);
						$sOldTitle = $nodeSubject->getProperty('info_title');
						$sNewTitle = str_replace($sOld, $sNew, $sOldTitle);
						$sOldTitleTag = $nodeSubject->getTag('TIT2');
						$sNewTitleTag = str_replace($sOld, $sNew, $sOldTitleTag);
						$sOldFilename = $nodeSubject->getProperty('info_filename');
						$sNewFilename = str_replace($sOld, $sNew, $nodeSubject->getProperty('info_filename'));
						
						$aqActions->addAction('relabel', $nodeSubject, array('old_label' => $sOldLabel, 'new_label' => $sNewLabel, 'ignore' => $sOldLabel == $sNewLabel));
						$aqActions->addAction('rename', $nodeSubject, array('old_name' => $sOldName, 'new_name' => $sNewName,  'ignore' => $sOldName == $sNewName));
						$aqActions->addAction('retag_mp3', $nodeSubject, array('tag' => 'TPE1', 'old_tag' => $sOldArtistTag, 'new_tag' => $sNewArtistTag, 'ignore' => $sOldArtistTag == $sNewArtistTag));
						$aqActions->addAction('retag_mp3', $nodeSubject, array('tag' => 'TIT2', 'old_tag' => $sOldTitleTag, 'new_tag' => $sNewTitleTag, 'ignore' => $sOldTitleTag == $sNewTitleTag));
						$aqActions->addAction('rename_file', $nodeSubject, array('old_filename' => $sOldFilename, 'new_filename' => $sNewFilename, 'ignore' => $sOldFilename == $sNewFilename));
						$aqActions->addAction('change_property', $nodeSubject, array('property' => 'info_filename', 'old_content' => $sOldFilename, 'new_content' => $sNewFilename, 'ignore' => $sOldFilename == $sNewFilename));
						$aqActions->addAction('change_property', $nodeSubject, array('property' => 'info_title', 'old_content' => $sOldTitle, 'new_content' => $sNewTitle, 'ignore' => $sOldTitle == $sNewTitle));
						
						break;
						
					case 'sbJukebox:Album':
					
						foreach ($nodeSubject->getChildren() as $nodeChild) {
							if ($nodeChild->getPrimaryNodeType() == 'sbJukebox:Track') {
								$this->gatherNecessaryActions('rename', $nodeChild, $aOptions, $aqActions);
							}
						}
						
						$sOldLabel = $nodeSubject->getProperty('label');
						$sNewLabel = str_replace($sOld, $sNew, $nodeSubject->getProperty('label'));
						$sOldName = $nodeSubject->getName();
						$sNewName = str_replace(str2urlsafe($sOld), str2urlsafe($sNew), $nodeSubject->getName());
						$sOldDirname = $nodeSubject->getProperty('info_relpath');
						$sNewDirname = str_replace($sOld, $sNew, $nodeSubject->getProperty('info_relpath'));
						
						$aqActions->addAction('relabel', $nodeSubject, array('old_label' => $sOldLabel, 'new_label' => $sNewLabel, 'ignore' => $sOldLabel == $sNewLabel));
						$aqActions->addAction('rename', $nodeSubject, array('old_name' => $sOldName, 'new_name' => $sNewName,  'ignore' => $sOldName == $sNewName));
						$aqActions->addAction('rename_file', $nodeSubject, array('old_filename' => $sOldDirname, 'new_filename' => $sNewDirname, 'ignore' => $sOldDirname == $sNewDirname));
						
						break;
						
					case 'sbJukebox:Artist':
					
						foreach ($nodeSubject->getChildren() as $nodeChild) {
							if ($nodeChild->getPrimaryNodeType() == 'sbJukebox:Album') {
								$this->gatherNecessaryActions('rename', $nodeChild, $aOptions, $aqActions);
							}
						}
						
						foreach ($nodeSubject->getTracks() as $nodeChild) {
							$this->gatherNecessaryActions('rename', $nodeChild, $aOptions, $aqActions);
						}
						
						$sOldLabel = $nodeSubject->getProperty('label');
						$sNewLabel = str_replace($sOld, $sNew, $nodeSubject->getProperty('label'));
						
						$aqActions->addAction('relabel', $nodeSubject, array('old_label' => $sOldLabel, 'new_label' => $sNewLabel, 'ignore' => $sOldLabel == $sNewLabel));
						
						break;
					
				}
				
				break;
			
		}
		
		return ($aqActions);
		
	}
	
}

?>