<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

// TODO: apply checks
//------------------------------------------------------------------------------
/**
*/
class sbView_structure extends sbView {
	
	public function execute($sAction) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'cut':
				
				$sChildUUID = $_REQUEST->getParam('childnode');
				$sParentUUID = $_REQUEST->getParam('parentnode');
				
				if (!User::isAuthorised('write', $sParentUUID) || !User::isAuthorised('write', $sChildUUID)) {
					throw new sbException('you are not permitted to cut here');
				}
				
				sbSession::$aData['clipboard']['type'] = 'cut';
				sbSession::$aData['clipboard']['childnode'] = $_REQUEST->getParam('childnode');
				sbSession::$aData['clipboard']['parentnode'] = $_REQUEST->getParam('parentnode');
				sbSession::commit();
				
				break;
				
			case 'copy':
			
				$sChildUUID = $_REQUEST->getParam('childnode');
				$sParentUUID = $_REQUEST->getParam('parentnode');
				
				if (!User::isAuthorised('write', $sParentUUID) || !User::isAuthorised('write', $sChildUUID)) {
					throw new sbException('you are not permitted to copy here');
				}
				
				sbSession::$aData['clipboard']['type'] = 'copy';
				sbSession::$aData['clipboard']['childnode'] = $sChildUUID;
				sbSession::$aData['clipboard']['parentnode'] = $sParentUUID;
				sbSession::commit();
				break;
				
			case 'paste':
				
				if (!isset(sbSession::$aData['clipboard'])) {
					throw new MissingParameterException('nothing in clipboard');	
				}
				
				$sNewParentUUID = $_REQUEST->getParam('parentnode');
				$sOldParentUUID = sbSession::$aData['clipboard']['parentnode'];
				$sSubjectUUID = sbSession::$aData['clipboard']['childnode'];
				
				// check if new parent is child of subject
				$nodeNewParent = $this->crSession->getNodeByIdentifier($sNewParentUUID);
				$nodeOldParent = $this->crSession->getNodeByIdentifier($sOldParentUUID);
				$nodeSubject = $this->crSession->getNodeByIdentifier($sSubjectUUID);
				
				if ($nodeNewParent->isDescendantOf($nodeSubject)) {
					throw new RepositoryException('nodes cannot be children of themselves');	
				}
				
				if ($nodeNewParent->isSame($nodeOldParent)) {
					return; // do nothing
				}
				
				// move/copy tree
				if (sbSession::$aData['clipboard']['type'] == 'cut') { // move branch
					$this->crSession->moveBranchByNodes($nodeSubject, $nodeOldParent, $nodeNewParent);
					$this->crSession->save();
					unset(sbSession::$aData['clipboard']);
				} else { // copy branch
					throw new LazyBastardException();
				}
				
				sbSession::commit();
				break;
				
			case 'createLink':
				
				if (!isset(sbSession::$aData['clipboard'])) {
					throw new MissingParameterException('nothing in clipboard');	
				}
				
				if (sbSession::$aData['clipboard']['type'] == 'cut') {
					throw new sbException('creating hardlinks is only possible via copy');
				}
				
				$sNewParentUUID = $this->requireParam('parentnode');
				$sOldParentUUID = sbSession::$aData['clipboard']['parentnode'];
				$sSubjectUUID = sbSession::$aData['clipboard']['childnode'];
				
				// check if new parent is child of subject
				$nodeNewParent = $this->crSession->getNodeByIdentifier($sNewParentUUID);
				$nodeOldParent = $this->crSession->getNodeByIdentifier($sOldParentUUID);
				$nodeSubject = $this->crSession->getNodeByIdentifier($sSubjectUUID);
				
				/*if ($nodeNewParent->isDescendantOf($nodeSubject)) {
					throw new RepositoryException('nodes cannot be children of themselves');	
				}*/
				
				if ($nodeNewParent->isSame($nodeOldParent)) {
					return; // do nothing
				}
				
				// create link
				$nodeNewParent->addExistingNode($nodeSubject);
				$nodeNewParent->save();
				
				sbSession::commit();
				break;
				
			case 'setPrimary':
				
				$sNewParentUUID = $this->requireParam('parentnode');
				$sSubjectUUID = $this->requireParam('childnode');
				
				$nodeNewParent = $this->crSession->getNodeByIdentifier($sNewParentUUID);
				$nodeSubject = $this->crSession->getNodeByIdentifier($sSubjectUUID);
				$nodeSubject->setPrimaryParent($nodeNewParent);
				
				break;
				
			case 'addToFavorites':
				// TODO: various checks
				$sSubjectUUID = $this->requireParam('node');
				$nodeUserFavorites = User::getNode()->getNode('favorites');
				$nodeSubject = $this->crSession->getNodeByIdentifier($sSubjectUUID);
				// create link
				$nodeUserFavorites->addExistingNode($nodeSubject);
				$nodeUserFavorites->save();
				break;
			
			case 'deleteChild':
				
				$sChildUUID = $this->requireParam('childnode');
				$sParentUUID = $this->requireParam('parentnode');
				
				$nodeParent = $this->crSession->getNodeByIdentifier($sParentUUID);
				$nodeChild = $this->crSession->getNodeByIdentifier($sChildUUID);
				
				$elemContainer = ResponseFactory::createElement('confirm');
				$elemContainer->setAttribute('type', 'delete');
				$elemParent = ResponseFactory::createElement('parent');
				$elemParent->appendChild($nodeParent->getElement());
				$elemChild = ResponseFactory::createElement('child');
				$elemChild->appendChild($nodeChild->getElement());
				$elemContainer->appendChild($elemParent);
				$elemContainer->appendChild($elemChild);
				
				$_RESPONSE->addData($elemContainer);
				
				if ($_REQUEST->getParam('confirm') == NULL) {
					$niRefencingNodes = $nodeChild->getReferencingNodes();
					if ($niRefencingNodes->getSize() > 0) {
						$_RESPONSE->addData($niRefencingNodes->getElement('references'));
					}
					$niLinkingNodes = $nodeChild->getWeakReferencingNodes();
					if ($niLinkingNodes->getSize() > 0) {
						$_RESPONSE->addData($niLinkingNodes->getElement('softlinks'));
					}
				} else {
					
					foreach ($nodeChild->getSharedSet() as $nodeShared) {
						if ($nodeShared->getParent()->isSame($nodeParent)) {
							$nodeShared->moveToTrash();
							$this->crSession->save();
						}
					}
					// old method
					/*$nodeTrashcan = $this->crSession->getNode('//*[@uid="sbSystem:Trashcan"]');
					$this->crSession->moveBranchByNodes($nodeChild, $nodeParent, $nodeTrashcan);
					$this->crSession->save();
					/* DISABLED, testing trashcan
					$nodeChild->remove();
					$nodeChild->save();
					//$nodeParent->deleteChild($nodeChild, FALSE);
					//$_RESPONSE->addCommand('reloadTree');*/
					$_RESPONSE->addData($this->nodeSubject);
					$_RESPONSE->forceRenderMode('XML');
				}
				break;
				
			case 'createChild':
				$nodeParent = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('parentnode'));
				$_RESPONSE->addData($nodeParent, 'parent');
				$nodeChild = $nodeParent->addNode('TEMP', $_REQUEST->getParam('nodetype'));
				$formCreate = $nodeChild->buildForm('create', $_REQUEST->getParam('parentnode'));
				$formCreate->saveDOM();
				$_RESPONSE->addData($formCreate);
				break;
				
			case 'saveChild':
				
				$nodeParent = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('parentnode'));
				$_RESPONSE->addData($nodeParent, 'parent');
				$nodeChild = $nodeParent->addNode('temp', $_REQUEST->getParam('nodetype'));
				$formCreate = $nodeChild->buildForm('create', $_REQUEST->getParam('parentnode'));
				$formCreate->recieveInputs();
				
				if ($formCreate->checkInputs()) {
					
					$aValues = $formCreate->getValues();
					foreach ($aValues as $sName => $mValue) {
						$nodeChild->setProperty($sName, $mValue);
					}
					$nodeParent->save();
					
					if ($formCreate->getChosenSubmit() == 'create_multiple') { // generate fresh form
						$formCreate = $nodeChild->buildForm('create', $_REQUEST->getParam('parentnode'));
						$_RESPONSE->addData($formCreate);
						$_RESPONSE->addCommand('reloadTree');
					} else { // redirect to default view of newly created node
						$_RESPONSE->redirect($nodeChild->getProperty('jcr:uuid'), NULL, NULL, array('sbCommand' => 'reloadTree'));
					}
						
				} else {
					
					$formCreate->saveDOM();
					$_RESPONSE->addData($formCreate);
				}
				break;
				
			case 'orderBefore':
				$sView = 'list';
				if ($_REQUEST->getParam('redirectview') != NULL) {
					$sView = $_REQUEST->getParam('redirectview');
				}
				$nodeSubject = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('subject'));
				$nodeSubject->orderBefore($_REQUEST->getParam('source'), $_REQUEST->getParam('destination'));
				$nodeSubject->save();
				$_RESPONSE->redirect($_REQUEST->getParam('subject'), $sView);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
		return (NULL);
		
	}
	
}

?>