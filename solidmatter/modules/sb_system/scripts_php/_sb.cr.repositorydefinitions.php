<?php

//------------------------------------------------------------------------------
/**
* @package solidMatter[sbCR]
* @author	()((() [Oliver Müller]
* @version 1.00.00
*/
//------------------------------------------------------------------------------

if(!defined('REPOSITORY_DEFINITION_FILE')) { define('REPOSITORY_DEFINITION_FILE', 'repositories.xml'); }

//------------------------------------------------------------------------------
/**
*/
class sbCR_RepositoryDefinitions {
	
	private $sxmlRepositoryDefinitions = NULL;
		
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct() {
		$this->sxmlRepositoryDefinitions = simplexml_load_file(REPOSITORY_DEFINITION_FILE);
	}
	
	
	
}

?>