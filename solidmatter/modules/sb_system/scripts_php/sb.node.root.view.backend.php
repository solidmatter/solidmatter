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
class sbView_root_backend extends sbView {
	
	/**
	* 
	*/
	protected $bLoginRequired = TRUE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function init() {
		parent::init();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		//global $_RESPONSE;
		
		/*if ($sAction == 'show_outer') {
			$_RESPONSE->addSystemMeta('backendview', 'outer');
		} else {
			$_RESPONSE->addSystemMeta('backendview', 'inner');
		}*/
		
		//$_RESPONSE->setStylesheet('root.backend.xsl');
		
		return (NULL);
		
		
	}
	
	
	
}


?>