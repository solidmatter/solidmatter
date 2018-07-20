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
	private static $sDefinitionFile = NULL;
	
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
	public static function createRepository(string $sRepositoryID, string $sPrefix, string $sDatabaseID) {
		import('sb.pdo.repository.queries.repositories');
		$pdoRepository = new sbPDOSystem($sDatabaseID);
		$pdoRepository->addRewrite('{PREFIX_REPOSITORY}', $sPrefix);
		$stmtCreate = $pdoRepository->prepareKnown('sbCR/repository/createTables');
		$stmtCreate->execute();
		$stmtCreate->closeCursor();
		$stmtInit = $pdoRepository->prepareKnown('sbCR/repository/createEntries');
		$stmtInit->execute();
		CONFIG::addRepository($sRepositoryID, $sPrefix, $sDatabaseID);
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function createWorkspace(string $sRepositoryID, string $sWorkspaceID, string $sPrefix) {
		
		
		
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
