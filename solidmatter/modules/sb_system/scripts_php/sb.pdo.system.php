<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter
*	@subpackage sbPDO
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.pdo');
import('sb.pdo.repository');
import('sb.pdo.system.queries');
import('sb.system.errors');

//------------------------------------------------------------------------------
/**
* 
*/
class sbPDOSystem extends sbPDORepository {
	
	protected $sGlobalPrefix		= 'global';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function prepareQuery($sQuery) {
	
		$aSearch = array();
		$aReplace = array();
		$aSearch[] = '{PREFIX_REPOSITORY}';
		$aReplace[] = $this->sRepositoryPrefix;
		$aSearch[] = '{PREFIX_WORKSPACE}';
		$aReplace[] = $this->sWorkspacePrefix;
		$aSearch[] = '{PREFIX_SYSTEM}';
		$aReplace[] = $this->sGlobalPrefix;
		$sQuery = str_replace($aSearch, $aReplace, $sQuery);
		
		return ($sQuery);
		
	}
	
}

?>