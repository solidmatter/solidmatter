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
	public static function getRepositoryDefinition(string $sRepositoryID) : SimpleXMLElement {
		return (CONFIG::getRepositoryConfig($sRepositoryID));
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function getRepository(string $sRepositoryID) : sbCR_Repository {
		
		$elemRepositoryDefinition = self::getRepositoryDefinition($sRepositoryID);
		
		// init database
		$sRepositoryPrefix = (string) $elemRepositoryDefinition['prefix'];
		$sDBID = (string) $elemRepositoryDefinition['db'];
		
		if ($sDBID == 'system') {
			$DB = System::getDatabase();
		} else {
			$DB = new sbPDOSystem($sDBID);
		}
		
		return (new sbCR_Repository($DB, $sRepositoryID, $sRepositoryPrefix));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public static function createRepository(string $sRepositoryID, string $sPrefix, string $sDatabaseID) {
		import('sb.pdo.setup.queries.repositories');
		$pdoRepository = new sbPDOSystem($sDatabaseID);
		$pdoRepository->setRewrite('{PREFIX_REPOSITORY}', $sPrefix);
		$stmtCreate = $pdoRepository->prepareKnown('sbCR/repository/createTables');
		$stmtCreate->execute();
		$stmtCreate->closeCursor();
		$stmtInit = $pdoRepository->prepareKnown('sbCR/repository/createEntries');
		$stmtInit->execute();
// 		$stmtInit->debug();
		$stmtInit->closeCursor();
		CONFIG::addRepository($sRepositoryID, $sPrefix, $sDatabaseID);
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
// 	public static function initRepository(string $sRepositoryID, string $sPrefix, string $sDatabaseID) {
// 		import('sb.pdo.repository.queries.repositories');
// 		$pdoRepository = new sbPDOSystem($sDatabaseID);
// 		$pdoRepository->setRewrite('{PREFIX_REPOSITORY}', $sPrefix);
// 		$stmtInit = $pdoRepository->prepareKnown('sbCR/repository/createEntries');
// 		$stmtInit->execute();
// 		$stmtInit->debug();
// 	}
	
}

?>
