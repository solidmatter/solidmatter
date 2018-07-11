<?php

//------------------------------------------------------------------------------
/**
* @package solidMatter[sbCR]
* @author	()((() [Oliver Müller]
* @version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbCR {
	
	// basic info about existing repositories 
	private static $sxmlRepositoryDefinitions = NULL;
	
	// filled with repository ID as key when iterating over the definition 
	private static $aRepositories = NULL;
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function getRepositoryDefinition(string $sRepositoryID) {
		
		self::loadRepositoryDefinitions();
		
		// check in repository exists
		foreach (self::$sxmlRepositoryDefinitions->repository as $elemRepository) {
			self::$aRepositories[(string) $elemRepository['id']] = $elemRepository;
		}
		if (!isset(self::$aRepositories[$sRepositoryID])) {
			throw new RepositoryException('no such repository "'.$sRepositoryID.'"');
		}
		
		return (self::$aRepositories[$sRepositoryID]);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function loadRepositoryDefinitions(string $sDefinitionFile = NULL, bool $bForceReload = FALSE) {
		if (self::$sxmlRepositoryDefinitions == NULL || $bForceReload) {
			if ($sDefinitionFile == NULL) {
				self::$sxmlRepositoryDefinitions = simplexml_load_file(CONFIG::DIR.CONFIG::REPOSITORIES);
			} else {
				self::$sxmlRepositoryDefinitions = simplexml_load_file($sDefinitionFile);
			}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function getSimpleXML() {
		
	}
	
	
}

?>