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
class sbNode_user extends sbNode {
	
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbSystem/user/loadProperties/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbSystem/user/saveProperties/auxiliary';
	}
	
}

	

?>