<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbCR_Workspace {
	
	private $sWorkspaceName			= '';
	private $sWorkspacePrefix		= '';
	
	private $crSession				= NULL;
	private $crNodeTypeManager		= NULL;
	private $crNodeTypeRegistry		= NULL;
	private $crObservationManager	= NULL;
	private $crQueryManager			= NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sWorkspaceName, $sWorkspacePrefix, $crSession) {
		$this->sWorkspaceName = $sWorkspaceName;
		$this->sWorkspacePrefix = $sWorkspacePrefix;
		$this->crSession = $crSession;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Clones the subtree at the node srcAbsPath in srcWorkspace to the new 
	* location at destAbsPath in this workspace.
	* @param 
	* @return 
	*/
	// FIXME: unsing clone as method name causes white screen of death
	/*public function clone($sSrcWorkspace, $sSrcAbsPath, $sDestAbsPath, $bRemoveExisting) {
		throw new UnsupportedRepositoryOperationException('multiple workspaces not supported yet');
	}
	
	//--------------------------------------------------------------------------
	/**
	* This method copies the node at srcAbsPath to the new location at 
	* destAbsPath.
	* @param 
	* @return 
	*/
	/*public function copy($sSrcAbsPath, $sDestAbsPath) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* This method copies the subtree at srcAbsPath in srcWorkspace to 
	* destAbsPath in this workspace.
	* @param 
	* @return 
	*/
	/*public function copy($sSrcWorkspace, $sSrcAbsPath, $sDestAbsPath) {
			
	}
	      
	//--------------------------------------------------------------------------
	/**
	* Returns an string array containing the names of all workspaces in this 
	* repository that are accessible to this user, given the Credentials that 
	* were used to get the Session tied to this Workspace.
	* @param 
	* @return 
	*/
	public function getAccessibleWorkspaceNames() {
		// TODO: implement listing of all workspaces
		throw new UnsupportedRepositoryOperationException();
	}
	
	      
	//--------------------------------------------------------------------------
	/**
	* Returns an org.xml.sax.ContentHandler which can be used to push SAX events
	* into the repository.
	* @param 
	* @return 
	*/
	public function getImportContentHandler($sParentAbsPath, $eUUIDBehavior) {
		throw new UnsupportedRepositoryOperationException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the name of the actual persistent workspace represented by this 
	* Workspace object.
	* @param 
	* @return 
	*/
	public function getName() {
		return ($this->sWorkspaceName);
	}
	      
	//--------------------------------------------------------------------------
	/**
	*  Returns the NamespaceRegistry object, which is used to access information
	* and (in level 2) set the mapping between namespace prefixes and URIs.
	* @param 
	* @return 
	*/
	public function getNamespaceRegistry() {
		if (is_null($this->crNamespaceRegistry)) {
			import('sb.cr.namespaceregistry');
			$this->crNamespaceRegistry = new sbCR_NamespaceRegistry($this->crSession);
		}
		return ($this->crNamespaceRegistry);
	}
	
	
	     
	//--------------------------------------------------------------------------
	/**
	* Returns the NodeTypeManager through which node type information can be
	* queried.
	* @param 
	* @return 
	*/
	public function getNodeTypeManager() {
		if (is_null($this->crNodeTypeManager)) {
			import('sb.cr.nodetypemanager');
			$this->crNodeTypeManager = new sbCR_NodeTypeManager($this->crSession);
		}
		return ($this->crNodeTypeManager);
	}
	      
	//--------------------------------------------------------------------------
	/**
	* If the the implementation supports observation this method returns the
	* ObservationManager object; otherwise it throws an
	* UnsupportedRepositoryOperationException.
	* @param 
	* @return 
	*/
	public function getObservationManager() {
		throw new UnsupportedRepositoryOperationException('observation api not supported yet');
		/*if (is_null($this->crObservationManager)) {
			import('sb.cr.observationmanager');
			$this->crObservationManager = new sbCR_ObservationManager($this->crSession);
		}
		return ($this->crObservationManager);*/
	}
	      
	//--------------------------------------------------------------------------
	/**
	* Gets the QueryManager.
	* @param 
	* @return 
	*/
	public function getQueryManager() {
		if (is_null($this->crQueryManager)) {
			import('sb.cr.querymanager');
			$this->crQueryManager = new sbCR_QueryManager($this->crSession);
		}
		return ($this->crQueryManager);
	}
	      
	//--------------------------------------------------------------------------
	/**
	* Returns the Session object through which this Workspace object was 
	* acquired.
	* @param 
	* @return 
	*/
	public function getSession() {
		return ($this->crSession);
	}
	      
	
	//--------------------------------------------------------------------------
	/**
	* Deserializes an XML document and adds the resulting item subtree 
	* as a child of the node at parentAbsPath.
	* @param 
	* @return 
	*/
	public function importXML($sParentAbsPath, $resIn, $eUUIDBehavior) {
		throw new UnsupportedRepositoryOperationException('XML import/export not implemented yet');
	}
	     
	
	//--------------------------------------------------------------------------
	/**
	* Moves the node at srcAbsPath (and its entire subtree) to the new location
	* at destAbsPath.
	* @param 
	* @return 
	*/
	public function move($sSrcAbsPath, $sDestAbsPath) {
		$this->crSession->move($sSrcAbsPath, $sDestAbsPath);
		$this->crSession->save();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Restores a set of versions at once.
	* @param 
	* @return 
	*/
	public function restore($aVersions, $bRemoveExisting) {
	 	throw new UnsupportedRepositoryOperationException('versioning not implemented yet');
	}
	
}

?>