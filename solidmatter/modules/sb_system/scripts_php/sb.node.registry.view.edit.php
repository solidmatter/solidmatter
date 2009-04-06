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
* Lets you edit the registry entries.
* TODO: ability to edit user-specific values is missing.
*/
class sbView_registry_edit extends sbView {
	
	private $aRegistry = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$formRegistry = $this->buildForm();
				$formRegistry->saveDOM();
				$_RESPONSE->addData($formRegistry);
				
				return (NULL);
				
			case 'save':
				
				$formRegistry = $this->buildForm();
				$formRegistry->recieveInputs();
				
				if ($formRegistry->checkInputs()) {
					
					$aData = $formRegistry->getValues();
					
					$stmtWriteData = $this->crSession->prepareKnown('sbSystem/registry/setValue');
					$sKey = '';
					$sValue = '';
					if ($this->nodeSubject->isNodeType('sbSystem:User')) {
						$sUserUUID = $this->nodeSubject->getProperty('jcr:uuid');	
					} else {
						$sUserUUID = 'SYSTEM';
					}
					$stmtWriteData->bindParam('key', $sKey, PDO::PARAM_STR);
					$stmtWriteData->bindParam('value', $sValue, PDO::PARAM_STR);
					$stmtWriteData->bindParam('user_uuid', $sUserUUID, PDO::PARAM_STR);
					foreach($this->aRegistry as $aRow) {
						
						// set to random value for change detection
						if ($aRow['s_key'] == 'sb.system.cache.registry.changedetection') {
							$sValue = uuid();
						} elseif ($this->nodeSubject->isNodeType('sbSystem:User') && $aRow['b_userspecific'] == 'FALSE') {
							continue;
						} else { // otherwise use form value
							$sFormName = str_replace('.', '_', $aRow['s_key']);
							$sValue = $formRegistry->getValue($sFormName);
						}
						$sKey = $aRow['s_key'];
						
						$stmtWriteData->execute();
						
					}
					
					$formRegistry->saveDOM();
					$_RESPONSE->addData($formRegistry);
					
					$this->logEvent(System::MAINTENANCE, 'REGISTRY_SAVED', 'SYSTEM values have been saved');
					
					return (NULL);
					
				} else {
					
					$formRegistry->saveDOM();
					$_RESPONSE = ResponseFactory::getInstance('global');
					$_RESPONSE->addData($formRegistry);
					return (NULL);
					
				}
				
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
	protected function buildForm() {
		
		$sAction = '/'.$this->nodeSubject->getProperty('jcr:uuid').'/edit/save';
		if ($this->nodeSubject->isNodeType('sbSystem:User')) {
			$sAction = '/'.$this->nodeSubject->getProperty('jcr:uuid').'/registry/save';
		}
		$formRegistry = new sbDOMForm(
			'registry',
			'$locale/sbSystem/registry/label',
			$sAction,
			$this->crSession
		);
		
		if ($this->nodeSubject->isNodeType('sbSystem:User')) {
			$sUserUUID = $this->nodeSubject->getProperty('jcr:uuid');	
		} else {
			$sUserUUID = 'SYSTEM';
		}
		$stmtGetData = $this->crSession->prepareKnown('sbSystem/registry/getAllEntries');
		$stmtGetData->bindParam('user_uuid', $sUserUUID, PDO::PARAM_STR);
		$stmtGetData->execute();
		$this->aRegistry = $stmtGetData->fetchAll();
		$stmtGetData->closeCursor();
		
		foreach ($this->aRegistry as $aRow) {
			
			// skip change detection entry
			if ($aRow['s_key'] == 'sb.system.cache.registry.changedetection') {
				continue;
			}
			
			// skip non-userspecific entries for user registry
			if ($this->nodeSubject->isNodeType('sbSystem:User') && $aRow['b_userspecific'] == 'FALSE') {
				continue;
			}
			
			if (isset($aRow['s_internaltype']) && $aRow['s_internaltype']) {
				$sConfig = $aRow['s_internaltype'];
			} else {
				switch ($aRow['e_type']) {
					case 'boolean': 	
						$sConfig = 'checkbox';
						break;
					case 'integer':
						$sConfig = 'integer;minvalue=-65536;maxvalue=65535';
						break;
					case 'string':
						$sConfig = 'string;maxlength=250';
						break;
					default:
						throw new sbException('unrecognized type: '.$aRow['e_type']);
						break;
				}
			}
			
			$sFormName = str_replace('.', '_', $aRow['s_key']);
			$sConfig = $sFormName.';'.$sConfig;
			
			$formRegistry->addInput($sConfig);
			$formRegistry->setValue($sFormName, $aRow['s_value']);
			
		}
		
		$formRegistry->addSubmit('$locale/sbSystem/actions/save');
		
		$stmtGetData->closeCursor();
		return ($formRegistry);

	}
	
}


?>