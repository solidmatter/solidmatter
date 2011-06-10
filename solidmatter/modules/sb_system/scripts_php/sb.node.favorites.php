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
class sbNode_favorites extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getElementModifications($elemSubject) {
		$elemSubject->setAttribute('uuid', $this->getIdentifier());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getIdentifier() {
		
		static $nodeUserFavorites = NULL;
		
		if ($this->getParent()->getPrimaryNodeType() != 'sbSystem:User') {
			$nodeUserFavorites = User::getNode()->getNode('favorites');
			return ($nodeUserFavorites->getIdentifier());
		} else {
			return (parent::getIdentifier());
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getChildren($sMode = 'debug', $aRequiredAuthorisations = array()) {
		
		static $nodeUserFavorites = NULL;
		
		if ($this->getParent()->getPrimaryNodeType() != 'sbSystem:User') {
			$nodeUserFavorites = User::getNode()->getNode('favorites');
			return ($nodeUserFavorites->getChildren('debug', $aRequiredAuthorisations));
		} else {
			return (parent::getChildren($sMode, $aRequiredAuthorisations));
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getNumberOfChildren($sMode = NULL) {
		
		static $nodeUserFavorites = NULL;
		
		if ($this->getParent()->getPrimaryNodeType() != 'sbSystem:User') {
			$nodeUserFavorites = User::getNode()->getNode('favorites');
			return ($nodeUserFavorites->getNumberOfChildren('debug'));
		} else {
			return (parent::getNumberOfChildren($sMode));
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getChildByName($sName) {
		
	static $nodeUserFavorites = NULL;
		
		if ($this->getParent()->getPrimaryNodeType() != 'sbSystem:User') {
			$nodeUserFavorites = User::getNode()->getNode('favorites');
			return ($nodeUserFavorites->getChildByName($sName));
		} else {
			return (parent::getChildByName($sName));
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function isAuthorised($sAuthorisation, $sEntityID = NULL) {
		
		if ($this->getParent()->getPrimaryNodeType() != 'sbSystem:User' && $sAuthorisation == 'read') {
			return (TRUE);
		} else {
			return (parent::isAuthorised($sAuthorisation, $sEntityID));
		}
		
	}
	
}

?>