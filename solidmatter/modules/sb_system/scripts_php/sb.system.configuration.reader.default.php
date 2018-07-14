<?php

//------------------------------------------------------------------------------
/**
 *	@package solidMatter
 *	@author	()((() [Oliver Müller]
 *	@version 1.00.00
 */
//------------------------------------------------------------------------------

//-----------------------------------------------------------------------------
/**
 * 
 * 
 *
 */
class sbConfigurationReader {
		
	// container member variable for the coniguration as SimpleXMLElement
	static $CONFIGSXML = NULL;
	
	//-------------------------------------------------------------------------
	/**
	 * Initializes the CONFIG class, e.g. loading additional values from config files.
	 * Currently only loads the configuration XML at DIR.FILE and stores it.
	 */
	static function init() {
		self::$CONFIGSXML = simplexml_load_file(CONFIG::DIR.CONFIG::FILE);
	}
	
	//-------------------------------------------------------------------------
	/**
	 * Returns a site definition appropriate for the request path.
	 * The resulting array contains at least the following entries:
	 * 'sitelocation': the matched site as string
	 * 'site': SimpleXMLElement with the site or subsite definition
	 * 'controller': currently an additional reference of the matched site, which is used to get additional controller info
	 * TODO: needs to be changed to allow site definitions with 
	 * @param string the path to be matched
	 * @return array the site definition (for contents see description)
	 */
	static function getSiteConfig(string $sSitePath) {
		$aResult = array();
		if (!self::matchSite($sSitePath, $aResult)) {
			header('HTTP/1.1 500 Internal Server Error');
			die_fancy('The site in request "'.$sSitePath.'" is not defined.');
			throw new sbException('site '.$sSitePath.' is not defined');
		} else {
			return ($aResult);
		}
	}
	
	//-------------------------------------------------------------------------
	/**
	 * Matches a requested URI against all defined sites by iterating over all defined sites.
	 * Also checks for defined subsites that match a greater part of the requested site string
	 * TODO: make this a recursive operation, enabling deeply nested site definitions (needs modification to config handling)
	 * @param string the request path to be matched
	 * @param array the resulting array after iteration
	 * @return
	 */
	private static function matchSite(string $sSitePath, array &$aResult) : bool {
		
		$aResult['sitelocation'] = '';
		$aResult['site'] = NULL;
		$aResult['repository'] = NULL;
		
		// match site root
		foreach (self::$CONFIGSXML->xpath('sites/site') as $elemSite) {
			$sSiteLocation = (string) $elemSite['location'];
			if (substr_count($sSitePath, $sSiteLocation) > 0) {
				if (strlen($sSiteLocation) >= strlen($aResult['sitelocation'])) {
					$aResult['sitelocation'] = $sSiteLocation;
					$aResult['site'] = $elemSite;
					$aResult['controller'] = $elemSite;
					// check if site has subsites and match these
					foreach ($aResult['site']->xpath('subsite') as $elemSubSite) {
						$sSubSiteLocation = $sSiteLocation . (string) $elemSubSite['location'];
						if (substr_count($sSitePath, $sSubSiteLocation) > 0) {
							if (strlen($sSubSiteLocation) >= strlen($aResult['sitelocation'])) {
								$aResult['sitelocation'] = $sSubSiteLocation;
								$aResult['site'] = $elemSubSite;
							}
						}
					}
				}
			}
		}
		
		if (!isset($aResult['site'])) {
			return (FALSE);
		}
		return (TRUE);
		
	}
	
	//-------------------------------------------------------------------------
	/**
	 * Returns the definition of a handler.
	 * @param string ID of the handler
	 * @return SimpleXMLElement the handler definition (see configuration.xml)
	 */
	static function getHandlerConfig(string $sHandlerID) {
		if (!isset(self::$CONFIGSXML->handlers->$sHandlerID)) {
			throw new sbException('handler '.$sHandlerID.' could not be initialized');
		} else {
			return (self::$CONFIGSXML->handlers->$sHandlerID);
		}
	}
	
	//-------------------------------------------------------------------------
	/**
	 * Returns the definition of a repository.
	 * @param string ID of the repository
	 * @return SimpleXMLElement the repository definition (see configuration.xml)
	 */
	static function getRepositoryConfig(string $sRepositoryID) {
		if (!isset(self::$CONFIGSXML->repositories->$sRepositoryID)) {
			throw new sbException('repository '.$sRepositoryID.' not defined');
		} else {
			return (self::$CONFIGSXML->repositories->$sRepositoryID);
		}
	}
	
	//-------------------------------------------------------------------------
	/**
	 * Returns the definition of a database.
	 * @param string ID of the database
	 * @return SimpleXMLElement the database definition (see configuration.xml)
	 */
	static function getDatabaseConfig(string $sDatabaseID) {
		if (!isset(self::$CONFIGSXML->databases->$sDatabaseID)) {
			throw new sbException('database '.$sHandlerID.' not defined');
		} else {
			return (self::$CONFIGSXML->databases->$sDatabaseID);
		}
	}
	
}

// immediately initialize the configuration class
CONFIG::init();

?>