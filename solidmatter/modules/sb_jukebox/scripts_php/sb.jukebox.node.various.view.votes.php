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
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
}

?>