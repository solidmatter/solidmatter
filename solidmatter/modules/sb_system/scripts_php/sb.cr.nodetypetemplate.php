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
class sbCR_NodeTypeTemplate extends sbCR_NodeDefinition {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crSession) {
		
//		$this->crRepositoryStructure = $crRepositoryStructure;
//		$this->sNodeTypeName = $sNodeTypeName;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a mutable List of NodeDefinitionTemplate objects.
	* @param 
	* @return 
	*/
	public function getNodeDefinitionTemplates() {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a mutable List of PropertyDefinitionTemplate objects.
	* @param 
	* @return 
	*/
	public function getPropertyDefinitionTemplates() {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the abstract flag of the node type.
	* @param 
	* @return 
	*/
	public function setAbstract($bAbstractStatus) {
		$this->aNodeTypeInformation['isAbstract'] = $bAbstractStatus;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the names of the supertypes of the node type.
	* @param 
	* @return 
	*/
	public function setDeclaredSuperTypeNames($aNames) {
		$this->aNodeTypeInformation['SuperTypeNames'] = $aNames();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the mixin flag of the node type.
	* @param 
	* @return 
	*/
	public function setMixin($bMixin) {
		$this->aNodeTypeInformation['isMixin'] = $bMixin;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the name of the node type.
	* @param 
	* @return 
	*/
	public function setName($sName) {
		$this->aNodeTypeInformation['NodeTypeName'] = $sName;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the orderable child nodes flag of the node type.
	* @param 
	* @return 
	*/
	public function setOrderableChildNodes($bOrderable) {
		$this->aNodeTypeInformation['hasOrderableChildNodes'] = $bOrderable;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the name of the primary item.
	* @param 
	* @return 
	*/
	public function setPrimaryItemName($sName) {
		$this->aNodeTypeInformation['PrimaryItemName'] = $sName;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addViewDefinition() {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addMode($sMode, $sParentNodeType, $sChildNodeType, $bVisible, $bChoosable) {
		
	}
	
}

?>