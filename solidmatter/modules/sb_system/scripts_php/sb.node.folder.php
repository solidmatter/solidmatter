<?php
//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbNode_folder extends sbNode {
	
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadChildren']['byMode'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getDefaultView() {
		$sDefaultView = sbSession::getData('sb_system:folder:defaultView');
		if ($sDefaultView != NULL) {
			return ($sDefaultView);
		} else {
			return (parent::getDefaultView());	
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function callView($sView = NULL, $sAction = NULL) {
		if ($sView == 'thumbnails' || $sView == 'list') {
			sbSession::addData('sb_system:folder:defaultView', $sView);
		}
		parent::callView($sView, $sAction);
	}
	
}

?>