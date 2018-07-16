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
		return (CONFIG::getRepositoryConfig($sRepositoryID));
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
				self::$sxmlRepositoryDefinitions = simplexml_load_file(CONFIG::DIR.CONFIG::FILE);
			} else {
				self::$sxmlRepositoryDefinitions = simplexml_load_file($sDefinitionFile);
			}
		}
		return (self::$sxmlRepositoryDefinitions);
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function createRepository(string $sRepositoryID, string $sPrefix) {
		import('sb.pdo.repository.queries.repositories');
		$sxmlDefinition = self::getRepositoryDefinition($sRepositoryID);
		$pdoRepository = new sbPDORepository($sxmlDefinition);
		$stmtCreate = $pdoRepository->prepareKnown('sbCR/repository/create');
		$stmtCreate->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function initRepository(string $sRepositoryID) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function createWorkspace($sID) {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function initWorkspace($sID) {
		
	}
	
	
}

?>
