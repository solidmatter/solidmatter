<?php

//------------------------------------------------------------------------------
/**
 *	@package solidMatter
 *	@author	()((() [Oliver Müller]
 *	@version 1.00.00
 */
//------------------------------------------------------------------------------

//-----------------------------------------------------------------------------
/**
 * 
 * 
 *
 */
class sbConfigurationWriter {
		
	// container member variable for the configuration as SimpleXMLElement
	protected $elemConfigXML = NULL;
	
	//-------------------------------------------------------------------------
	/**
	 * 
	 */
	public function __construct() {
		$this->elemConfigXML = simplexml_load_file(CONFIG::DIR.CONFIG::FILE);
	}
	
	//-------------------------------------------------------------------------
	/**
	 * Saves the modified configuration file.
	 */
	public function save() {
		$this->elemConfigXML->saveXML(CONFIG::DIR.CONFIG::FILE);
	}
	
	//-------------------------------------------------------------------------
	/**
	 * 
	 * @param string ID of the repository
	 * @return 
	 */
	public function addRepository(string $sRepositoryID, string $sPrefix, string $sDatabaseID) {
		$elemRepositories = $this->elemConfigXML->repositories;
		$elemRepository = $elemRepositories->addChild($sRepositoryID);
		$elemRepository->addAttribute('prefix', $sPrefix);
		$elemRepository->addAttribute('db', $sDatabaseID);
	}
	
}

?>