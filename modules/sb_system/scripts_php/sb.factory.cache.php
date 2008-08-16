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
	public static function getInstance($sType) {
		
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

?>