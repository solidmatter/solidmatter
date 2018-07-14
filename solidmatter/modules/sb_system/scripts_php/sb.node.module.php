<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbNode_module extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbSystem/module/loadProperties/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbSystem/module/saveProperties/auxiliary';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function save() {
		
		// TODO: implement logic to override empty default values - also install/uninstall
		
		parent::save();
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function getStruture() {
		
		$sStructureFile = REPOSITORY_DEFINITION_FILE;
		
		return (simplexml_load_file($sStructureFile));
		
	}
	
}

?>