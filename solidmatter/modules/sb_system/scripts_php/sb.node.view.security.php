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
				//$_RESPONSE->addData($this->nodeSubject);
				break;
				
			case 'addUser':
				$sEntityUUID = $_REQUEST->getParam('entity_uuid');
				$aDefaultAuthorisations = array(
					'read',
					'write',
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
				//$this->nodeSubject->callView('security');
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
				//$this->nodeSubject->callView('security');
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'security');
				break;
			
			case 'changeInheritance':
				$formInheritance = $this->buildForm('changeInheritance');
				$formInheritance->recieveInputs();
				if (!$formInheritance->checkInputs()) {
					throw new InvalidFormDataException('changeInheritance');
				}
				$aInputs = $formInheritance->getValues();
				$this->nodeSubject->setProperty('inheritrights', $aInputs['inheritrights']);
				$this->nodeSubject->setProperty('bequeathrights', $aInputs['bequeathrights']);
				$this->nodeSubject->save();
				$cacheAuth = CacheFactory::getInstance('authorisations');
				$cacheAuth->clearAuthorisations($_REQUEST->getParam('userentity'));
				//$this->nodeSubject->callView('security', 'display');
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'security');
				break;
				
			case 'editAuthorisations':
				$this->nodeSubject->loadSecurityAuthorisations();
				$this->nodeSubject->setAttribute('subjectid', $_REQUEST->getParam('userentity'));
				//$_RESPONSE->addData($this->nodeSubject);
				break;
				
			case 'saveAuthorisations':
				//var_dumpp($this->nodeSubject->loadSupportedAuthorisations());
				$stmtSaveAuth = $this->nodeSubject->getSession()->prepareKnown('sbSystem/node/setAuthorisation');
				$stmtSaveAuth->bindValue('entity_uuid', $_REQUEST->getParam('userentity'), PDO::PARAM_STR);
				$stmtSaveAuth->bindValue('subject_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtRemoveAuth = $this->nodeSubject->getSession()->prepareKnown('sbSystem/node/removeAuthorisation');
				$stmtRemoveAuth->bindValue('entity_uuid', $_REQUEST->getParam('userentity'), PDO::PARAM_STR);
				$stmtRemoveAuth->bindValue('subject_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				// TODO: save on node::save()
				foreach ($this->nodeSubject->loadSupportedAuthorisations() as $sAuth => $sParentAuth) {
					//echo($sAuth.'_allow = '.$_REQUEST->getParam($sAuth.'_allow').'<br>');
					//echo($sAuth.'_deny = '.$_REQUEST->getParam($sAuth.'_deny').'<br>');
					if ($_REQUEST->getParam($sAuth.'_allow') == 'on') {
						$stmtSaveAuth->bindValue('authorisation', $sAuth, PDO::PARAM_STR);
						$stmtSaveAuth->bindValue('granttype', 'ALLOW', PDO::PARAM_STR);
						$stmtSaveAuth->execute();
					} elseif ($_REQUEST->getParam($sAuth.'_deny') == 'on') {
						$stmtSaveAuth->bindValue('authorisation', $sAuth, PDO::PARAM_STR);
						$stmtSaveAuth->bindValue('granttype', 'DENY', PDO::PARAM_STR);
						$stmtSaveAuth->execute();
					} else {
						$stmtRemoveAuth->bindValue('authorisation', $sAuth, PDO::PARAM_STR);
						$stmtRemoveAuth->execute();
					}
					
				}
				$cacheAuth = CacheFactory::getInstance('authorisations');
				$cacheAuth->clearAuthorisations($_REQUEST->getParam('userentity'));
				$this->nodeSubject->loadSecurityAuthorisations();
				$this->nodeSubject->setAttribute('subjectid', $_REQUEST->getParam('userentity'));
				//$_RESPONSE->addData($this->nodeSubject);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
		//return ($this->nodeSubject);
		
		
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
				return ($formInheritance);
		}
		
	}
	
}


?>