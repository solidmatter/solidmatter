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
		'addComment' => array('comment'),
		'addTag' => array('tag'),
		'placeVote' => array('vote'),
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
				
			case 'placeVote':
				$iVote = $this->requireParam('vote');
				$nodeJukebox = $this->nodeSubject->getAncestorOfType('sbJukebox:Jukebox');
				$iRealVote = $iVote;
				$this->nodeSubject->removeVote(User::getUUID());
				$this->nodeSubject->placeVote(User::getUUID(), $iRealVote);
				$_RESPONSE->addHeader('X-Vote: '.$this->nodeSubject->getVote());
				break;
				
			case 'addTag':
				$sTag = $this->requireParam('tag');
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
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
}

?>