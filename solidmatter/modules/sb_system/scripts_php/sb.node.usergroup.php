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
class sbNode_usergroup extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __init() {
		
		parent::__init();
		
		if ($this->elemSubject->getAttribute('uid') == 'sbSystem:Admins') {
			$this->elemSubject->setAttribute('displaytype', 'sbSystem_Usergroup_Admins');
		}
		if ($this->elemSubject->getAttribute('uid') == 'sbSystem:Guests') {
			$this->elemSubject->setAttribute('displaytype', 'sbSystem_Usergroup_Guests');
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadChildren']['byMode'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadViews() {
		
		parent::loadViews();
		
		$sUID = $this->getProperty('sbcr:uid');
		if ($sUID == 'sbSystem:Guests' || $sUID == 'sbSystem:Admins') {
			unset ($this->aViews['properties']);
		}
		
	}
	
}

	

?>