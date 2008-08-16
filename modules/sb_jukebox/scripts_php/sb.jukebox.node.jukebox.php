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
	
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['saveProperties']['auxiliary'] = 'sbJukebox/album/properties/save/auxiliary';
	}
	
}

?>