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
class sbNode_user extends sbNode {
	
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbSystem/user/loadProperties/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbSystem/user/saveProperties/auxiliary';
	}
	
	public function saveNode() {
		if ($this->isNew()) {
			$this->setProperty('security_activationkey', uuid());
			$this->setProperty('security_failedlogins', 0);
			$this->setProperty('info_successfullogins', 0);
			$this->setProperty('info_silentlogins', 0);
			$this->setProperty('info_totalfailedlogins', 0);
		}
		parent::saveNode();
	}
	
}

?>