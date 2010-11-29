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
class sbNode_root extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPath($sProperty = 'name') {
		return ('/');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getParent() {
		throw new ItemNotFoundException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getParents() {
		throw new ItemNotFoundException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPaths() {
		return (array('/'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getName() {
		return ('');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function remove() {
		throw new sbException('Deleting the ROOT node? are you crazy?');
	}
	
	/*
	 * getDeclaringNodeType(): A valid NodeType object. See 4.7.20.2 Root Declaring Node Type, below. 
	 * isMandatory(): true 
	 * isAutoCreated(): true 
	 * isProtected(): false 
	 * allowsSameNameSiblings(): false 
	 * getOnParentVersion(): VERSION, if versioning is supported and the root node is capable of being made versionable, IGNORE otherwise. 
	 * getDefaultPrimaryType(): A valid non-null NodeType object. See 4.7.20.3 Root Node Type, below. 195
	 * getRequiredPrimaryTypes(): An array containing a single NodeType object identical with that returned by getDefaultPrimaryType.
	*/
	
	
}

	

?>