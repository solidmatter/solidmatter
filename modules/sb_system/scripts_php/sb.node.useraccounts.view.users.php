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
class sbView_useraccounts_users extends sbView {
	
	private $aQueries = array();
	
	protected function init() {
		$this->aQueries['loadUsers']['basic'] = 'sb_system/node/useraccounts/loadUsers/basic';
		$this->aQueries['loadGroups']['basic'] = 'sb_system/node/useraccounts/loadGroups/basic';
	}
	
	public function execute($sAction) {
		
		switch ($sAction) {
			
			case 'users':
				return ($this->nodeSubject->loadUsers());
				//break;
				
			case 'groups':
				return ($this->nodeSubject->loadGroups());
				//break;			
			
		}
		
	}
	
}


?>