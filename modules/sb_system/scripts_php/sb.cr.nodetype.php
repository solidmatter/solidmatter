<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.nodedefinition');

//------------------------------------------------------------------------------
/** TODO: complete this
*/
class sbCR_NodeType extends sbCR_NodeDefinition {
	
	private $aDeclaredSupertypes = NULL;
	private $aSupertypes = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this node type allows the addition of a child node called 
	* childNodeName without specific node type information (that is, given the 
	* definition of this parent node type, the child node name is sufficient to 
	* determine the intended child node type).
	* Returns true if this node type allows the addition of a child node called 
	* childNodeName of node type nodeTypeName.
	* @param 
	* @return boolean
	*/
	public function canAddChildNode($sChildNodeName, $sNodeTypeName = NULL) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if removing the child node called nodeName is allowed by 
	* this node type.
	* @param 
	* @return boolean
	*/
	public function canRemoveNode($sNodeName) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if removing the property called propertyName is allowed by 
	* this node type.
	* @param 
	* @return boolean
	*/
	public function canRemoveProperty($sPropertyName) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if setting propertyName to value(s) is allowed by this node
	* type. 
	* @param 
	* @param multiple 
	* @return 
	*/
	public function canSetProperty($sPropertyName, $mValue) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array containing the child node definitions of this node type.
	* @param 
	* @return NodeDefinition[]
	*/
	public function getChildNodeDefinitions() {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the direct supertypes of this node type in the node type 
	* inheritance hierarchy, that is, those actually declared in this node type.
	* @param 
	* @return NodeType[]
	*/
	public function getDeclaredSupertypes() {
		if (is_null($this->aDeclaredSupertypes)) {
			$this->aDeclaredSupertypes = $this->crRepositoryStructure->getDeclaredSupertypes($this->sNodeTypeName);
		}
		return ($this->aDeclaredSupertypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns all supertypes of this node type in the node type inheritance 
	* hierarchy.
	* @param 
	* @return NodeType[]
	*/
	public function getSupertypes() {
		if (is_null($this->aSupertypes)) {
			$this->aSupertypes = $this->crRepositoryStructure->getSupertypes($this->sNodeTypeName);
		}
		return ($this->aSupertypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: Returns the names of all supertypes of this node type.
	* @param 
	* @return java.lang.String[]
	*/
	public function getSupertypeNames() {
		return ($this->crRepositoryStructure->getSupertypeNames($this->sNodeTypeName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array containing the property definitions of this node type.
	* @param 
	* @return PropertyDefinition[]
	*/
	public function getPropertyDefinitions() {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this node type is nodeTypeName or a subtype of 
	* nodeTypeName, otherwise returns false.
	* @param 
	* @return boolean
	*/
	public function isNodeType($sNodeTypeName) {
		if ($this->sNodeTypeName == $sNodeTypeName || in_array($sNodeTypeName, $this->getDeclaredSupertypeNames())) {
			return (true);
		}
		foreach ($this->getDeclaredSupertypes() as $crNodeType) {
			if ($crNodeType->isNodeType($sNodeTypeName)) {
				return (TRUE);
			}
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSupportedViews() {
		return ($this->crRepositoryStructure->getSupportedViews($this->sNodeTypeName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSupportedAuthorisations() {
		return ($this->crRepositoryStructure->getSupportedAuthorisations($this->sNodeTypeName));
	}
		
	public function getDebugInfo() {
		
		$aInfo = array();
		$aInfo['DeclaredSupertypes'] = $this->getDeclaredSupertypeNames();
		$aInfo['Supertypes'] = $this->getSupertypeNames();
		$aInfo['SupportedViews'] = $this->getSupportedViews();
		$aInfo['SupportedAuthorisations'] = $this->getSupportedAuthorisations();
		
		return ($aInfo);
			
	}
	
	
}

?>