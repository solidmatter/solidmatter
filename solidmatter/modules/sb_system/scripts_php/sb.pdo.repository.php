<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.pdo.system');
import('sb.pdo.repository.queries');

//------------------------------------------------------------------------------
/**
*/
class sbPDORepository extends sbPDOSystem {
		
	protected $sRepositoryPrefix	= '';
	protected $sWorkspacePrefix		= '';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct(string $sRepositoryID) {
		
		$elemRepository = CONFIG::getRepositoryConfig($sRepositoryID);
		
		if ($elemRepository['db'] == 'system') {
			System::getDatabase();
		} else {
			parent::__construct($elemRepository['db']);
		}
		
	}

}

?>