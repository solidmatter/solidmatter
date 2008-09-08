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
class sbView_properties extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'edit':
				
				$formProperties = $this->buildForm();
				$formProperties->saveDOM();
				$_RESPONSE->addData($formProperties);
				
				return (NULL);
				
			case 'save':
				
				$formProperties = $this->buildForm();
				$formProperties->recieveInputs();
				
				if ($this->checkInputs($formProperties)) {
					
					if ($formProperties->getValue('label') != $this->nodeSubject->getProperty('label')) {
						$_RESPONSE->addCommand('reloadTree');
					}
					
					// set properties
					// TODO: this sucks a bit, i guess
					$aValues = $formProperties->getValues();
					foreach ($aValues as $sName => $mValue) {
						// check for tag input
						if ($sName == 'tags_'.$this->nodeSubject->getProperty('jcr:uuid')) { // tags
							$this->nodeSubject->setTags(explode(',', $mValue));
						} else { // normal property
							$this->nodeSubject->setProperty($sName, $mValue);
						}
					}
					$this->nodeSubject->save();
					
					$formProperties->saveDOM();
					
					$_RESPONSE->addData($formProperties);
					
					return (NULL);
					
				} else {
					
					$formProperties->saveDOM();
					$_RESPONSE->addData($formProperties);
					
					return (NULL);
					
				}
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
	
	protected function buildForm() {
		return ($this->nodeSubject->buildForm('properties'));
	}
	
	protected function checkInputs($formProperties) {
		return ($formProperties->checkInputs());
	}
	
}

?>