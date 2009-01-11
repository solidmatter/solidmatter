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
class sbNode_filemanager extends sbNode {
	
	protected function __replaceQueries() {
		$this->aQueries['loadChildren']['byMode'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
	}
	
}

	

?>