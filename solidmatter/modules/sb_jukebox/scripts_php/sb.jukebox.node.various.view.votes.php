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
class sbView_jukebox_various_votes extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		'placeVote' => array('vote'),
		//'removeVote' => array('write'), // not hard-coded because users can remove their own votes
		'addComment' => array('comment'),
		//'removeComment' => array('write'), // not hard-coded because users can remove their own comments
		'addTag' => array('tag'),
		'removeTag' => array('write'),
		'addRelation' => array('relate'),
		'removeRelation' => array('write'),
		'editLyrics' => array('edit_lyrics'),
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
			
			case 'addComment':
				
				// comment form
				$formComment = $this->buildCommentForm();
				$formComment->recieveInputs();
				if ($formComment->checkInputs()) {
					import('sb.tools.strings.conversion');
					$nodeComment = $this->nodeSubject->addNode('changeme', 'sbSystem:Comment');
					$nodeComment->setProperty('name', $nodeComment->getProperty('jcr:uuid'));
					$nodeComment->setProperty('label', $nodeComment->getProperty('jcr:uuid'));
					$nodeComment->setProperty('comment', $_REQUEST->getParam('comment'));
					$this->nodeSubject->save();
					if ($_REQUEST->getParam('silent') == NULL) {
						switch($this->nodeSubject->getPrimaryNodeType()) {
							case 'sbJukebox:Album':
								$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'details');
								break;
							case 'sbJukebox:Artist':
								$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'details');
								break;
							case 'sbJukebox:Track':
								$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'details');
								break;
							case 'sbJukebox:Playlist':
								$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'details');
								break;
						}
					}
				} else {
					throw new sbException('title or comment missing!');
					$formComment->saveDOM();
					$_RESPONSE->addData($formComment);
				}
				break;
				
			case 'removeComment':
				$sCommentUUID = $this->requireParam('comment');
				$nodeComment = $this->nodeSubject->getNode($sCommentUUID);
				if (!User::isAuthorised('write', $this->nodeSubject) && User::getUUID() != $nodeComment->getProperty('jcr:createdBy')) {
					throw new SecurityException('you are neither the author of the comment nor authorised to edit the comments of this object');	
				}
				$nodeTrashcan = $this->crSession->getNode('//*[@uid="sbSystem:Trashcan"]');
				$this->crSession->moveBranchByNodes($nodeComment, $this->nodeSubject, $nodeTrashcan);
				$this->crSession->save();
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			case 'placeVote':
				$iVote = $this->requireParam('vote');
				$nodeJukebox = $this->nodeSubject->getAncestorOfType('sbJukebox:Jukebox');
				$iRealVote = $iVote;
				$this->nodeSubject->removeVote(User::getUUID());
				$this->nodeSubject->placeVote(User::getUUID(), $iRealVote);
				if ($this->getPivotUUID() != User::getUUID()) {
					$iVote = $this->nodeSubject->getVote(User::getUUID());
				} else {
					$iVote = $this->nodeSubject->getVote();
				}
				$_RESPONSE->addHeader('X-sbVote: '.$iVote);
				break;
				
			case 'removeVote':
				$sUserUUID = $this->requireParam('user_uuid');
				if (!User::isAuthorised('write', $this->nodeSubject) && User::getUUID() != $sUserUUID) {
					throw new SecurityException('this is neither your own vote nor are you authorised write permissions on this object');
				}
				$this->nodeSubject->removeVote($sUserUUID);
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'votes', 'showDetails');
				break;
				
			case 'removeAllVotes':
				if (!User::isAuthorised('write', $this->nodeSubject)) {
					throw new SecurityException('you need write permissions on this object to remove all votes');
				}
				$this->nodeSubject->removeAllVotes();
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			case 'showDetails':
				$this->nodeSubject->storeUserVotes();
				break;
				
			case 'addTag':
				$formTag = $this->buildTagForm();
				$formTag->recieveInputs();
				$aValues = $formTag->getValues();
				$sTag = $this->requireParam('tag');
				if ($aValues['spread'] == 'TRUE') {
					
					switch ($this->nodeSubject->getPrimaryNodeType()) {
						
						case 'sbJukebox:Artist':
							foreach ($this->nodeSubject->getChildren('albums') as $nodeAlbum) {
								$nodeAlbum->addTag($sTag);
								$nodeAlbum->save();
								foreach ($nodeAlbum->getChildren('tracks') as $nodeTrack) {
									$nodeTrack->addTag($sTag);
									$nodeTrack->save();
								}
							}
							break;
							
						case 'sbJukebox:Album':
							foreach ($this->nodeSubject->getChildren('tracks') as $nodeTrack) {
								$nodeTrack->addTag($sTag);
								$nodeTrack->save();
							}
							$nodeArtist = $this->nodeSubject->getParent();
							$nodeArtist->addTag($sTag);
							$nodeArtist->save();
							break;
							
						case 'sbJukebox:Track':
							$nodeAlbum = $this->nodeSubject->getParent();
							$nodeAlbum->addTag($sTag);
							$nodeAlbum->save();
							$nodeArtist = $nodeAlbum->getParent();
							$nodeArtist->addTag($sTag);
							$nodeArtist->save();
							break;
					
					}
					
				}
				$this->nodeSubject->addTag($sTag);
				$this->nodeSubject->save();
				$this->logEvent(System::INFO, 'TAG_ADDED', $sTag);
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			case 'getTags':
				$stmtGetTags = $this->crSession->prepareKnown('sbSystem/tagging/tags/getMatchingTags');
				$stmtGetTags->bindValue('substring', '%'.$_REQUEST->getParam('tag').'%', PDO::PARAM_STR);
				$stmtGetTags->execute();
				echo '<ul>';
				foreach ($stmtGetTags as $aTag) {
					echo '<li>'.$aTag['tag'].'</li>';
				}
				echo '</ul>';
				exit();
				break;
				
			case 'removeTag':
				$iTagID = $this->requireParam('tagid');
				$sTag = $this->nodeSubject->getTag($iTagID);
				$this->nodeSubject->removeTag($sTag);
				$this->nodeSubject->save();
				$this->logEvent(System::INFO, 'TAG_REMOVED', $sTag);
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			case 'getTargets':
				$aTargets = $this->nodeSubject->getPossibleTargets($_REQUEST->getParam('type_relation'), $_REQUEST->getParam('target_relation'));
				echo '<ul>';
				foreach ($aTargets as $sUUID => $aDetails) {
					echo '<li><span style="display:none;">'.$sUUID.'|</span>'.'<span class="type '.$aDetails['displaytype'].'">'.$aDetails['label'].'</span></li>';
				}
				echo '</ul>';
				exit();
				break;
				
			case 'addRelation':
				$sRelation = $_REQUEST->getParam('type_relation');
				$sTarget = substr($_REQUEST->getParam('target_relation'), 0, strpos($_REQUEST->getParam('target_relation'), '|'));
				$nodeTarget = $this->crSession->getNodeByIdentifier($sTarget);
				$this->nodeSubject->addRelation($sRelation, $nodeTarget);
				$this->logEvent(System::INFO, 'RELATION_ADDED', $sRelation.' to '.$nodeTarget->getName().' ('.$nodeTarget->getIdentifier().')');
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			case 'removeRelation':
				$sRelation = $_REQUEST->getParam('type_relation');
				$sTarget = $_REQUEST->getParam('target_relation');
				$nodeTarget = $this->crSession->getNodeByIdentifier($sTarget);
				$this->nodeSubject->removeRelation($sRelation, $nodeTarget);
				$this->logEvent(System::INFO, 'RELATION_REMOVED', $sRelation.' to '.$nodeTarget->getName().' ('.$nodeTarget->getIdentifier().')');
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			case 'saveLyrics':
				$sLyrics = $this->requireParam('lyrics');
				$this->nodeSubject->setProperty('info_lyrics', $sLyrics);
				$this->nodeSubject->save();
				$this->logEvent(System::INFO, 'LYRICS_SAVED', $sLyrics);
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
}

?>