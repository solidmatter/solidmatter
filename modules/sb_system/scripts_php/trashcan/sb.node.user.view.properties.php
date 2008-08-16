<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.node.view.properties');

//------------------------------------------------------------------------------
/**
*/
class sbView_user_properties extends sbView_properties {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		switch ($sAction) {
			
			case 'edit':
				
				$formProperties = $this->buildForm();
				$formProperties->saveDOM();
				
				$_RESPONSE->addData($formProperties);
				
				return (NULL);
				
			case 'save':
				
				$formProperties = $this->buildForm();
				$formProperties->recieveInputs();
				if ($formProperties->checkInputs()) {
					// TODO: this sucks a bit, i guess
					$aValues = $formProperties->getValues();
					$this->nodeSubject->setProperties($aValues);
					$this->nodeSubject->save();
					$formProperties->saveDOM();
					$_RESPONSE->addData($formProperties);
					return (NULL);
				} else {
					$formProperties->saveDOM();
					$_RESPONSE->addData($formProperties);
					return (NULL);
				}
				//break;
				
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
		
		//$this->nodeSubject->loadAttributes('auxiliary');
		
		$formProperties = new sbDOMForm(
			'properties',
			'$locale/system/general/labels/properties',
			'backend.nodeid='.$this->nodeSubject->getProperty('jcr:uuid').'&view=properties&action=save',
			$this->crSession
		);
		$formProperties->addInput('label;string;required=TRUE', '$locale/system/general/labels/nickname');
		$formProperties->addInput('name;urlsafe;required=TRUE', '$locale/system/general/labels/login');
		$formProperties->addInput('email;email;required=TRUE', '$locale/system/general/labels/email');
		$formProperties->addInput('comment;text', '$locale/system/general/labels/comment');
		$formProperties->addInput('activated;checkbox', '$locale/system/general/labels/activated');
		$formProperties->addInput('createdat;string', '$locale/system/general/labels/activated');
		$formProperties->setValue('label', $this->nodeSubject->getProperty('label'));
		$formProperties->setValue('name', $this->nodeSubject->getProperty('name'));
		$formProperties->setValue('email', $this->nodeSubject->getProperty('email'));
		$formProperties->setValue('comment', $this->nodeSubject->getProperty('comment'));
		$formProperties->setValue('activated', $this->nodeSubject->getProperty('activated'));
		$formProperties->setValue('createdat', $this->nodeSubject->getProperty('createdat'));
		$formProperties->disable('createdat');
		$formProperties->addSubmit('$locale/system/general/actions/save');
		
		//$this->extendForm($formProperties);
		
		return ($formProperties);
		
	}
	
}

?>