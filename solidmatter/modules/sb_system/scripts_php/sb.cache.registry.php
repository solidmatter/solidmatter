<?php

//------------------------------------------------------------------------------
/**
* @package solidMatter[sbSystem]
* @subpackage Cache
* @author	()((() [Oliver Müller]
* @version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cache.database');

//------------------------------------------------------------------------------
/** 
*/
class RegistryCache extends SessionCache implements sbCache {
	
	protected $sPrefix = 'REGISTRY:';

}



?>