<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.nodedefinition');
import('sb.cr.viewdefinition');

//------------------------------------------------------------------------------
/** TODO: complete this
*/
class sbCR_NodeType extends sbCR_NodeDefinition {
	
	private $aDeclaredSupertypes = NULL;
	private $aSupertypes = NULL;
	private $aDeclaredViewDefinitions = NULL;
	private $aSupportedViews = array();
	
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
		// TODO: implement hierarchy constraints
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if removing the child node called nodeName is allowed by 
	* this node type.
	* @param 
	* @return boolean
	*/
	public function canRemoveNode($sNodeName) {
		// TODO: implement hierarchy constraints (currently nodes themselves know if they can be deleted)
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if removing the property called propertyName is allowed by 
	* this node type.
	* @param 
	* @return boolean
	*/
	public function canRemoveProperty($sPropertyName) {
		// TODO: improve property handling (support removing properties, unsolved: arbitrary properties)
		return (FALSE);
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
		// check property existence
		// check if property is protected
		// check property constraints
		// TODO: implement restrictions
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array containing the child node definitions of this node type.
	* @param 
	* @return NodeDefinition[]
	*/
	public function getChildNodeDefinitions() {
		// 
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
			$this->aDeclaredSupertypes = $this->crRepositoryStructure->getDeclaredSupertypes($this->aNodeTypeInformation['NodeTypeName']);
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
			$this->aSupertypes = $this->crRepositoryStructure->getSupertypes($this->aNodeTypeInformation['NodeTypeName']);
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
		return ($this->crRepositoryStructure->getSupertypeNames($this->aNodeTypeInformation['NodeTypeName']));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array containing the property definitions of this node type.
	* @param 
	* @return PropertyDefinition[]
	*/
	public function getPropertyDefinitions() {
		throw new LazyBastardException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this node type is nodeTypeName or a subtype of 
	* nodeTypeName, otherwise returns false.
	* @param 
	* @return boolean
	*/
	public function isNodeType($sNodeTypeName) {
		if ($this->aNodeTypeInformation['NodeTypeName'] == $sNodeTypeName || in_array($sNodeTypeName, $this->getDeclaredSupertypeNames())) {
			return (TRUE);
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
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function getViewDefinition($sViewName) {
		
		switch ($sViewName) {
			case 'security':
				$vdCurrentView = new sbCR_ViewDefinition(
					$this,
					$this->aNodeTypeInformation['NodeTypeName'],
					'security',
					'sbView_security',
					'sbSystem:sb.node.view.security',
					TRUE,
					1
				);
				break;
			case 'debug':
				$vdCurrentView = new sbCR_ViewDefinition(
					$this,
					$this->aNodeTypeInformation['NodeTypeName'],
					'debug',
					'sbView_debug',
					'sbSystem:sb.node.view.debug',
					TRUE,
					2
				);
				break;
			default:
				$vdCurrentView = $this->crRepositoryStructure->getViewDefinition($this->aNodeTypeInformation['NodeTypeName'], $sViewName);
		}
		
		return ($vdCurrentView);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function getSupportedViews($nodeSubject = NULL) {
		return ($this->crRepositoryStructure->getSupportedViews($this->aNodeTypeInformation['NodeTypeName'], $nodeSubject));
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function getDeclaredViewDefinitions() {
		return ($this->crRepositoryStructure->getDeclaredViewDefinitions($this->aNodeTypeInformation['NodeTypeName']));
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function getSupportedAuthorisations() {
		return ($this->crRepositoryStructure->getSupportedAuthorisations($this->aNodeTypeInformation['NodeTypeName']));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
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