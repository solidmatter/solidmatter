<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.system.security');

//------------------------------------------------------------------------------
/**
*/
class sbNode_user extends sbNode {
	
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbSystem/user/loadProperties/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbSystem/user/saveProperties/auxiliary';
	}
	
	public function saveNode() {
		
		// initialize historic properties if user is created
		if ($this->isNew()) {
			$this->setProperty('security_activationkey', uuid());
			$this->setProperty('security_failedlogins', 0);
			$this->setProperty('info_successfullogins', 0);
			$this->setProperty('info_silentlogins', 0);
			$this->setProperty('info_totalfailedlogins', 0);
		}
		
		// get a salted string of the password before storage (only if password is not already salted - which indicates a newly assigned password)
		$sPass = $this->getProperty('security_password');
		if (!is_salted_password($sPass)) {
			$sStorablePassword = salt_password($sPass, $this->getProperty('jcr:uuid'));
			$this->setProperty('security_password', $sStorablePassword);
		}
		
		parent::saveNode();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function modifyForm($formCurrent, $sMode) {
		if ($sMode == 'properties' && !$this->isNew()) {
			$formCurrent->removeInput('security_password');
		}
	}
	
}

?>