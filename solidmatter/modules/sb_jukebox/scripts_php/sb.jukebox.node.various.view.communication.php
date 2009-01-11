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
class sbView_jukebox_various_communication extends sbJukeboxView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'newRecommendation':
				$formRecommend = $this->buildRecommendationForm();
				$formRecommend->saveDOM();
				$_RESPONSE->addData($formRecommend);
				break;
			
			case 'sendRecommendation':
				
				$formRecommend = $this->buildRecommendationForm();
				$formRecommend->recieveInputs();
				if ($formRecommend->checkInputs()) {
					$aInputs = $formRecommend->getValues();
					// add recommendation
					$nodeUser = $this->crSession->getNodeByIdentifier($aInputs['user']);
					$nodeInbox = $nodeUser->getNode('inbox');
					$nodeJukebox = $this->getJukebox();
					$nodeRecommendation = $nodeInbox->addNode($nodeInbox->getIdentifier().'_'.uuid(), 'sbJukebox:Recommendation');
					$nodeRecommendation->setProperty('label', $this->nodeSubject->getProperty('label'));
					$nodeRecommendation->setProperty('comment', $aInputs['comment']);
					$nodeRecommendation->setProperty('subject', $this->nodeSubject->getProperty('jcr:uuid'));
					$nodeInbox->save();
					$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'));
				} else {
					throw new sbException('something is not right...');
				}
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
	protected function buildRecommendationForm() {
		$formRecommend = new sbDOMForm(
			'recommendation',
			'$locale/system/general/labels/recommend',
			System::getURL($this->nodeSubject, 'recommend', 'sendRecommendation'),
			$this->crSession
		);
		$formRecommend->addInput('user;users;includeself=FALSE', '');
		$formRecommend->addInput('comment;text', '');
		$formRecommend->addSubmit('$locale/system/general/actions/send');
		return ($formRecommend);
	}
	
	
}

?>