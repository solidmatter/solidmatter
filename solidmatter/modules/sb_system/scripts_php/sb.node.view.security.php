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
class sbView_security extends sbView {
	
	protected $aRequiredAuthorisations = array(
		'display' => array('grant'),
		'addUser' => array('grant'),
		'removeUser' => array('grant'),
		'changeInheritance' => array('grant'),
		'editAuthorisations' => array('grant'),
		'saveAuthorisations' => array('grant'),
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function init() {
		
		//$this->aQueries['addUser'] = 'sb_system/node/view/security/addUser';
		//$this->aQueries['saveInheritance'] = 'sb_system/node/view/security/saveInheritance';
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	// TODO: solve via sbForm object
	public function execute($sAction) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				$aResult = $this->nodeSubject->loadSecurityAuthorisations();
				$_RESPONSE->addData($aResult['groups']);
				$_RESPONSE->addData($aResult['users']);
				break;
				
			case 'addUser':
				$sEntityUUID = $_REQUEST->getParam('entity_uuid');
				$aDefaultAuthorisations = array(
					'read',
				);
				$stmtDefault = $this->nodeSubject->getSession()->prepareKnown('sb_system/node/view/security/addAuthorisation');
				foreach ($aDefaultAuthorisations as $sAuthorisation) {
					$stmtDefault->bindValue('subject_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
					$stmtDefault->bindValue('entity_uuid', $sEntityUUID, PDO::PARAM_STR);
					$stmtDefault->bindValue('authorisation', $sAuthorisation, PDO::PARAM_STR);
					$stmtDefault->bindValue('granttype', 'ALLOW', PDO::PARAM_STR);
					$stmtDefault->execute();
					$stmtDefault->closeCursor();
				}
				$cacheAuth = CacheFactory::getInstance('authorisations');
				$cacheAuth->clearAuthorisations($sEntityUUID);
				
				$this->logEvent(System::SECURITY, 'USERENTITY_ADDED', $sEntityUUID);
				
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'security');
				break;
			
			case 'removeUser':
				$sEntityUUID = $_REQUEST->getParam('userentity');
				$stmtDefault = $this->nodeSubject->getSession()->prepareKnown('sb_system/node/view/security/removeAuthorisations');
				$stmtDefault->bindValue('subject_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtDefault->bindValue('entity_uuid', $sEntityUUID, PDO::PARAM_STR);
				$stmtDefault->execute();
				$stmtDefault->closeCursor();
				$cacheAuth = CacheFactory::getInstance('authorisations');
				$cacheAuth->clearAuthorisations($sEntityUUID);
				
				$this->logEvent(System::SECURITY, 'USERENTITY_REMOVED', $sEntityUUID);
				
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'security');
				break;
			
			case 'changeInheritance':
				$formInheritance = $this->buildForm('changeInheritance');
				$formInheritance->recieveInputs();
				if (!$formInheritance->checkInputs()) {
					throw new InvalidFormDataException('changeInheritance');
				}
				$aInputs = $formInheritance->getValues();
				$this->nodeSubject->setProperty('sbcr:inheritRights', $aInputs['inheritrights']);
				$this->nodeSubject->setProperty('sbcr:bequeathRights', $aInputs['bequeathrights']);
				$this->nodeSubject->setProperty('sbcr:bequeathLocalRights', $aInputs['bequeathlocalrights']);
				$this->nodeSubject->save();
				
				$cacheAuth = CacheFactory::getInstance('authorisations');
				$cacheAuth->clearAuthorisations($_REQUEST->getParam('userentity'));
				
				$this->logEvent(System::SECURITY, 'INHERITANCE_CHANGED', 'I: '.$aInputs['inheritrights'].' B: '.$aInputs['bequeathrights'].' BL: '.$aInputs['bequeathlocalrights']);
				
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'security');
				break;
				
			case 'editAuthorisations':
				$this->nodeSubject->loadSecurityAuthorisations();
				$this->nodeSubject->setAttribute('subjectid', $_REQUEST->getParam('userentity'));
				$nodeUserEntity = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('userentity'));
				$_RESPONSE->addData($nodeUserEntity, 'userentity');
				break;
				
			case 'saveAuthorisations':
				$stmtSaveAuth = $this->nodeSubject->getSession()->prepareKnown('sbSystem/node/setAuthorisation');
				$stmtSaveAuth->bindValue('entity_uuid', $_REQUEST->getParam('userentity'), PDO::PARAM_STR);
				$stmtSaveAuth->bindValue('subject_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtRemoveAuth = $this->nodeSubject->getSession()->prepareKnown('sbSystem/node/removeAuthorisation');
				$stmtRemoveAuth->bindValue('entity_uuid', $_REQUEST->getParam('userentity'), PDO::PARAM_STR);
				$stmtRemoveAuth->bindValue('subject_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				// TODO: save on node::save()?
				$sMessage = $_REQUEST->getParam('userentity').' ';
				foreach ($this->nodeSubject->getSupportedAuthorisations() as $sAuth => $sParentAuth) {
					if ($_REQUEST->getParam($sAuth.'_allow') == 'on') {
						$stmtSaveAuth->bindValue('authorisation', $sAuth, PDO::PARAM_STR);
						$stmtSaveAuth->bindValue('granttype', 'ALLOW', PDO::PARAM_STR);
						$stmtSaveAuth->execute();
						$sMessage .= $sAuth.'=ALLOW ';
					} elseif ($_REQUEST->getParam($sAuth.'_deny') == 'on') {
						$stmtSaveAuth->bindValue('authorisation', $sAuth, PDO::PARAM_STR);
						$stmtSaveAuth->bindValue('granttype', 'DENY', PDO::PARAM_STR);
						$stmtSaveAuth->execute();
						$sMessage .= $sAuth.'=DENY ';
					} else {
						$stmtRemoveAuth->bindValue('authorisation', $sAuth, PDO::PARAM_STR);
						$stmtRemoveAuth->execute();
						$sMessage .= $sAuth.'=REMOVE ';
					}
				}
				$cacheAuth = CacheFactory::getInstance('authorisations');
				$cacheAuth->clearAuthorisations($_REQUEST->getParam('userentity'));
				$this->nodeSubject->loadSecurityAuthorisations();
				$this->nodeSubject->setAttribute('subjectid', $_REQUEST->getParam('userentity'));
				
				$nodeUserEntity = $this->crSession->getNodeByIdentifier($_REQUEST->getParam('userentity'));
				$_RESPONSE->addData($nodeUserEntity, 'userentity');
				
				$this->logEvent(System::SECURITY, 'AUTHORISATIONS_CHANGED', $sMessage);
				
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
	private function buildForm($sID) {
		
		import('sb.form');
		
		switch ($sID) {
			
			case 'addUser':
				$formAddUser = new sbDOMForm(
					'adduser', 
					'', 
					'/'.$this->nodeSubject->getProperty('jcr:uuid').'/security/adduser',
					$this->nodeSubject->getSession()
				);
				$formAddUser->addInput('select:user');
				return ($formAddUser);
			
			case 'changeInheritance':
				$formInheritance = new sbDOMForm(
					'changeinheritance', 
					'', 
					'/'.$this->nodeSubject->getProperty('jcr:uuid').'/security/changeinheritance',
					$this->nodeSubject->getSession()
				);
				$formInheritance->addInput('inheritrights;checkbox');
				$formInheritance->addInput('bequeathrights;checkbox');
				$formInheritance->addInput('bequeathlocalrights;checkbox');
				return ($formInheritance);
		}
		
	}
	
}


?>