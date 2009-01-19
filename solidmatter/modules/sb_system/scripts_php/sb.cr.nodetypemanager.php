<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.nodetype');
import('sb.cr.repository.structure');

//------------------------------------------------------------------------------
/**
*/
class sbCR_NodeTypeManager {
	
	protected $crSession				= NULL;
	protected $crRepositoryStructure	= NULL;
	
	protected $aNodeTypeHierarchy		= array();
	
	protected $aNodeTypes				= array();
	protected $aMixinNodeTypes 			= array();
	protected $aPrimaryNodeTypes		= array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crSession) {
		
		$this->crSession = $crSession;
		$this->crRepositoryStructure = new sbCR_RepositoryStructure($crSession);
		
		// init existing nodetypes (only names!)
		$this->aNodeTypeHierarchy = $this->crRepositoryStructure->getNodeTypeHierarchy();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the named node type.
	* @param 
	* @return 
	*/
	public function getNodeType($sNodeTypeName) {
		if (!$this->hasNodeType($sNodeTypeName)) {
			throw new NoSuchNodeTypeException(__CLASS__.': '.$sNodeTypeName);
		}
		if (!isset($this->aNodeTypes[$sNodeTypeName])) {
			$this->aNodeTypes[$sNodeTypeName] = $this->crRepositoryStructure->getNodeType($sNodeTypeName);
		}
		return ($this->aNodeTypes[$sNodeTypeName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an iterator over all available node types (primary and mixin).
	* @param 
	* @return 
	*/
	public function getAllNodeTypes() {
		throw new UnsupportedRepositoryOperationException();
		return (array_merge($this->aPrimaryNodeTypes, $this->aMixinNodeTypes));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an iterator over all available primary node types.
	* @param 
	* @return 
	*/
	public function getPrimaryNodeTypes() {
		throw new UnsupportedRepositoryOperationException();
		return ($this->aPrimaryNodeTypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	*  Returns an iterator over all available mixin node types.
	* @param 
	* @return 
	*/
	public function getMixinNodeTypes() {
		throw new UnsupportedRepositoryOperationException();
		return ($this->aMixinNodeTypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an empty NodeDefinitionTemplate which can then be used to create a child node definition and attached to a NodeTypeTemplate.
	* @param 
	* @return 
	*/
	public function createNodeDefinitionTemplate() {
		throw new UnsupportedRepositoryOperationException();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an empty NodeTypeTemplate which can then be used to define a node type and passed to NodeTypeManager.registerNodeType.
	* OR
	* Returns a NodeTypeTemplate holding the specified node type definition.
	* @param 
	* @return 
	*/
	public function createNodeTypeTemplate($ntdNewType = NULL) {
		import('sb.cr.nodetypetemplate');
		return(new sbCR_NodeTypeTemplate($this->crSession));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an empty PropertyDefinitionTemplate which can then be used to create a property definition and attached to a NodeTypeTemplate.
	* @param 
	* @return 
	*/
	public function createPropertyDefinitionTemplate() {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if a node type with the specified name is registered.
	* @param 
	* @return 
	*/
	public function hasNodeType($sNodeTypeName) {
		if (!isset($this->aNodeTypeHierarchy[$sNodeTypeName])) {
			return (FALSE);
		}
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Registers a new node type or updates an existing node type using the specified definition and returns the resulting NodeType object.
	* @param 
	* @return 
	*/
	public function registerNodeType($ntdNewType, $bAllowUpdate = TRUE) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Registers or updates the specified Collection of NodeTypeDefinition objects.
	* @param 
	* @return 
	*/
	public function registerNodeTypes($aNodetypes, $bAllowUpdate = TRUE) {
		foreach ($aNodetypes as $sNodetype) {
			$this->registerNodeType($sNodetype, $bAllowUpdate);	
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Unregisters the specified node type.
	* @param 
	* @return 
	*/
	public function unregisterNodeType($sNodeTypeName) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Unregisters the specified set of node types.
	* @param 
	* @return 
	*/
	public function unregisterNodeTypes($aNodeTypeNames) {
		foreach ($aNodeTypeNames as $sNodeTypeName) {
			$this->unregisterNodeType($sNodeTypeName);	
		}
	}
	
}

?>