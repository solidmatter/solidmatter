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
	
	//--------------------------------------------------------------------------
	/**
	* Initializes the object, replacing queries.
	*/
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadChildren']['byMode'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
	}
	
	//--------------------------------------------------------------------------
	/**
	* Overloads the default method.
	* Returns the last used view (list or thumbnail) if it was set before
	* @return string name of the default view
	*/
	protected function getDefaultViewName() {
		$sDefaultView = sbSession::getData('sbFiles:Folder:defaultView');
		if ($sDefaultView != NULL) {
			return ($sDefaultView);
		} else {
			return (parent::getDefaultView());
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Overloads the default method.
	* Saves the last used view (list or thumbnail) and calls the parent method
	* afterwards.
	* @param string the view to be called, or NULL if default should be called
	* @param string the action to be called, or NULL if default should be called
	* @return DOMElement the view element
	*/
	public function callView($sView = NULL, $sAction = NULL) {
		if ($sView == 'thumbnails' || $sView == 'list') {
			sbSession::addData('sbFiles:Folder:defaultView', $sView);
		}
		return (parent::callView($sView, $sAction));
	}
	
}

?>