<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_relations extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$this->nodeSubject->storeRelations();
				
				$formRelate = new sbDOMForm(
					'addRelation',
					'$locale/sbSystem/actions/relate',
					System::getRequestURL($this->nodeSubject, 'relations', 'add'),
					$this->crSession
				);
				
				$formRelate->addInput('relation;relation;url=/'.$this->nodeSubject->getProperty('jcr:uuid').'/relations/getTargets;', '$locale/sbSystem/labels/relation');
				$formRelate->addSubmit('$locale/sbSystem/actions/save');
				
				$aRelations = $this->nodeSubject->getSupportedRelations();
				foreach ($aRelations as $sRelation => $unused) {
					$aOptions[$sRelation] = $sRelation;
				}
				$formRelate->setOptions('relation', $aOptions);
				
				$_RESPONSE->addData($formRelate);
				
				return;
				
			case 'add':
				$sRelation = $_REQUEST->getParam('type_relation');
				$sTargetUUID = $_REQUEST->getParam('target_uuid_relation');
				$nodeTarget = $this->crSession->getNodeByIdentifier($sTargetUUID);
				$this->nodeSubject->addRelation($sRelation, $nodeTarget);
				$this->logEvent(System::INFO, 'RELATION_ADDED', $sRelation.' to '.$nodeTarget->getName().' ('.$nodeTarget->getIdentifier().')');
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'relations');
				break;
				
			case 'remove':
				$sRelation = $_REQUEST->getParam('type_relation');
				$sTarget = $_REQUEST->getParam('target_relation');
				$nodeTarget = $this->crSession->getNodeByIdentifier($sTarget);
				$this->nodeSubject->removeRelation($sRelation, $nodeTarget);
				$this->logEvent(System::INFO, 'RELATION_REMOVED', $sRelation.' to '.$nodeTarget->getName().' ('.$nodeTarget->getIdentifier().')');
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'relations');
				break;
				
			case 'getTargets':
				$aTargets = $this->nodeSubject->getPossibleTargets($_REQUEST->getParam('type_relation'), $_REQUEST->getParam('target_relation'));
				echo '<ul>';
				foreach ($aTargets as $sUUID => $aDetails) {
					echo '<li><span style="display:none;">'.$sUUID.'</span>'.'<span class="type '.str_replace(':', '_', $aDetails['nodetype']).'">'.$aDetails['label'].'</span></li>';
				}
				echo '</ul>';
				exit();
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
			
	}
	
}


?>