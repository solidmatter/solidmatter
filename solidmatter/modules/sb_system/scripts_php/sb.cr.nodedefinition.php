<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/** TODO: complete this
*/
class sbCR_NodeDefinition {
	
	protected $sNodeTypeName;
	
	protected $crRepositoryStructure = NULL;
	
	protected $aDeclaredSupertypeNames = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crRepositoryStructure, $sNodeTypeName) {
		
		$this->crRepositoryStructure = $crRepositoryStructure;
		$this->sNodeTypeName = $sNodeTypeName;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array containing the child node definitions actually declared in this node type.
	* @param 
	* @return NodeDefinition[]
	*/
	public function getDeclaredChildNodeDefinitions() {
		//return ($this->aNodeTypeInformation['ChildNodeDefinitions']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array containing the property definitions actually declared 
	* in this node type.
	* @param 
	* @return PropertyDefinition[]
	*/
	public function getDeclaredPropertyDefinitions() {
		//return ($this->aNodeTypeInformation['PropertyDefinitions']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the names of the supertypes actually declared in this node type.
	* @param 
	* @return java.lang.String[]
	*/
	public function getDeclaredSupertypeNames() {
		return ($this->crRepositoryStructure->getDeclaredSupertypeNames($this->sNodeTypeName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the name of the node type.
	* @param 
	* @return java.lang.String
	*/
	public function getName() {
		return ($this->sNodeTypeName);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the name of the primary item (one of the child items of the nodes of this node type).
	* @param 
	* @return java.lang.String
	*/
	public function getPrimaryItemName() {
		//return ($this->aNodeTypeInformation['PrimaryItemName']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if nodes of this type must support orderable child nodes; returns false otherwise.
	* @param 
	* @return boolean
	*/
	public function hasOrderableChildNodes() {
		//return ($this->aNodeTypeInformation['hasOrderableChildNodes']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this is an abstract node type; returns false otherwise.
	* @param 
	* @return boolean
	*/
	public function isAbstract() {
		//return ($this->aNodeTypeInformation['isAbstract']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this is a mixin type; returns false if it is primary.
	* @param 
	* @return boolean
	*/
	public function isMixin() {
		//return ($this->aNodeTypeInformation['isMixin']);
	}
	
}

?>