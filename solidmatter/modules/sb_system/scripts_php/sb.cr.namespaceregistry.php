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
class sbCR_NamespaceRegistry {
	
	protected $aNamespaces = NULL;
	
	protected $crSession = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crSession) {
		$this->crSession = $crSession;
		$stmtNamespaces = $this->crSession->prepare('sbCR/NamespaceRegistry/loadNamespaces');
		$stmtNamespaces->execute();
		foreach ($stmtNamespaces as $aRow) {
			$this->aNamespaces[$aRow['s_prefix']] = $aRow['s_uri'];	
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the prefix to which the given URI is mapped
	* @param 
	* @return 
	*/
	public function getPrefix($sURI) {
		$aTemp = array_flip($this->aNamespaces);
		if (!isset($aTemp[$sURI])) {
			throw new NamespaceException('namespace with URI "'.$sURI.'" does not exist');
		}
		return ($aTemp[$sURI]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array holding all currently registered prefixes.
	* @param 
	* @return 
	*/
	public function getPrefixes() {
		return (array_flip($this->aNamespaces));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the URI to which the given prefix is mapped.
	* @param 
	* @return 
	*/
	public function getURI($sPrefix) {
		if (!isset($this->aNamespaces[$sPrefix])) {
			throw new NamespaceException('namespace with prefix "'.$sPrefix.'" does not exist');
		}
		return ($this->aNamespaces[$sPrefix]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array holding all currently registered URIs.
	* @param 
	* @return 
	*/
	public function getURIs() {
		return ($this->aNamespaces);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets a one-to-one mapping between prefix and URI in the global namespace 
	* registry of this repository.
	* @param 
	* @return 
	*/
	public function registerNamespace($sPrefix, $sURI) {
		throw new LazyBastardException('registering through API not supported yet');
	}
	
	//--------------------------------------------------------------------------
	/**
	* Removes a namespace mapping from the registry.
	* @param 
	* @return 
	*/
	public function unregisterNamespace($sPrefix) {
		throw new LazyBastardException('unregistering through API not supported yet');
	}
		
}

?>