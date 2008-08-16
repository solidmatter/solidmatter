<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb_jukebox:sb.pdo.queries');

//------------------------------------------------------------------------------
/**
*/
class sbNode_jukebox_album extends sbNode {
	
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbJukebox/album/properties/load/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbJukebox/album/properties/save/auxiliary';
	}
	
}

?>