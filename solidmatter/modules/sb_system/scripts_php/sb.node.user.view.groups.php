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
class sbView_user_groups extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				$this->addGroupData();
				break;
				
			case 'add':
				$nodeGroup = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('group'));
				$nodeGroup->addExistingNode($this->nodeSubject);
				$nodeGroup->save();
				$this->logEvent(System::SECURITY, 'ADDED_TO_GROUP', $nodeGroup->getProperty('label').' ('.$nodeGroup->getProperty('jcr:uuid').')');
				$this->addGroupData();
				break;
				
			case 'remove':
				$nodeGroup = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('group'));
				foreach ($this->nodeSubject->getSharedSet() as $nodeShared) {
					if ($nodeShared->getParent()->isSame($nodeGroup)) {
						$nodeShared->removeShare();
						$this->crSession->save();
					}
				}
				$this->logEvent(System::SECURITY, 'REMOVED_FROM_GROUP', $nodeGroup->getProperty('label').' ('.$nodeGroup->getProperty('jcr:uuid').')');
				$this->addGroupData();
				break;
			
		}
		
	}
	
	protected function addGroupData() {
		global $_RESPONSE;
		$nodeUserAccounts = $this->nodeSubject->getParent();
		$niGroups = $nodeUserAccounts->getChildren('groups');
		$niSharedSet = $this->nodeSubject->getSharedSet();
		$elemGroups = $_RESPONSE->createElement('groups');
		foreach ($niGroups as $nodeGroup) {
			// only display the group if the current user may administer it
			if (!User::isAuthorised('grant', $nodeGroup)) {
				continue;
			}
			// users can't be added to guests
			if ($nodeGroup->getProperty('uid') == 'sbSystem:Guests') {
				continue;	
			}
			$elemGroup = $_RESPONSE->createElement('group');
			$elemGroup->setAttribute('uuid', $nodeGroup->getProperty('jcr:uuid'));
			$elemGroup->setAttribute('label', $nodeGroup->getProperty('label'));
			$elemGroup->setAttribute('name', $nodeGroup->getName());
			$elemGroup->setAttribute('displaytype', $nodeGroup->getProperty('displaytype'));
			$elemGroup->setAttribute('member', 'FALSE');
			foreach ($niSharedSet as $nodeShared) {
				if ($nodeShared->getParent()->isSame($nodeGroup)) {
					$elemGroup->setAttribute('member', 'TRUE');
				}
			}
			$elemGroups->appendChild($elemGroup);
		}
		$_RESPONSE->addData($elemGroups);
	}
	
}


?>