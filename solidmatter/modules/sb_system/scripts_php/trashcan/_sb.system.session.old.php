<?php

//------------------------------------------------------------------------------
/**
* This script holds the primary functions used by the framework.
* @package	solidBrickz
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------


//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function is_loggedin() {
	if (isset($_SESSION['system']['user_id'])) {
		return (TRUE);
	}
	return (FALSE);
}

function user_id() {
	return ($_SESSION['system']['user_id']);
}


//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function session_init() {
	
	
	
	
}





?>