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
/*class CacheFactory {
	
	private static $cacheSystem = NULL;
	private static $cachePaths = NULL;
	private static $cacheRegistry = NULL;
	private static $cacheImages = NULL;
	private static $cacheAuthorisations = NULL;
	private static $cacheRepository = NULL;
	private static $cacheMisc = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	/*public static function getInstance($sType) {
		
		switch ($sType) {
			
			case 'system':
				if (self::$cacheSystem === NULL) {
					import('sb.cache.session');
					self::$cacheSystem = new SessionCache();
				}
				return (self::$cacheSystem);
				//break;
			
			case 'paths':
				if (self::$cachePaths === NULL) {
					import('sb.cache.paths');
					self::$cachePaths = new PathCache();
				}
				return (self::$cachePaths);
				//break;
			
			case 'registry':
				if (self::$cacheRegistry === NULL) {
					import('sb.cache.registry');
					self::$cacheRegistry = new RegistryCache();
				}
				return (self::$cacheRegistry);
				//break;
				
			case 'images':
				if (self::$cacheImages === NULL) {
					import('sb.cache.images');
					self::$cacheImages = new ImageCache();
				}
				return (self::$cacheImages);
				//break;
			
			case 'authorisations':
				if (self::$cacheAuthorisations === NULL) {
					import('sb.cache.authorisations');
					self::$cacheAuthorisations = new AuthorisationCache();
				}
				return (self::$cacheAuthorisations);
				//break;
				
			case 'repository':
				if (self::$cacheRepository === NULL) {
					import('sb.cache.repository');
					self::$cacheRepository = new RepositoryCache();
				}
				return (self::$cacheRepository);
				//break;
				
			case 'misc':
				if (self::$cacheMisc === NULL) {
					import('sb.cache.misc');
					self::$cacheMisc = new MiscCache();
				}
				return (self::$cacheMisc);
				//break;
				
			default:
				throw new sbException('type not recognized: '.$sType);
				break;
			
			
		}	
		
	}
	
}

//------------------------------------------------------------------------------
/**
*/
class CacheFactory {
	
	private static $crSession = NULL;
	
	private static $aCacheTypes = array(
		
		// type => 				array(main class,	class, 				scope, 			prefix)
		
		// special caches
		'images' => 			array('special',	'ImageCache',			'workspace',	''),
		'authorisations' => 	array('special',	'AuthorisationCache',	'workspace',	''),
		
		// multipurpose caches
		'paths' =>				array('generic',	'MemoryCache',			'workspace',	'PATHS:'),
		'system' =>				array('generic',	'MemoryCache', 			'global',		'SYSTEM:'),
		'registry' =>			array('generic',	'SessionCache',			'session',		'REGISTRY:'),
		'repository' => 		array('generic',	'MemoryCache',			'repository',	'REPOSITORY:'),
		'misc' => 				array('generic',	'DatabaseCache',		'global',		'MISC:'),
		
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
				
				self::$aCaches[$sType] = new $sClass();
				
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
						if ($sClass != 'SessionCache') { // TODO: for now, no prefis is needed with SessionCache
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