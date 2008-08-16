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
class sbNode_useraccounts extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadUsers']['basic'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
		$this->aQueries['loadGroups']['basic'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
		$this->aQueries['loadChildren']['byMode'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadUsers() {
		
		$stmtUsers = $this->crSession->prepareKnown($this->aQueries['loadUsers']['basic']);
		$stmtUsers->bindValue('parent_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_INT);
		$stmtUsers->bindValue('mode', 'loadusers', PDO::PARAM_STR);
		$stmtUsers->execute();
		
		return ($stmtUsers->fetchDOM('users'));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadGroups() {
			
		$stmtUsers = $this->crSession->prepareKnown($this->aQueries['loadGroups']['basic']);
		$stmtUsers->bindValue('parent_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_INT);
		$stmtUsers->bindValue('mode', 'loadgroups', PDO::PARAM_STR);
		$stmtUsers->execute();
		
		return ($stmtUsers->fetchDOM('groups'));
		
	}
	
	
}

	

?>