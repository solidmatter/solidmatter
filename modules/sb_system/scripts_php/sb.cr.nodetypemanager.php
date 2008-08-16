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
	* 
	* @param 
	* @return 
	*/
	public function getNodeType($sNodeTypeName) {
		if (!isset($this->aNodeTypeHierarchy[$sNodeTypeName])) {
			throw new NoSuchNodeTypeException();
		}
		if (!isset($this->aNodeTypes[$sNodeTypeName])) {
			$this->aNodeTypes[$sNodeTypeName] = $this->crRepositoryStructure->getNodeType($sNodeTypeName);
		}
		return ($this->aNodeTypes[$sNodeTypeName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAllNodeTypes() {
		throw new UnsupportedRepositoryOperationException();
		return (array_merge($this->aPrimaryNodeTypes, $this->aMixinNodeTypes));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPrimaryNodeTypes() {
		throw new UnsupportedRepositoryOperationException();
		return ($this->aPrimaryNodeTypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getMixinNodeTypes() {
		throw new UnsupportedRepositoryOperationException();
		return ($this->aMixinNodeTypes);
	}
	
}

?>