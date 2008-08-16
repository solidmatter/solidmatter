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
class sbView_useraccounts_gatherdata extends sbView {
	
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