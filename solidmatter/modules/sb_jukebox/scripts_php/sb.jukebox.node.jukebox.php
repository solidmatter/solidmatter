<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbNode_jukebox_jukebox extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* Overrides parent method to adjust view dependent on handler.
	* @return string 'properties' if in backend handler, otherwise the nodetype default
	*/
	protected function getDefaultView() {
		if ($_REQUEST->getHandler() == 'backend') {
			return ('properties');	
		} else {
			return (parent::getDefaultView());	
		}
	}
		
}

?>