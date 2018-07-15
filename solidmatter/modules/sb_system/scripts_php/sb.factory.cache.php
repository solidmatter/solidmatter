<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cache');

//------------------------------------------------------------------------------
/**
*/
class CacheFactory {
	
	private static $crSession = NULL;
	
	private static $aCacheTypes = array(
		
		// type => 				array(main class,	class, 					scope, 			prefix)
		
		// special caches
		'images' => 			array('special',	'ImageCache',			'workspace',	''),
		'authorisations' => 	array('special',	'AuthorisationCache',	'workspace',	''),
		
		// multipurpose caches
		'misc' => 				array('generic',	'DatabaseCache',		'global',		'MISC:'),
		'system' =>				array('generic',	'MemoryCache', 			'global',		'SYSTEM:'),
		'registry' =>			array('generic',	'SessionCache',			'session',		'REGISTRY:'),
		'paths' =>				array('generic',	'MemoryCache',			'workspace',	'PATHS:'),
		'repository' => 		array('generic',	'MemoryCache',			'repository',	'REPOSITORY:'),
		
	);
	
	private static $aCacheClasses = array(
		'ImageCache'			=> 'sb.cache.images',
		'AuthorisationCache'	=> 'sb.cache.authorisations',
		'DatabaseCache'			=> 'sb.cache.database',
		'MemoryCache'			=> 'sb.cache.memory',
		'SessionCache'			=> 'sb.cache.session',
		'APCCache'				=> 'sb.cache.apc',
	);
	
	private static $aCaches = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function setSession($crSession) {
		
		self::$crSession = $crSession;
		
		// purge existing session-dependent caches
		foreach (self::$aCacheTypes as $sCacheType => $aCacheDefinition) {
			if ($aCacheDefinition[0] == 'special') {
				self::$aCaches[$sCacheType] = NULL; 
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: implement clean cache scope usage
	* @param 
	* @return 
	*/
	public static function getInstance($sType) {
		
		if (!isset(self::$aCacheTypes[$sType])) {
			throw new sbException('type not recognized: '.$sType);
		}
		
		if (!isset(self::$aCaches[$sType])) {
			
			$sPrefix = self::$aCacheTypes[$sType][3];
			
			// determine class
			$sClass = self::$aCacheTypes[$sType][1];
			if ($sClass == 'MemoryCache') {
				if (function_exists('apc_store')) { // use apc if possible
					$sClass = 'APCCache';
				} else {
					$sClass = 'DatabaseCache';
				}
			}	
			$sLibrary = self::$aCacheClasses[$sClass];
			import($sLibrary);
			
			// treat special and generic caches differently
			if (self::$aCacheTypes[$sType][0] == 'special') {
				
				self::$aCaches[$sType] = new $sClass(self::$crSession);
				
			} else { // generic
				
				$sScope = self::$aCacheTypes[$sType][2];
				
				// prepare cache depending on scope
				switch ($sScope) {
					case 'global':
						$sPrefix .= 'G:';
						break;
					case 'repository':
						$sPrefix .= 'R['.self::$crSession->getRepository()->getID().']:';
						break;
					case 'workspace': // TODO: implement real 
						$sPrefix .= 'W['.self::$crSession->getRepository()->getID().':'.self::$crSession->getWorkspace()->getName().']:';
						break;
					case 'session':
						if ($sClass != 'SessionCache') { // TODO: for now, no prefix is needed with SessionCache
							$sPrefix .= 'S['.sbSession::getID().']:';
						}
						break;
					default:
						throw new CacheException('cache scope "'.$sScope.'" is unknown');
				}
				
				self::$aCaches[$sType] = new $sClass($sPrefix);
				
				// TODO: use repository-local db, then remove the repository-local db-connection again :(
				if ($sClass == 'DatabaseCache') {
					self::$aCaches[$sType]->setDatabase(System::getDatabase());
				}
				
			}
			
		}
		
		return (self::$aCaches[$sType]);
		
	}
	
}

?>